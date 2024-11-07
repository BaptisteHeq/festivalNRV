<?php

declare(strict_types=1);

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\auth\AuthzProvider;
use iutnc\nrv\exception\AuthnException;

class SignOutAction extends Action
{
    public function __construct()
    {
        parent::__construct();
        $this->role = 1;
    }

    public function execute(): string{
        if(!AuthzProvider::isAuthorized($this->role))
            return "Vous n'êtes pas autorisé à accéder à cette page";

        $html = "";

        if(!isset($_SESSION['email'])){
            $html .= '<p>Vous n\'êtes pas connecté</p>';
        }
        else{
            AuthnProvider::signout();
            $html .= '<p>Vous êtes déconnecté</p>';
        }

        return $html;
    }
}