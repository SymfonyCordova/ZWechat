<?php


namespace Zler\Wechat\Provider;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Zler\Wechat\Service\Impl\GzhServiceImpl;

class WechatServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['wechat.default_options'] = [
            'gzh' => [
                'app_id' => '',
                'app_secret' => '',
                'token' => '',
            ],
            'miniprograme' => [
                'app_id' => '',
                'app_secret' => '',
            ],
            'pc' => [
                'app_id' => '',
                'app_secret' => '',
            ]
        ];

        $app['wechat.options.initializer'] = $app->protect(function () use ($app) {
            static $initialized = false;

            if ($initialized) {
                return;
            }

            $initialized = true;

            if (!isset($app['wechat.options'])) {
                $app['wechat.options'] = $app['wechat.default_options'];
            }

            $tmp = $app['wechat.options'];
            foreach ($tmp as $name => &$options) {
                $options = array_replace($app['wechat.default_options'], $options);
            }

            $app['wechat.options'] = $tmp;
        });

        $app['gzh'] = function ($app){
            $app['wechat.options.initializer']();
            return new GzhServiceImpl($app['wechat.options.gzh']);
        };
    }
}