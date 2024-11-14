<?php

namespace iutnc\nrv\dispatch;

use Exception;
use iutnc\nrv\action\AddSoireeAction;
use iutnc\nrv\action\AddSpectacleToSoireeAction;
use iutnc\nrv\action\AfficherSpectaclesAction;

use iutnc\nrv\action\AnnulerAction;
use iutnc\nrv\action\DefaultAction;
use iutnc\nrv\action\AddSpectacleAction;
use iutnc\nrv\action\DeleteSoireeAction;
use iutnc\nrv\action\DeleteSpectacleAction;
use iutnc\nrv\action\DeleteSpectacleToSoireeAction;
use iutnc\nrv\action\DisplaySoireeAction;
use iutnc\nrv\action\DisplaySpectacleAction;
use iutnc\nrv\action\DisplayProgrammeAction;
use iutnc\nrv\action\DisplaySpectacleDetailAction;
use iutnc\nrv\action\RegisterAction;
use iutnc\nrv\action\SearchAction;
use iutnc\nrv\action\SignOutAction;

use iutnc\nrv\action\SignInAction;
use iutnc\nrv\action\UpdateSpectacleAction;
use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\repository\NrvRepository;
use iutnc\nrv\action\UpdateRoleAction;


class Dispatcher
{
    private string $action;

    public function __construct(string $action)
    {
        $this->action = $action;
    }

    public function run()
    {
        switch ($this->action) {
            case 'annuler':
                $action = new AnnulerAction();
                $html = $action->execute();
                break;
            case 'delete-spectacle-to-soiree':
                $action = new DeleteSpectacleToSoireeAction();
                $html = $action->execute();
                break;
            case 'update-spectacle':
                $action = new UpdateSpectacleAction();
                $html = $action->execute();
                break;
            case 'add-spec-to-soiree':
                $action = new AddSpectacleToSoireeAction();
                $html = $action->execute();
                break;
            case 'delete-soiree':
                $action = new DeleteSoireeAction();
                $html = $action->execute();
                break;
            case 'add-soiree':
                $action = new AddSoireeAction();
                $html = $action->execute();
                break;
            case 'soiree':
                $action = new DisplaySoireeAction();
                $html = $action->execute();
                break;
            case 'delete-spectacle':
                $action = new DeleteSpectacleAction();
                $html = $action->execute();
                break;
            case 'spectacle-detail':
                $action = new DisplaySpectacleDetailAction();
                $html = $action->execute();
                break;
            case 'programme':
                $action = new DisplayProgrammeAction();
                $html = $action->execute();
                break;
            case 'spectacle':
                $action = new DisplaySpectacleAction();
                $html = $action->execute();
                break;
            case 'add-spectacle':
                $action = new AddSpectacleAction();
                $html = $action->execute();
                break;

            case 'display_spectacle':
                $action = new AfficherSpectaclesAction();
                $html=$action->execute();
                break;
            case 'signin':
                $action = new SignInAction();
                $html = $action->execute();
                break;
            case 'signout':
                $action = new SignOutAction();
                $html = $action->execute();
                break;
            case 'register':
                $action = new RegisterAction();
                $html = $action->execute();
                break;
            case 'update-role':
                $action = new UpdateRoleAction();
                $html = $action->execute();
                break;
            case 'search':
                $nom = isset($_POST['NomSp']) ? $_POST['NomSp'] : '';
                $action = new SearchAction($nom);
                $html = $action->execute();
                break;
            default:
                $action = new DefaultAction();
                $html = $action->execute();
                break;


        }
        $this->renderPage($html);
    }

    public function renderPage ($html)
    {
        $email = "pas connecté";
        $nom = "pas connecté";
        if(isset($_SESSION['email']))
        {
            $email = $_SESSION['email'];
            $r = NrvRepository::getInstance();
            $nom = $r->getNomUser($email);
        }
        try{
            $user = AuthnProvider::getSignedInUser();
            $estConnecte = true;
        }catch (Exception $e){
            $estConnecte = false;
        }

        $DecoReco = $estConnecte
            ? '<button onclick="window.location.href=\'?action=signout\';">déconnexion</button>'
            : '<button onclick="window.location.href=\'?action=signin\';">connexion</button>
       <button onclick="window.location.href=\'?action=register\';">inscription</button>';


        echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Festival NRV</title>
    </head>
    <header>
    <h1>Festival NRV</h1>
    <h2>utilisateur : $email, $nom</h2>
    $DecoReco
    <nav>
    <ul>
    <li><a href="?action=delete-spectacle-to-soiree"> supprimer un spectacle d'une soirée </a></li>
    <li><a href="?action=add-spec-to-soiree">Ajouter un spectacle à une soirée</a></li>
    <li><a href="?action=add-soiree">Ajouter une soirée</a></li>
    <li><a href="?action=add-spectacle">Ajouter un spectacle</a></li>
    <li><a href="?action=programme">Programme</a></li>
    <li><a href="?action=display_spectacle">Liste des spectacles</a></li>
    <li><a href="?action=update-role">Mettre à jour le role d'un utilisateur</a></li>
    </ul>
    <form action="?action=search" method="post" style="float: right; margin-top: -250px; margin-right: 200px;">
    <input type="text" name="NomSp" placeholder="Recherche..." required>
    <button type="submit">Rechercher</button>
    </form>

    </header>
    
    <main>
    $html
    </main>
    
</html>

HTML;

    }

}