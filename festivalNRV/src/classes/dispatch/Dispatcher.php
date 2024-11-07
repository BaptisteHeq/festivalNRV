<?php

namespace iutnc\nrv\dispatch;

use iutnc\nrv\action\AddSoireeAction;
use iutnc\nrv\action\AddSpectacleToSoireeAction;
use iutnc\nrv\action\AfficherSpectaclesAction;

use iutnc\nrv\action\DefaultAction;
use iutnc\nrv\action\AddSpectacleAction;
use iutnc\nrv\action\DeleteSoireeAction;
use iutnc\nrv\action\DeleteSpectacleAction;
use iutnc\nrv\action\DisplaySoireeAction;
use iutnc\nrv\action\DisplaySpectacleAction;
use iutnc\nrv\action\DisplayProgrammeAction;
use iutnc\nrv\action\DisplaySpectacleDetailAction;

use iutnc\nrv\action\SignInAction;
use iutnc\nrv\action\UpdateSpectacleAction;


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
            default:
                $action = new DefaultAction();
                $html = $action->execute();
                break;


        }
        $this->renderPage($html);
    }

    public function renderPage ($html)
    {
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
    <nav>
    <ul>
    <li><a href="?action=add-spec-to-soiree">Ajouter un spectacle à une soirée</a></li>
    <li><a href="?action=delete-soiree">Supprimer une soirée</a></li>
    <li><a href="?action=add-soiree">Ajouter une soirée</a></li>
    <li><a href="?action=soiree">Soirée</a></li>
    <li><a href="?action=spectacle-detail">Spectacle en session</a></li>
    <li><a href="?action=add-spectacle">Ajouter un spectacle</a></li>
    <li><a href="?action=update-spectacle">Editer le spectacle en session</a></li>
    <li><a href="?action=programme">Programme</a></li>
    <li><a href="?action=display_spectacle">Liste des spectacles</a></li>
    <li><a href="?action=signin">Se connecter</a></li>
    </ul>
    </header>
    
    <main>
    $html
    </main>
    
</html>

HTML;

    }

}