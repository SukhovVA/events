<?php

namespace App\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class VisitExistException extends Exception
{
    protected $message = 'Visit Already Exist';
    protected $code = Response::HTTP_BAD_REQUEST;
}
