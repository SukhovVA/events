<?php

namespace App\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ProsvException extends Exception
{
    protected $code = Response::HTTP_BAD_REQUEST;

    /**
     * @param string $message
     */
    public function __construct(string $message = 'Prosv Gateway exception')
    {
        parent::__construct($message);
    }
}
