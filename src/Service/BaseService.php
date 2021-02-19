<?php


namespace Zler\Wechat\Service;


interface BaseService
{
    const GET_ACCESS_TOKEN_URL          = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';

    const GET_USER_INFO_URL             = 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s';
//    const PAY_UNIFIED_ORDER_URL = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
//    const ADD_RECEIVER_URL      = 'https://api.mch.weixin.qq.com/pay/profitsharingaddreceiver';
//    const PROFIT_SHARING_URL    = 'https://api.mch.weixin.qq.com/secapi/pay/profitsharing';

    public function checkSignature($fields);

    public function getAccessToken($code);

    public function getUserInfo($accessToken, $openId);

    public function getJsTikcet();

    public function getJsSdkParams($fields);
}