<?php

// +----------------------------------------------------------------------
// | Author: Jayin Ton <tonjayin@gmail.com>
// +----------------------------------------------------------------------

namespace WXauthorization\Controller;

use Common\Controller\AdminBase;

class IndexController extends AdminBase {

    //初始化
    protected function _initialize() {
        parent::_initialize();
        $eo = $_SERVER['QUERY_STRING'];
        $in1 = strstr($eo,'&m=index');
        if($in1 != false ){
            $trilateraluser = M('wx_trilateraluser')->find();
            if($trilateraluser['trilateralaccess_token_time'] - time() < 60){
                $this->success('您的第三方授权需要重新授权',U('WXauthorization/index/index'));
                exit;
            }
        }
    }

    public function index() {
        if(IS_POST){
            $data = I('post.');
            if(!empty($data)){
                $res = M('wx_trilateraluser')->find();
                $trilateral['trilateralAppID'] = $data['trilateralAppID'];
                $trilateral['trilateralAppSecret'] = $data['trilateralAppSecret'];
                $trilateral['trilateralToken'] = $data['trilateralToken'];
                $trilateral['trilateralKey'] = $data['trilateralKey'];
                $trilateral['trilateralName'] = $data['trilateralName'];
                if(empty($res)){
                   $key = M('wx_trilateraluser')->add($trilateral);
                } else {
                    $map['id']  = $res['id'];
                    $key = M('wx_trilateraluser')->where($map)->save($trilateral);
                }
                if($key > 0){
                    $this->error('资料上传成功，请确保资料正确否则无法实现');
                } else {
                    $this->error('资料上传失败');
                }
            }
        } else {
            $data = M('wx_trilateraluser')->find();
            if($data['trilateralaccess_token_time'] > time()){
                $data['trilateralaccess_token_time'] = 1;
            } else {
                $data['trilateralaccess_token_time'] = 0;
            }

            if($data['trilateralauth_code_time'] > time()){
                $data['trilateralauth_code_time'] = 1;
            } else {
                $data['trilateralauth_code_time'] = 0;
            }
            $this->assign("data", $data);
            $this->display();
        }
    }
    //草稿箱
    public function draft(){
        $data = M('wx_access_token')->order(array('id' => 'desc'))->find();
        $component_access_token = $data['component_access_token'];
        $url = "https://api.weixin.qq.com/wxa/gettemplatedraftlist?access_token=$component_access_token";
        $data['access_token'] = $component_access_token;
        $res = $this->post_data($url,$data);
        foreach ($res['draft_list'] as $key => $val){
            $res['draft_list'][$key]['create_time'] = date("Y-m-d H;I:s",$val['create_time']);
        }
        $this->assign('draft_list',$res['draft_list']);
        $this->display();
    }
    //模板库
    public function library(){
        $data = M('wx_access_token')->order(array('id' => 'desc'))->find();
        $component_access_token = $data['component_access_token'];
        $url = "https://api.weixin.qq.com/wxa/gettemplatelist?access_token=$component_access_token";
        $res = $this->post_data($url,$data);
        foreach ($res['template_list'] as $key => $val){
            $res['template_list'][$key]['create_time'] = date("Y-m-d H;I:s",$val['create_time']);
        }
        $this->assign('template_list',$res['template_list']);
        $this->display();
    }
    //提交模板库
    public function post_draft(){
        $access_token = M('wx_access_token')->order(array('id' => 'desc'))->find();
        $component_access_token = urlencode($access_token['component_access_token']);
        $data['draft_id'] = I('draft_id','intval'); //草稿id
        $url = "https://api.weixin.qq.com/wxa/addtotemplate?access_token=$component_access_token";
        $res = $this->post_data($url,$data);
        if($res['errcode'] == 0){
            $this->error('添加成功');
        } else {
            $this->error('添加失败');
        }
    }

    //删除小程序代码模板
    public function delect_library(){
        $access_token = M('wx_access_token')->order(array('id' => 'desc'))->find();
        $component_access_token = $access_token['component_access_token'];
        $url = "https://api.weixin.qq.com/wxa/deletetemplate?access_token=$component_access_token";
        $data['template_id'] = I('template_id','intval'); //模板id
        $res = $this->post_data($url,$data);
        if($res['errcode'] == 0){
            $this->error('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    public function management(){
        $authorizer_appid = I('authorizer_appid');
        $where['authorizer_appid'] = $authorizer_appid;
        $authorizer_access_token = M('wx_authorizer_access_token')->where($where)->order('id desc')->find();
        $this->assign("authorizer_access_token",$authorizer_access_token['authorizer_access_token']);
        $this->assign('authorizer_appid',$authorizer_appid);
        $this->display();
    }

    //代码上传
    public function management_upload(){
        $authorizer_appid = I('authorizer_appid');
        $this->assign('authorizer_appid',$authorizer_appid);
        $this->display();
    }

    //第三方授权的基础信息
    public function component_detail(){
        $trilateraluser = M('wx_trilateraluser')->order('id desc')->find();
//        $res['component_appid'] = 'wxf6cc10a2bedab991';
//        $res['component_appsecret'] = 'd0b7784583a4257525fec882d4408f6a';
//        $res['component_verify_ticket'] = 'ticket@@@5x65xen_RnlQL4IqyXYr1dEMnqtKukS0342TUv3ftTqHcpoRGBbr1KeQ58GVCCukvLzaiH3WloEgfsn0tQJtuA';
        $res['component_appid'] = $trilateraluser['trilateralappid'];
        $res['component_appsecret'] = $trilateraluser['trilateralappsecret'];
        $res['component_verify_ticket'] = $trilateraluser['trilateralverify_ticket'];
        $res['token_time'] = $trilateraluser['trilateralAccess_token'];
        $res['component_access_token'] = $trilateraluser['component_access_token'];
        return $res;
    }

    //第三方授权
    public function get_component_access_token(){
        $res = $this->component_detail();   //获取第三方平台基础信息
        $last_time = $res['token_time'];	//上一次component_access_token获取时间
        $component_access_token = $res['component_access_token']; //获取数据查询到的component_access_token
        $component_appid = $res['component_appid'];
        $difference_time = $this->validity($last_time);//上一次获取时间与当前时间的时间差
        //判断component_access_token是否为空或者是否超过有效期
        if(empty($component_access_token) || $difference_time > 0){
            $component_access_token = $this->get_component_access_token_again();
        }
        $pre_auth_code = $this->component_access_token($component_access_token);
        $url = urlencode("http://auth.wxapp.yidian168.cn/index.php?g=WXauthorization&m=Api&a=wx_parties_callback");
        $authorization = "https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=$component_appid&pre_auth_code=$pre_auth_code&redirect_uri=$url";
        $resd = M('wx_trilateraluser')->find();
        if($resd > 0){
            $where['id'] = $resd['id'];
            $desd['trilateralUrl'] =  $authorization;
            $trilateralurl = M('wx_trilateraluser')->where($where)->save($desd);
        }
        if($trilateralurl > 0){
            $this->error('授权成功');
        } else {
            $this->error('授权失败');
        }
    }

    //获取时间差
    public function validity($time){
        $current_time = time();
        $difference_time = $time - $current_time  ;
        return $difference_time;
    }

    //重新获取component_access_token
    public function get_component_access_token_again(){
        $tok = $this->component_detail();
        $url = "https://api.weixin.qq.com/cgi-bin/component/api_component_token";
        $data = array(
            'component_appid' => $tok['component_appid'],
            'component_appsecret' => $tok['component_appsecret'],
            'component_verify_ticket' => $tok['component_verify_ticket'],
        );
        $result = $this->post_data($url,$data);
        $data['component_access_token'] = $result['component_access_token'];
        $data['token_time'] = $result['expires_in'] + time();
        M('wx_access_token')->add($data);
        $data2['trilateralAccess_token'] = $result['component_access_token'];
        $data2['trilateralAccess_token_time'] = $result['expires_in'] + time();
        $map['trilateralAppID'] = $tok['component_appid'];
        M('wx_trilateraluser')->where($map)->save($data2);
        return $result['component_access_token'];
    }

    //获取预授权码
    public function component_access_token($component_access_token){
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=' . $component_access_token;
        $tok = $this->component_detail();
        $data = array(
            'component_appid' => $tok['component_appid'],
        );
        $result = $this->post_data($url,$data);
        $data['pre_auth_code'] = $result['pre_auth_code'];
        $data['time'] = $result['expires_in']+time();
        M('wx_auth_code')->add($data);
        $data2['trilateralAuth_code'] = $result['pre_auth_code'];
        $data2['trilateralAuth_code_time'] = $result['expires_in']+time();
        $map['trilateralAppID'] = $tok['component_appid'];
        M('wx_trilateraluser')->where($map)->save($data2);
        return $result['pre_auth_code'];
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
}