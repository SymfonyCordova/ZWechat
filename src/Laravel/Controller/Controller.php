<?php


namespace Zler\Wechat\Laravel\Controller;


use App\Http\Controllers\Controller as BaseController;
use Zler\Wechat\Exception\InvalidArgumentException;
use Zler\Wechat\Service\GzhService;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class Controller extends BaseController
{
    protected function checkRequiredFields($requiredFields, $requestData)
    {
        $requestFields = array_keys($requestData);
        foreach ($requiredFields as $field){
            if(!in_array($field, $requestFields)){
                throw new InvalidArgumentException(sprintf("missing required field: %s", $field));
            }
        }

        return $requestData;
    }

    protected function createSuccessResponse($errcode = 0, $errmsg = '')
    {
        return new JsonResponse(array(
            'errcode' => $errcode,
            'errmsg' => $errmsg,
        ), 200, $this->getHeaders());
    }

    protected function getHeaders()
    {
        return array(
            'Access-Control-Allow-Headers' => 'origin, content-type, accept, x-app-key, x-access-token, x-login-user-id, x-login-token',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'POST, GET, PUT, DELETE, PATCH, OPTIONS',
        );
    }

    /**
     * @return GzhService
     */
    protected function getGzhService()
    {
        return app()->make(GzhService::class);
    }

}