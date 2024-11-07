<?php

declare(strict_types=1);

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\exception\AuthnException;

class SignOutAction extends Action
{
    public function execute(): string{
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