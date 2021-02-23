<?php


namespace Zler\Wechat\Service\Impl;


use Zler\Wechat\Exception\AccessDeniedException;
use Zler\Wechat\Service\GzhService;
use Zler\Wechat\Toolkits\CurlToolkit;
use Zler\Wechat\Toolkits\StringToolkit;

class GzhServiceImpl implements GzhService
{
    protected $appId;
    protected $appSecret;
    protected $token;

    public function __construct($fields)
    {
        $this->appId = $fields['app_id'];
        $this->appSecret = $fields['app_secret'];
        $this->token = $fields['token'];
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

    public function generateOauth2Url($redirectUri, $state = "STATE")
    {
        $url = self::GENERATE_OAUTH2_URL;
        $url = sprintf($url, urlencode($redirectUri),$state);
        return $url;
    }

    public function getAccessToken()
    {
        $data = CurlToolkit::request('GET',sprintf(self::GET_ACCESS_TOKEN_URL, $this->appId, $this->appSecret));

        if(!isset($data['access_token'])){
            throw new AccessDeniedException("get openid accessToken fail");
        }

        return $data['access_token'];
    }

    public function getAccessTokenByCode($code)
    {
        $url = sprintf(self::OAUTH2_ACCESS_TOKEN_URL,$this->appId, $this->appSecret, $code);
        $data = CurlToolkit::request('GET', $url);

        if(!isset($data['access_token'])){
            throw new AccessDeniedException("get openid accessToken by code fail");
        }

        return $data['access_token'];
    }

    public function getJsTikcet()
    {
        $accessToken = $this->getAccessToKen();

        $url = sprintf(self::GET_JST_TICKET_URL, $accessToken);

        $data = CurlToolkit::request('GET', $url);
        if(!isset($data['errcode'])){
            throw new AccessDeniedException("get js tikcet fail no errcode");
        }

        if($data['errcode'] != 0){
            throw new AccessDeniedException("get js tikcet fail");
        }

        return $data['ticket'];
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

    public function getUserInfo($openId)
    {
        $accessToken = $this->getAccessToKen();
        $url = sprintf(self::GET_USER_INFO_URL, $accessToken, $openId);
        return CurlToolkit::request('GET', $url);
    }
}