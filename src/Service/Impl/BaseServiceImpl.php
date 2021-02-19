<?php

namespace Zler\Wechat\Service\Impl;

use Zler\Wechat\Service\BaseService;
use Zler\Wechat\Toolkits\CurlToolkit;

class BaseServiceImpl implements BaseService
{
    private $appId;
    private $appSecret;

    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    public function getAccessToken($code)
    {
        $url = sprintf(self::ACCESS_TOKEN, $this->appId, $this->appSecret, $code);
        return CurlToolkit::request('GET', $url);
    }

    public function getUserInfo($accessToken, $openId)
    {
        $url = sprintf(self::USER_INFO, $accessToken, $openId);
        return CurlToolkit::request('GET', $url);
    }

}