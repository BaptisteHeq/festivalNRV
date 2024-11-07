<?php

declare(strict_types=1);

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\exception\AuthnException;

class SignInAction extends Action
{
    public function execute(): string{
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
                AuthnProvider::signin($email, $password);
                
                $html .= '<p>Connexion réussie</p>';
            }catch(AuthnException $e){
                $html .= $e->getMessage();
            }
        }
        return $html;
    }
}