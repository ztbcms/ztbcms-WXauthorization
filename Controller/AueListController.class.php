<?php

// +----------------------------------------------------------------------
// | URL规则管理
// +----------------------------------------------------------------------

namespace WXauthorization\Controller;

use Common\Controller\AdminBase;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

/**
 * 授权应用管理页面
 */
class AueListController extends AdminBase {
    /**
     * 授权的小程序列表
     */
    public function index() {
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
        $data = $this->ajax_content_list($category, $start_date, $end_date, $page, $limit, $message);
        //返回数据
        $this->ajaxReturn(self::createReturn(true, $data));
    }
    //获取数据
    public static function ajax_content_list($category = '', $start_date = '', $end_date = '', $page = 1, $limit = 20, $message = '',$authorizer_appid)
    {
        $db = M('wx_authorizer_access_token');
        $where = array();
        if(!empty($authorizer_appid)){
            $where['authorizer_appid'] = $authorizer_appid;
        }
        if (!empty($start_date) && !empty($end_date)) {
            $start_date = strtotime($start_date);
            $end_date = strtotime($end_date) + 24 * 60 * 60 - 1;
            $where['addtime'] = array(array('EGT', $start_date), array('ELT', $end_date), 'AND');
        }
        else {
            !empty($start_date) ? : $start_date = time();
            !empty($end_date) ? : $end_date = time() + 24 * 60 * 60 -1;
        }
        if (!empty($message)) {
            $where['message'] = array('LIKE', '%' . $message . '%');
        }
        $count = $db->where($where)->count();
        $total_page = ceil($count / $limit);
        $list = $db->where($where)->page($page)->limit($limit)->order(array("id" => "desc"))->select();
        $data = [
            'items' => $list,
            'page' => $page,
            'limit' => $limit,
            'total_page' => $total_page,
        ];
        return $data;
    }


}
