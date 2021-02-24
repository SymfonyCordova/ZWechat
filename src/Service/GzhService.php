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

    const GET_JST_TICKET_URL
        = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi";

    const GENERATE_SCENE_VALUE_QR_CODE_URL =
        'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=%s';

    const GENERATE_SCENE_VALUE_QR_CODE_TEMPLATE
        = '{"action_name": "%s", "action_info": {"scene": {"scene_str": "%s"}}}';

    const SHOW_QR_CODE_URL = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=%s';

    const CREATE_MENU_URL = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s";

//    const PAY_UNIFIED_ORDER_URL = "https://api.mch.weixin.qq.com/pay/unifiedorder";
//    const ADD_CUSTOMER_URL = 'https://api.weixin.qq.com/customservice/kfaccount/add?access_token=%s';
//    const CUSTOMER_SEND_MESSAGE_URL = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s';

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

    /**
     * 生成带参数的二维码
     * @param $sceneValue
     * @param string $actionName
     * @return mixed
     */
    public function generateSceneValueQrCode($sceneValue, $actionName = 'QR_LIMIT_STR_SCENE');

    /**
     * 解析微信消息
     * @return mixed
     */
    public function resolveMessage();

    /**
     * 是否是关注事件
     * @return boolean|array
     */
    public function hasResolveSubscribeEvent();

    /**
     * 是否是取消关注事件
     * @return mixed
     */
    public function hasResolveUnSubscribeEvent();

    /**
     * 用户已关注时的事件推送
     *
     * @return mixed
     */
    public function hasResolveScanSubscribedEvent();

    /**
     * 用户未关注时，进行关注后的事件推送
     *
     * @return mixed
     */
    public function hasResolveScanUnsubscribedEvent();

    /**
     * 创建微信消息
     * @param $fromUsername
     * @param $toUsername
     * @param $context
     * @return mixed
     */
    public function createTextMessage($fromUsername, $toUsername, $context);
}