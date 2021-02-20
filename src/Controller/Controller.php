<?php


namespace Zler\Wechat\Controller;


use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Zler\Wechat\Exception\InvalidArgumentException;
use Zler\Wechat\Service\GzhService;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

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
    protected function getGzh()
    {
        return app()->make(GzhService::class);
    }

}