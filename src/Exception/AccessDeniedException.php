<?php


namespace Zler\Wechat\Exception;


class AccessDeniedException extends BaseException
{
    public function __construct($message = 'Access Denied', int $code = 403, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}