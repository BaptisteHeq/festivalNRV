<?php

declare(strict_types=1);

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\auth\AuthzProvider;
use iutnc\nrv\exception\AuthnException;

class SignInAction extends Action
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
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            $html .= <<<HTML
            <h2>Connexion</h2>
            <form method="post" action="?action=signin">
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
            try{
                $bool = AuthnProvider::signin($email, $password);
                if($bool)
                    $html .= '<p>Connexion réussie</p>';
                else
                    $html .= '<p>Connexion échouée</p>';

            }catch(AuthnException $e){
                $html .= $e->getMessage();
            }
        }
        return $html;
    }
}