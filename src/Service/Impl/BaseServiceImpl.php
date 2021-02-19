<?php

namespace Zler\Wechat\Service\Impl;

use Zler\Wechat\Exception\AccessDeniedException;
use Zler\Wechat\Service\BaseService;
use Zler\Wechat\Toolkits\CurlToolkit;

class BaseServiceImpl implements BaseService
{
    private $appId;
    private $appSecret;
    private $token;

    public function __construct($appId, $appSecret, $token)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->token = $token;
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

    public function getAccessToken($code)
    {
        $data = CurlToolkit::request(sprintf(self::GET_ACCESS_TOKEN_URL, $this->appId, $this->appSecret));

        if(!isset($data['access_token'])){
            throw new AccessDeniedException("get access token fail");
        }

        return $data['access_token'];
    }

    public function getUserInfo($accessToken, $openId)
    {
        $url = sprintf(self::USER_INFO, $accessToken, $openId);
        return CurlToolkit::request('GET', $url);
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

    }

}