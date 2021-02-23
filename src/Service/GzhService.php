<?php


namespace Zler\Wechat\Service;


interface GzhService
{
    const GET_ACCESS_TOKEN_URL =
        "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s";

    const GET_USER_INFO_URL =
        "https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN";

    const GENERATE_OAUTH2_URL =
        'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=%s#wechat_redirect';

    const OAUTH2_ACCESS_TOKEN_URL =
        "https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code";

    const OAUTH2_USER_INFO_URL =
        'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN';


    //const CREATE_MENU_URL = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s";


    const GET_JST_TICKET_URL = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi";
//    const PAY_UNIFIED_ORDER_URL = "https://api.mch.weixin.qq.com/pay/unifiedorder";
//
//    const ADD_CUSTOMER_URL = 'https://api.weixin.qq.com/customservice/kfaccount/add?access_token=%s';
//    const CUSTOMER_SEND_MESSAGE_URL = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s';

    const EVENT = 'event';
    const EVENT_SUBSCRIBE = 'subscribe';
    const EVENT_UNSUBSCRIBE = 'unsubscribe';
    const EVENT_CLICK = 'CLICK';

    /**
     * 公众号验证开发者服务器token
     * @param $fields
     * @return boolean
     */
    public function checkSignature($fields);

    /**
     * @return 获取公众号accessToken
     */
    public function getAccessToken();

    /**
     * 根据openid获取用户信息
     * @param $openId
     * @return mixed
     */
    public function getUserInfo($openId);

    /**
     * 网页授权-生成Oauth2跳转的地址
     * @param $redirectUri
     * @param string $state
     * @return mixed
     */
    public function generateOauth2Url($redirectUri, $state="STATE");

    public function getUserInfoByOauth2Code($code);

    /**
     * 获取js的tikcet的票据
     * @return mixed
     */
    public function getJsTikcet();

    /**
     * 生成客户端js-sdk的参数
     * @param $fields
     * @return mixed
     */
    public function getJsSdkParams($fields);
}