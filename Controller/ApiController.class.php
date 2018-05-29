<?php

// +----------------------------------------------------------------------
// | Author: Jayin Ton <tonjayin@gmail.com>
// +----------------------------------------------------------------------

namespace WXauthorization\Controller;

use Common\Controller\Base;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class ApiController extends Base {

    public function index(){
        include_once  dirname(__FILE__). '/wxapp/WXBizMsgCrypt.php';
        $trilateraluser = M('wx_trilateraluser')->find();
//            $encodingAesKey = 'e456bacc1a47f55549a2d70ee7cd3b00e456bacc1a4';
//            $token = 'zhutibang';
//            $appId = 'wxf6cc10a2bedab991';
        $encodingAesKey = $trilateraluser['trilateralkey'];
        $token = $trilateraluser['trilateraltoken'];
        $appId = $trilateraluser['trilateralappid'];
        $timeStamp  = empty($_GET['timestamp'])     ? ""    : trim($_GET['timestamp']) ;
        $nonce      = empty($_GET['nonce'])     ? ""    : trim($_GET['nonce']) ;
        $msg_sign   = empty($_GET['msg_signature']) ? ""    : trim($_GET['msg_signature']) ;
        //接收XML数据
        $encryptMsg = file_get_contents('php://input');
        $pc = new \WXBizMsgCrypt($token, $encodingAesKey, $appId);
        $xml_tree = new \DOMDocument();
        $xml_tree->loadXML($encryptMsg);
        $array_e = $xml_tree->getElementsByTagName('Encrypt');
        $encrypt = $array_e->item(0)->nodeValue;
        $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
        $from_xml = sprintf($format, $encrypt);
        $data['timeStamp'] = $timeStamp;
        $data['nonce'] = $nonce;
        $data['msg_sign'] = $msg_sign;
        $data['from_xml'] = $from_xml;
        $data['message'] = serialize($data);
        $res = M('wx_verify_ticket')->add($data);
        $msg = '';
        $errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $msg);
        $component_verify_ticket =""; //解密成功
        if ($errCode == 0) {
            $xml = new \DOMDocument();
            $xml->loadXML($msg);
            $array_e = $xml->getElementsByTagName('ComponentVerifyTicket');
            $component_verify_ticket = $array_e->item(0)->nodeValue;
            $map['id'] = $res;
            $ed['verify_ticket'] = $component_verify_ticket;

            $wf['trilateralVerify_ticket'] = $component_verify_ticket;
            M('wx_verify_ticket')->where($map)->save($ed);
            M('wx_trilateraluser')->where(array('trilateralAppID'=>$trilateraluser['trilateralappid']))->save($wf);
            echo "success";
        }
        else {
//            $myfile = fopen("file/receiveTicket.txt", "w");
//            $array=array("errCode"=>$errCode,"component_verify_ticket"=>$component_verify_ticket);
//            fwrite($myfile, json_encode($array));
//            fclose($myfile);
            echo "false";
        }
    }

    //第三方授权的基础信息
    public function component_detail(){
        $trilateraluser = M('wx_trilateraluser')->order('id desc')->find();
        $res['component_appid'] = $trilateraluser['trilateralappid'];
        $res['component_appsecret'] = $trilateraluser['trilateralappsecret'];
        $res['component_verify_ticket'] = $trilateraluser['trilateralverify_ticket'];
        $res['token_time'] = $trilateraluser['trilateralAccess_token'];
        $res['component_access_token'] = $trilateraluser['component_access_token'];
        return $res;
    }

    //授权后保存信息
    public function wx_parties_callback(){
        $tok = $this->component_detail();
        $component_access_token = $tok['component_access_token'];
        $auth_code  = empty($_GET['auth_code'])     ? ""    : trim($_GET['auth_code']) ;
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token='.$component_access_token;
        $data['component_appid'] =  $tok['component_appid'];
        $data['authorization_code'] = $auth_code;
        $result = $this->post_data($url,$data);
        //获取公司授权信息
        $url2 = "https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=$component_access_token";
        $data['component_appid'] = $tok['component_appid'];
        $data['authorizer_appid'] = $result['authorization_info']['authorizer_appid'];
        $res = $this->post_data($url2,$data);
        $up['authorizer_access_token'] = $result['authorization_info']['authorizer_access_token'];
        $up['authorizer_refresh_token'] = $result['authorization_info']['authorizer_refresh_token'];
        $up['authorizer_appid'] = $result['authorization_info']['authorizer_appid'];
        $up['expires_in'] = $result['expires_in'] + time();
        $up['text'] = serialize($result);
        $up['auth_code'] = $auth_code;
        $up['authorizer_name'] = $res['authorizer_info']['nick_name'];
        $up['authorizer_qrcode_url'] = $res['authorizer_info']['qrcode_url'];
        $up['authorizer_signature'] = $res['authorizer_info']['signature'];
        $up['text2'] = serialize($res);

        $where['authorizer_appid'] = $result['authorization_info']['authorizer_appid'];
        $authorizer_appid = M('wx_authorizer_access_token')->where($where)->find();
        if($authorizer_appid > 0){
            $map['id'] = $authorizer_appid['id'];
            M('wx_authorizer_access_token')->where($map)->save($up);
        } else {
            M('wx_authorizer_access_token')->add($up);
        }
        header('Location:/index.php?g=WXauthorization&m=Management&a=management&authorizer_appid='.$result['authorization_info']['authorizer_appid']);
    }

    //获取回调
    public function messageUrl(){

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