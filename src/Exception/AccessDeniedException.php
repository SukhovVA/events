<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AccessDeniedException as SymfonyAccessDeniedException;

class AccessDeniedException extends SymfonyAccessDeniedException
{
    protected $message = 'Нет доступа. Пожалуйста, обратитесь к менеджеру';

    public function __construct()
    {
        parent::__construct($this->message);
    }
}
