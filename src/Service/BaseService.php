<?php


namespace Zler\Wechat\Service;


interface BaseService
{
    const ACCESS_TOKEN          = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';
    const USER_INFO             = 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s';
//    const PAY_UNIFIED_ORDER_URL = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
//    const ADD_RECEIVER_URL      = 'https://api.mch.weixin.qq.com/pay/profitsharingaddreceiver';
//    const PROFIT_SHARING_URL    = 'https://api.mch.weixin.qq.com/secapi/pay/profitsharing';

    public function getAccessToken($code);

    public function getUserInfo($accessToken, $openId);
}