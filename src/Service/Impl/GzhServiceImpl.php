<?php


namespace Zler\Wechat\Service\Impl;

use Symfony\Component\Filesystem\Filesystem;
use Zler\Wechat\Exception\AccessDeniedException;
use Zler\Wechat\Service\GzhService;
use Zler\Wechat\Toolkits\CurlToolkit;
use Zler\Wechat\Toolkits\StringToolkit;

class GzhServiceImpl implements GzhService
{
    private $appId;
    private $appSecret;
    private $token;
    private $accessTokenPath;
    private $jsTicketPath;
    private $resolveMessages = array();

    public function __construct($fields)
    {
        $this->appId                = $fields['app_id'];
        $this->appSecret            = $fields['app_secret'];
        $this->token                = $fields['token'];
        $this->accessTokenPath      = $fields['access_token_path'];
        $this->jsTicketPath         = $fields['js_ticket_path'];
        $this->resolveMessages      = array();
    }

    public function getResolveMessages()
    {
        return $this->resolveMessages;
    }

    public function checkSignature($fields)
    {
        $tmpArr = array($this->token, $fields['timestamp'], $fields['nonce']);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if($tmpStr == $fields['signature'] ){
            return true;
        }else{
            return false;
        }
    }

    public function getAccessToken()
    {
        $data = json_decode(file_get_contents($this->accessTokenPath), true);

        if($data['expires_in'] < time()) {
            $wx = CurlToolkit::request('GET',
                                sprintf(self::GET_ACCESS_TOKEN_URL, $this->appId, $this->appSecret),
                                array());

            if (!isset($wx['access_token'])) {
                throw new AccessDeniedException("get openid accessToken fail");
            }

            $fileData['expires_in'] = time() + $wx['expires_in'];
            $fileData['access_token'] = $wx['access_token'];

            $filesystem = new Filesystem();
            $filesystem->dumpFile($this->accessTokenPath, json_encode($fileData));

            return $wx['access_token'];
        }else{
            return $data['access_token'];
        }
    }

    public function getUserInfo($openId)
    {
        $accessToken = $this->getAccessToKen();

        $url = sprintf(self::GET_USER_INFO_URL, $accessToken, $openId);

        return CurlToolkit::request('GET', $url, array());
    }

    public function generateOauth2Url($redirectUri, $state = "STATE")
    {
        $url = self::GENERATE_OAUTH2_URL;
        $url = sprintf($url, urlencode($redirectUri), $state);
        return $url;
    }

    public function getUserInfoByOauth2Code($code)
    {
        $oauth2AccessTokenAndOpenId = $this->getOauth2AccessTokenAndOpenIdByCode($code);

        return $this->getUserInfoByAccessTokenAndOpenId($oauth2AccessTokenAndOpenId['access_token'], $oauth2AccessTokenAndOpenId['openid']);
    }

    private function getOauth2AccessTokenAndOpenIdByCode($code)
    {
        $url = sprintf(self::OAUTH2_ACCESS_TOKEN_URL,$this->appId, $this->appSecret, $code);

        $data = CurlToolkit::request('GET', $url, array());

        if(!isset($data['access_token']) || !isset($data['openid'])){
            throw new AccessDeniedException("get accessToken|openid by code fail");
        }

        return $data;
    }

    private function getUserInfoByAccessTokenAndOpenId($accessToken, $openId)
    {
        $url = sprintf(self::OAUTH2_USER_INFO_URL, $accessToken, $openId);

        $data = CurlToolkit::request('GET', $url, array());

        if(!isset($data['nickname'])){
            throw new AccessDeniedException("get user info by oauth2 fail");
        }

        return $data;
    }

    public function getJsTikcet()
    {
        $data = json_decode(file_get_contents($this->jsTicketPath), true);

        if($data['expires_in'] < time()) {
            $accessToken = $this->getAccessToKen();

            $url = sprintf(self::GET_JST_TICKET_URL, $accessToken);
            $wx = CurlToolkit::request('GET', $url, array());

            if(!isset($wx['errcode'])){
                throw new AccessDeniedException("get js tikcet fail no errcode");
            }

            if($wx['errcode'] != 0){
                throw new AccessDeniedException("get js tikcet fail");
            }

            $fileData['expires_in'] = time() + $wx['expires_in'];
            $fileData['ticket'] = $wx['ticket'];

            $filesystem = new Filesystem();
            $filesystem->dumpFile($this->jsTicketPath, json_encode($fileData));

            return $wx['ticket'];
        }else{
            return $data['ticket'];
        }
    }

    public function getJsSdkParams($fields)
    {
        $ticket = $this->getJsTikcet();
        $url = urldecode($fields['url']);
        $timestamp = time();
        $nonceStr = StringToolkit::createRandomString(16);
        $string = sprintf("jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s", $ticket, $nonceStr, $timestamp, $url);
        $signature = sha1($string);
        return array(
            'appId'     => $this->appId,
            'timestamp' => $timestamp,
            'nonceStr'  => $nonceStr,
            'signature' => $signature,
        );
    }

    public function generateSceneValueQrCode($sceneValue, $returnUrlAddress = true, $actionName = 'QR_LIMIT_STR_SCENE')
    {
        if( !in_array($actionName, array('QR_LIMIT_STR_SCENE', 'QR_STR_SCENE')) ){
            throw new AccessDeniedException('invalid action name');
        }

        $accessToken = $this->getAccessToken();

        $url = sprintf(self::GENERATE_SCENE_VALUE_QR_CODE_URL, $accessToken);

        $data = sprintf(self::GENERATE_SCENE_VALUE_QR_CODE_TEMPLATE, $actionName, $sceneValue);

        $data = CurlToolkit::request('POST', $url, $data);

        if(!isset($data['ticket'])){
            throw new AccessDeniedException("generate scene value QrCode ticket fail");
        }

        if($returnUrlAddress){
            return sprintf(self::SHOW_QR_CODE_URL, urldecode($data['ticket']));
        }else{
            return $data['ticket'];
        }
    }

    public function resolveMessage()
    {
        $context = isset($GLOBALS['HTTP_RAW_POST_DATA']) ?
            $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        $context = simplexml_load_string($context);

        return $this->resolveMessages = array(
            //公有部分
            'ToUserName' => $context->ToUserName,   // 开发者微信号
            'FromUserName' => $context->FromUserName, // 发送方帐号（一个OpenID）
            'CreateTime' => $context->CreateTime,
            'MsgType' => trim($context->MsgType),
            //私有部分
            'Content' => isset($context->Content)?trim($context->Content):null,//文本消息内容
            'MsgId' => isset($context->MsgId)?$context->MsgId:null, //消息id，64位整型
            'PicUrl' => isset($context->PicUrl)?$context->PicUrl:null, //图片链接（由系统生成)
            'MediaId' => isset($context->MediaId)?$context->MediaId:null, //消息媒体id
            'Format' => isset($context->Format)?$context->Format:null, //语音格式
            'Event' => isset($context->Event)?$context->Event:null,//事件类型
            'EventKey' => isset($context->EventKey)?$context->EventKey:null,//事件KEY值
            'Ticket' => isset($context->Ticket)?$context->Ticket:null,//二维码的ticket，可用来换取二维码图片
        );
    }

    public function hasResolveSubscribeEvent()
    {
        $message = $this->getResolveMessages();

        if($message['MsgType'] === 'event' && $message['Event'] === 'subscribe'){
            return $message;
        }else{
            return array();
        }
    }

    public function hasResolveUnSubscribeEvent()
    {
        $message = $this->getResolveMessages();

        if($message['MsgType'] == 'event' && $message['Event'] == 'unsubscribe'){
            return $message;
        }else{
            return false;
        }
    }

    public function hasResolveScanUnsubscribedEvent()
    {
        $message = $this->hasResolveSubscribeEvent();

        if(!$message){ return $message; }

        if( ($pos = stripos($message['EventKey'], 'qrscene_')) == 0 && !$message['Ticket']){
            $key = explode('_', $message['EventKey']);
            $message['scene_value'] = $key[1];
            return $message;
        }else{
            return false;
        }
    }

    public function hasResolveScanSubscribedEvent()
    {
        $message = $this->getResolveMessages();

        if($message['MsgType'] == 'event' && $message['Event'] == 'SCAN'
            && !$message['EventKey'] && !$message['Ticket']){
            $message['scene_value'] = $message['EventKey'];

            return $message;
        }else{
            return false;
        }
    }

    public function createTextMessage($fromUsername, $toUsername, $context)
    {
        $template = <<<EOF
            <xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[%s]]></Content>
            </xml>
        EOF;

        return sprintf($template, $toUsername, $fromUsername, time(), $context);
    }

    public function createMenu($menu)
    {
        $accessToken = $this->getAccessToKen();

        $url = sprintf(self::CREATE_MENU_URL, $accessToken);

        return CurlToolkit::request('POST', $url, $menu);
    }
}