<?php

declare(strict_types=1);

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\auth\AuthzProvider;
use iutnc\nrv\exception\AuthnException;

class RegisterAction extends Action
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
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            $html .= <<<HTML
            <h2>Inscription</h2>
            <form method="post" action="?action=register">
                <label>Nom</label>  
                <input type="text" name="nom" required>
                <label>Email</label>
                <input type="email" name="email" required> <br>
                <label>Mot de passe</label>
                <input type="password" name="password" required> <br>
                <input type="submit" value="Se connecter">
            </form>
            HTML;
        }elseif($_SERVER['REQUEST_METHOD'] === 'POST'){
            $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
            $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
            $nom = filter_var($_POST['nom'], FILTER_SANITIZE_STRING);

            try {
                AuthnProvider::register($nom, $email, $password);
                $html .= '<p>Inscription réussie</p>';
            }
            catch(AuthnException $e){
                $html .= $e->getMessage();
            }

        }
        return $html;
    }
}