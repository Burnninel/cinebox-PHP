<?php

namespace Cinebox\App\Core;

use Cinebox\App\Middlewares\AuthMiddleware;

class AuthenticatedController extends BaseController
{
    protected object $usuario;

    public function __construct()
    {
        $this->usuario = (new AuthMiddleware())->handle();
    }
}