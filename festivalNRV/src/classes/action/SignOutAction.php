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
        if (!AuthzProvider::isAuthorized($this->role))
            return '<div class="alert alert-danger">Vous n\'êtes pas autorisé à accéder à cette page</div>';

        $html = "";

        if(!isset($_SESSION['email'])){
            $html .= '<p>Vous n\'êtes pas connecté</p>';
        }
        else{
            AuthnProvider::signout();
            session_destroy();
            $html .= '<p>Vous êtes déconnecté</p>';
            header('Location: index.php');
        }

        return $html;
    }
}