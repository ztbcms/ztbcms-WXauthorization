<?php

// +----------------------------------------------------------------------
// | URL规则管理
// +----------------------------------------------------------------------

namespace WXauthorization\Controller;

use Common\Controller\AdminBase;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class ManagementController extends AdminBase {

	//初始化
	protected function _initialize() {
        parent::_initialize();
        $eo = $_SERVER['QUERY_STRING'];
        $in1 = strstr($eo,'g=WXauthorization&m=Management&a=management');
        $in2 = strstr($eo,'g=WXauthorization&m=Management');
        $in3 = strstr($eo, 'g=WXauthorization&m=Management&a=authorizer_refresh_token');
        $in4 = strstr($eo,'g=WXauthorization&m=management&a=content_list');

        if($in1 != false || $in2 != false|| $in3 != false || $in4 != false){
        } else {
            $where['authorizer_appid'] = I('authorizer_appid');
            $res = M('wx_authorizer_access_token')->where($where)->find();
            if($res['expires_in'] - time() < 60){
                $this->success('你令牌授权需要重新授权',U('WXauthorization/Management/index'));
                exit;
            }
            $trilateraluser = M('wx_trilateraluser')->find();
            if($trilateraluser['trilateralaccess_token_time'] - time() < 60){
                $this->success('您的第三方授权需要重新授权',U('WXauthorization/index/index'));
                exit;
            }
        }
	}

	//信息列表
	public function index() {
	    $res = M('wx_authorizer_access_token')->order(array('id' => 'DESC'))->select();
		$this->assign('info', $res);
		$this->display();
	}

    public function management(){
        $authorizer_appid = I('authorizer_appid');
        $data = M('wx_authorizer_access_token')->where(array('authorizer_appid' =>$authorizer_appid ))->find();
        $authorizer_access_token = urlencode($data['authorizer_access_token']);
        $this->assign('authorizer_access_token',$authorizer_access_token);
        $this->assign('authorizer_appid',$authorizer_appid);
	    $this->display();
    }

    //刷新令牌
    public function authorizer_refresh_token(){
        $authorizer_appid = I('authorizer_appid');
        $where['authorizer_appid'] = $authorizer_appid;
        $authorizer_access_token = M('wx_authorizer_access_token')->where($where)->find();
        $trilateraluser = M('wx_trilateraluser')->find();
        $component_access_token = $trilateraluser['trilateralaccess_token'];
        $url ="https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=$component_access_token";
        $data['component_appid'] = $trilateraluser['trilateralappid'];
        $data['authorizer_appid'] = $authorizer_access_token['authorizer_appid'];
        $data['authorizer_refresh_token'] = $authorizer_access_token['authorizer_refresh_token'];
        $res = $this->post_data($url,$data);
        $up['authorizer_access_token'] = $res['authorizer_access_token'];
        $up['authorizer_refresh_token'] = $res['authorizer_refresh_token'];
        $up['expires_in'] = $res['expires_in'] +  time();
        $wk = M('wx_authorizer_access_token')->where($where)->save($up);
        if($wk > 0){
            $this->success('令牌刷新成功',U('WXauthorization/Management/index'));
        } else {
            $this->success('令牌刷新失败');
        }
    }

    //获取体验二维码
    public function getExpCode() {
        $appId = I('authorizer_appid');
        if ( empty( $appId ) ) {
            $this->success( appid不能为空 );
            return;
        }

        $map['authorizer_appid'] = $appId;
        $authorizer_access_token = M('wx_authorizer_access_token')->where($map)->find();
        $accessToken = $authorizer_access_token['authorizer_access_token'];
        if ( empty( $accessToken ) ) {
            $this->success( '获取授权accessToken错误' );
            return;
        }

        $params = array(
            'access_token' => $accessToken
        );
        $result = $this->buildRequestForm( $params, 'GET', 'https://api.weixin.qq.com/wxa/get_qrcode?access_token='.$accessToken, true );
        echo $result;
        exit;
    }

    //代码上传
    public function wx_upload_code(){
        if(IS_POST){
            $authorizer_appid = I('authorizer_appid');
            $configuration_name = I('configuration_name');
            $configuration_information = I('configuration_information');
            foreach ($configuration_name as $key => $val){
                $arr[$key]['configuration_name'] = $val['configuration_name'];
            }
            foreach ($configuration_information as $key => $val){
                $arr[$key]['configuration_information'] = $val;
            }
            foreach ($arr as $key => $val){
                if(empty($val['configuration_name']) || empty($val['configuration_information'])){
                    unset($arr[$key]);
                } else {
                    $arrd[$key][$val['configuration_name']] = $val['configuration_information'];
                }
            }

            $template_id = I('template_id');
            $ext_json = array(
                'extEnable' => true,
                'extAppid'  => I('template_ids'),
                'directCommit' => false,
                'ext' => array(
                    $arrd
                ),
                'extPages'=> array(),
                'window'=> array(),
                'tabBar' => array(),
                'networkTimeout' => array(
                    'request' => 10000,
                    'downloadFile' => 10000,
                )
            );
            $ext_json = json_encode($ext_json,JSON_UNESCAPED_UNICODE);
            if(empty($template_id)){
                $this->success('template_id不能为空',U('WXauthorization/Management/index'));
            }
            if(empty($ext_json)){
                $this->success('ext_json不能为空',U('WXauthorization/Management/index'));
            }
            $user_version = I('user_version');
            if(empty($user_version)){
                $this->success('user_version不能为空',U('WXauthorization/Management/index'));
            }
            $user_desc = I('user_desc');
            if(empty($user_desc)){
                $this->success('user_desc不能为空',U('WXauthorization/Management/index'));
            }
            $authorizer_access_token = M('wx_authorizer_access_token')->order('id desc')->find();
            $access_token = $authorizer_access_token['authorizer_access_token'];
            $url = "https://api.weixin.qq.com/wxa/commit?access_token=$access_token";
            $data['template_id'] = $template_id;
            $data['ext_json'] = $ext_json;
            $data['user_version'] = $user_version;
            $data['user_desc'] = $user_desc;
            $result = $this->post_data($url,$data);
            if($result['errcode'] == '42001'){
                $this->success('令牌超时请重新授权',U('WXauthorization/Management/index'));
            }
            if($result['errcode'] == '85014'){
                $this->success('该模板不存在',U('WXauthorization/Management/index'));
            }
            if($result['errcode'] == 0){
                $daws['addtime'] = time();
                $daws['template_id'] = $template_id;
                $daws['message'] = "成功上传模板".$template_id."代码版本为".$user_version;
                $daws['type'] = '1';
                $daws['authorizer_appid'] = $authorizer_appid;
                M('wx_submitcode')->add($daws);
                $this->success('代码上传成功',U('WXauthorization/Management/index'));
            } else {
                $this->success('代码上传失败');
            }
        } else {
            $this->assign('authorizer_appid',I('authorizer_appid'));
            $this->display();
        }
    }

    //将第三方提交的代码包提交审核
    public function submit_audit(){
        $authorizer_appid = I('authorizer_appid');
        $map['authorizer_appid'] = $authorizer_appid;
        $authorizer_access_token = M('wx_authorizer_access_token')->where($map)->find();
        $access_token = $authorizer_access_token['authorizer_access_token'];
        if(IS_POST){
            $postcontent = I('post.');
            $access_token = $postcontent['access_token'];
            foreach ($postcontent['address'] as $key => $val){
                $tpy[$key]['address'] = $val;
            }
            foreach ($postcontent['tag'] as $key => $val){
                $tpy[$key]['tag'] = $val;
            }
            foreach ($postcontent['first_class'] as $key => $val){
                $tpy[$key]['first_class'] = $val;
            }
            foreach ($postcontent['second_class'] as $key => $val){
                $tpy[$key]['second_class'] = $val;
            }
            foreach ($postcontent['third_class'] as $key => $val){
                $tpy[$key]['third_class'] = $val;
            }
            foreach ($postcontent['first_id'] as $key => $val){
                $tpy[$key]['first_id'] = $val;
            }
            foreach ($postcontent['second_id'] as $key => $val){
                $tpy[$key]['second_id'] = $val;
            }
            foreach ($postcontent['third_id'] as $key => $val){
                $tpy[$key]['third_id'] = $val;
            }
            foreach ($postcontent['title'] as $key => $val){
                $tpy[$key]['title'] = $val;
            }
            foreach ($tpy as $key => $value){
                if(empty($value['first_id']) || empty($value['first_class'])){
                    unset($tpy[$key]['first_id']);
                    unset($tpy[$key]['first_class']);
                }
                if(empty($value['second_class']) || empty($value['second_id'])){
                    unset($tpy[$key]['second_class']);
                    unset($tpy[$key]['second_id']);
                }
                if(empty($value['third_class']) || empty($value['third_id'])){
                    unset($tpy[$key]['third_class']);
                    unset($tpy[$key]['third_id']);
                }
            }
            $params = array(
                'item_list' => $tpy
            );
            $params = json_encode( $params, JSON_UNESCAPED_UNICODE );
            $result = $this->curl_post( 'https://api.weixin.qq.com/wxa/submit_audit?access_token='.$access_token,$params);
            if($result['errcode'] == '42001'){
                $this->success('令牌已超时，请重新授权');
            }
            if($result['errcode'] == '85010'){
                $this->success('item_list有项目为空');
            }
            if($result['errcode'] == '85009'){
                $this->success('已经有在审核的版本了');
            }
            if($result['errcode'] == '0'){
                $daws['auditid'] = $result['auditid'];
                $daws['addtime'] = time();
                $daws['status'] = $result['status'];
                $daws['type'] = '2';
                $daws['authorizer_appid'] = $authorizer_appid;
                M('wx_submitcode')->add($daws);
                $this->success('提交审核成功');
            }
        } else {
            //有的栏目
            $url = "https://api.weixin.qq.com/wxa/get_category?access_token=$access_token";
            $res = $this->get_data($url);
            $url1 = "https://api.weixin.qq.com/wxa/get_page?access_token=$access_token";
            $page =  $this->get_data($url1);
            $this->assign('page_list',$page['page_list']);
            $this->assign('category_list',$res['category_list']);
            $this->assign('access_token',$access_token);
            $this->display();
        }
    }

    //查询最新的审核状态
    public function get_latest(){
        $authorizer_appid = I('authorizer_appid');
        $map['authorizer_appid'] = $authorizer_appid;
        $authorizer_access_token = M('wx_authorizer_access_token')->where($map)->find();
        $access_token = $authorizer_access_token['authorizer_access_token'];
        $url = "https://api.weixin.qq.com/wxa/get_latest_auditstatus?access_token=$access_token";
        $res = $this->get_data($url);
        $where['auditid'] = $res['auditid'];
        $status['status'] = $res['status'];
        M('wx_submitcode')->where($where)->save($status);
        if($res['errcode'] == 0){
            if($res['status'] == '2'){
                $res['status'] = '审核中';
            }
            if($res['status'] == '0'){
                $res['status'] = '审核成功';
            }
            if($res['status'] == '1'){
                $res['status'] = '审核被拒绝';
            }
        }
        $this->ajaxReturn($res);
    }

    //查询某个版本的信息
    public function query_auditid(){
        $auditid = I('text');
        $authorizer_appid = I('authorizer_appid');
        $map['authorizer_appid'] = $authorizer_appid;
        $authorizer_access_token = M('wx_authorizer_access_token')->where($map)->find();
        $access_token = $authorizer_access_token['authorizer_access_token'];
        $url = "https://api.weixin.qq.com/wxa/get_auditstatus?access_token=$access_token";
        $data['auditid'] = $auditid;
        $data = json_encode( $data, JSON_UNESCAPED_UNICODE );
        $result = $this->curl_post($url,$data);
        if($result['errcode'] == 0){
            if($result['status'] == '2'){
                $result['status'] = '审核中';
            }
            if($result['status'] == '0'){
                $result['status'] = '审核成功';
            }
            if($result['status'] == '1'){
                $result['status'] = '审核被拒绝';
            }
        }
        $this->ajaxReturn($result);
    }

    //发布已通过审核的
    public function release_program(){
        $authorizer_appid = I('authorizer_appid');
        $map['authorizer_appid'] = $authorizer_appid;
        $authorizer_access_token = M('wx_authorizer_access_token')->where($map)->find();
        $access_token = $authorizer_access_token['authorizer_access_token'];
        $result = $this->curl_post( 'https://api.weixin.qq.com/wxa/release?access_token='.$access_token, '{}' );
        if($result['errcode'] == '85020'){
            $result['errmsg'] = '审核状态未满足发布';
        }
        $this->ajaxReturn($result);
    }

    public function content_list() {
        //按类别搜索时的类别关键字
        $category = I('category');
        //设置时间范围，从 $start_date 到 $end_date
        $start_date = I('start_date');
        $end_date = I('end_date');
        //指定获取分页结果的第几页
        $page = I('page', 1);
        $limit = I('limit', 20);
        //按内容搜索时的日志内容关键字
        $message = I('message');
        $authorizer_appid = I('authorizer_appid');
        $data = $this->ajax_content_list($category, $start_date, $end_date, $page, $limit, $message,$authorizer_appid);
        //返回数据
        $this->ajaxReturn(self::createReturn(true, $data));
    }

    //列表
    public static function ajax_content_list($category = '', $start_date = '', $end_date = '', $page = 1, $limit = 20, $message = '',$authorizer_appid)
    {
        $db = M('wx_submitcode');
        //初始化条件数组
        $where = array();
        if(!empty($authorizer_appid)){
            $where['authorizer_appid'] = $authorizer_appid;
        }
        if (!empty($start_date) && !empty($end_date)) {
            //将输入的起始和结束时间转换成时间戳
            $start_date = strtotime($start_date);
            //这里是下面的计算是因为单单转换"结束日期"为时间戳的话，并不会包括"结束日期"的那一天
            $end_date = strtotime($end_date) + 24 * 60 * 60 - 1;
            //'EGT'是大于，'ELT'是小于
            $where['addtime'] = array(array('EGT', $start_date), array('ELT', $end_date), 'AND');
        }
        else {
            //如果是起始日期为空的话，那么给其默认为今天
            !empty($start_date) ? : $start_date = time();
            !empty($end_date) ? : $end_date = time() + 24 * 60 * 60 -1;
        }
        if (!empty($message)) {
            $where['message'] = array('LIKE', '%' . $message . '%');
        }
        //获取总记录数
        $count = $db->where($where)->count();
        //总页数
        $total_page = ceil($count / $limit);
        //获取到的分页数据
        $Logs = $db->where($where)->page($page)->limit($limit)->order(array("id" => "desc"))->select();
        foreach ($Logs as $key => $val){
            if($val['type'] == '1'){
                $Logs[$key]['auditid'] = $val['template_id'];
                $Logs[$key]['status'] = $val['message'];
            }
            if($val['type'] == '2'){
                if($val['status'] == '2'){
                    $Logs[$key]['status'] = '审核中';
                }
                if($val['status'] == '1'){
                    $Logs[$key]['status'] = '被拒绝';
                }
                if($val['status'] == '0'){
                    $Logs[$key]['status'] = '已同意';
                }
            }
        }
        $data = [
            'items' => $Logs,
            'page' => $page,
            'limit' => $limit,
            'total_page' => $total_page,
        ];
        return $data;
    }

    //版本回退
    public function versionBack(){
        $authorizer_appid = I('authorizer_appid');
        $map['authorizer_appid'] = $authorizer_appid;
        $authorizer_access_token = M('wx_authorizer_access_token')->where($map)->find();
        $access_token = $authorizer_access_token['authorizer_access_token'];
        $url = "https://api.weixin.qq.com/wxa/revertcoderelease?access_token=$access_token";
        $result = $this->get_data($url);
        if($result['errcode'] == '0'){
            $daws['type'] = '1';
            $daws['message'] = '进行版本回滚成功';
            $daws['authorizer_appid'] = $authorizer_appid;
            M('wx_submitcode')->add();
        }
        if($result['errcode'] == '87011'){
            $result['errmsg'] = '现网已经在灰度发布，不能进行版本回退';
        }
        if($result['errcode'] == '87012'){
            $result['errmsg'] = '该版本不能回退，可能的原因：1:无上一个线上版用于回退 2:此版本为已回退版本，不能回退 3:此版本为回退功能上线之前的版本，不能回退';
        }
        $this->ajaxReturn($result);
    }

    public function post_data($url,$data){
        $data = json_encode( $data );
        $ch = curl_init(); //用curl发送数据给api
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
        $response = curl_exec( $ch );
        curl_close( $ch );
        $result = json_decode( $response, true );
        return $result;
    }

    public function get_data($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        curl_close($curl);
        $data = strstr($data,'{');
        $arr = json_decode($data,true);
        return $arr;
    }

    protected function curl_post( $curlHttp, $postdata ) {
        $ch = curl_init(); //用curl发送数据给api
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_URL, $curlHttp );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $postdata );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );

        $response = curl_exec( $ch );
        curl_close( $ch );
        $result = json_decode( $response, true );
        return $result;
    }

    protected function buildRequestForm( array $param, $method, $target='',$jump=false) {
        $sHtml = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' /><form id='autoSubmit' action='".$target."' method='".$method."'>";

        if ( !empty( $param ) ) {
            foreach( $param as $key => $value ) {
                $sHtml.= "<input type='hidden' name='".$key."' value='".urldecode($value)."'/>";
            }
        }
        $sHtml .= "</form>";

        if($jump) $sHtml = $sHtml."<script>document.getElementById(\"autoSubmit\").submit();</script>";

        return $sHtml;
    }
}
