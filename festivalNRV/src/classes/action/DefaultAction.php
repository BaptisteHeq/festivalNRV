<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthzProvider;

class DefaultAction extends Action
{

    public function __construct()
    {
        parent::__construct();
        $this->role = 1;
    }

    public function execute(): string
    {
        if (!AuthzProvider::isAuthorized($this->role))
            return '<div class="alert alert-danger">Vous n\'êtes pas autorisé à accéder à cette page</div>';

        return "Hello World";
    }
}