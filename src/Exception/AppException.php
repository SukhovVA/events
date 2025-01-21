<?php

namespace App\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;


class AppException extends Exception
{
    protected $message = 'Internal server error';
    protected $code = Response::HTTP_INTERNAL_SERVER_ERROR;
}
