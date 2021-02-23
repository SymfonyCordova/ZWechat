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
    protected $oauth2RedirectUrl;

    public function __construct($fields)
    {
        $this->appId                = $fields['app_id'];
        $this->appSecret            = $fields['app_secret'];
        $this->token                = $fields['token'];
        $this->oauth2RedirectUrl    = $fields['oauth2_redirect_url'];
    }

    public function getOauth2RedirectUrl()
    {
        return $this->oauth2RedirectUrl;
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
        $data = CurlToolkit::request('GET',sprintf(self::GET_ACCESS_TOKEN_URL, $this->appId, $this->appSecret));

        if(!isset($data['access_token'])){
            throw new AccessDeniedException("get openid accessToken fail");
        }

        return $data['access_token'];
    }

    public function getUserInfo($openId)
    {
        $accessToken = $this->getAccessToKen();
        $url = sprintf(self::GET_USER_INFO_URL, $accessToken, $openId);
        return CurlToolkit::request('GET', $url);
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
        $data = CurlToolkit::request('GET', $url);

        if(!isset($data['access_token']) || !isset($data['openid'])){
            throw new AccessDeniedException("get accessToken|openid by code fail");
        }

        return $data;
    }

    private function getUserInfoByAccessTokenAndOpenId($accessToken, $openId)
    {
        $url = sprintf(self::OAUTH2_USER_INFO_URL, $accessToken, $openId);
        $data = CurlToolkit::request('GET', $url);

        if(!isset($data['nickname'])){
            throw new AccessDeniedException("get user info by oauth2 fail");
        }

        return $data;
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
}