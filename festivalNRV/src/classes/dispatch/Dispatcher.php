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
            case 'acceuil':
                header('Location= index.php');
            default:
                $action = new DefaultAction();
                $html = $action->execute();
                break;


        }
        $this->renderPage($html);
    }

    public function renderPage($html)
    {
        $email = "Pas connecté";
        $nom = "Pas connecté";
        if (isset($_SESSION['email'])) {
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
                ? '<a href="?action=signout" class="btn btn-danger ms-2">Déconnexion</a>
            <a href="?action=register" class="btn btn-warning ms-2">Inscription</a>'
                : '<a href="?action=signin" class="btn btn-primary ms-2">Connexion</a>';



        echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Festival NRV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="?action = 'accueil'">Festival NRV</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="?action=programme">Programme</a></li>
                    <li class="nav-item"><a class="nav-link" href="?action=display_spectacle">Liste des spectacles</a></li>
                    <li class="nav-item"><a class="nav-link" href="?action=add-spectacle">Ajouter un spectacle</a></li>
                    <li class="nav-item"><a class="nav-link" href="?action=add-soiree">Ajouter une soirée</a></li>
                    <li class="nav-item"><a class="nav-link" href="?action=update-role">Mettre à jour le rôle d'un utilisateur</a></li>
                </ul>
                <form class="d-flex" action="?action=search" method="post">
                    <input class="form-control me-2" type="search" name="NomSp" placeholder="Recherche..." required>
                    <button class="btn btn-outline-success" type="submit">Rechercher</button>
                </form>
                <div class="ms-3">
                    <span class="text-white">Utilisateur : $email, $nom</span>
                    $DecoReco
                </div>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="content">
            $html
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
    }

}