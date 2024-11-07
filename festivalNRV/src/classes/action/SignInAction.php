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
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" required> <br>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required> <br>
                <input type="submit" value="Se connecter">
            </form>
            HTML;
        }elseif($_SERVER['REQUEST_METHOD'] === 'POST'){
            $nom = filter_var($_POST['nom'], FILTER_SANITIZE_STRING);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
            try{
                AuthnProvider::signin($nom, $email);
                header('Location: index.php');
                $html .= '<p>Connexion réussie</p>';
            }catch(AuthnException $e){
                $html .= $e->getMessage();
            }
        }
        return $html;
    }
}