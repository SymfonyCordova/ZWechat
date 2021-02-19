<?php


namespace Zler\Wechat\Service;


interface GzhService
{
    const GET_ACCESS_TOKEN_URL = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s";
    const CREATE_MENU_URL = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s";
    const GET_USER_INFO_URL = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN";

    const OAUTH_ACCESS_TOKEN_URL = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code";
    const OAUTH_USER_INFO_URL = "https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN";

    const GET_JST_TICKET_URL = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi";
    const PAY_UNIFIED_ORDER_URL = "https://api.mch.weixin.qq.com/pay/unifiedorder";

    const ADD_CUSTOMER_URL = 'https://api.weixin.qq.com/customservice/kfaccount/add?access_token=%s';
    const CUSTOMER_SEND_MESSAGE_URL = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s';

    const EVENT = 'event';
    const EVENT_SUBSCRIBE = 'subscribe';
    const EVENT_UNSUBSCRIBE = 'unsubscribe';
    const EVENT_CLICK = 'CLICK';

    public function checkSignature($fields);

    public function getAccessToken();

    public function getUserInfo($openId);

    public function getJsTikcet();

    public function getJsSdkParams($fields);
}