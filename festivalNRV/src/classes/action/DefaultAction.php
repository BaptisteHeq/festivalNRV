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
        if(!AuthzProvider::isAuthorized($this->role))
            return "Vous n'êtes pas autorisé à accéder à cette page";

        return "Hello World";
    }
}