<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthzProvider;
use iutnc\nrv\evenement\programme\Programme;
use iutnc\nrv\evenement\spectacle\Spectacle;
use iutnc\nrv\repository\NrvRepository;

class UpdateSpectacleAction extends Action
{
    public function __construct()
    {
        parent::__construct();
        $this->role = 50;
    }

    public function execute(): string
    {
        if(!AuthzProvider::isAuthorized($this->role))
            return "Vous n'êtes pas autorisé à accéder à cette page";

        if(!isset($_GET['idSpectacle'])){
            return "Pas de spectacle en session";
        }
        /* récupération du spectacle */
        $spectacle = unserialize($_SESSION['spectacle']);

        $html = "";
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            $nom = $spectacle->getNom();
            $date = $spectacle->getDate();
            $horaire = $spectacle->getHoraire();
            $description = $spectacle->getDescription();
            $artiste = $spectacle->getArtistes();
            $duree = $spectacle->getDuree();
            $idSpectacle = $spectacle->getSpectacleID();


            $html .= <<<HTML
            <h2>Modifier le spectacle en session</h2>
            <form method="post" action="?action=update-spectacle&idSpectacle=$idSpectacle" enctype="multipart/form-data">
                <label for="spectacle_name">Nom du spectacle</label> 
                <input type="text" id="spectacle_name" name="spectacle_name" value="$nom" required> <br>
                <label for="spectacle_date" >Date du spectacle</label>
                <input type="date" id="spectacle_date" name="spectacle_date" value="$date" required>  <br>
                <label for="spectacle_style">Style du spectacle</label> 
                <select id="spectacle_style" name="spectacle_style" required>
                    <option value="1">Classic Rock</option>
                    <option value="2">Blue Rock</option>
                    <option value="3">Metal</option>
                </select> <br>
                <label for="spectacle_horaire">Horaire du spectacle</label>
                <input type="time" id="spectacle_horaire" name="spectacle_horaire" value="$horaire" required> <br>

                <label for="spectacle_description">Description du spectacle</label>
                <input type="text" id="spectacle_description" name="spectacle_description" value="$description" required> <br>

                <label for="spectacle_artistes">Artistes du spectacle</label>
                <input type="text" id="spectacle_artistes" name="spectacle_artistes" value="$artiste" required> <br>
                <label for="spectacle_duree">Durée du spectacle</label>
                <input type="number" id="spectacle_duree" name="spectacle_duree" value="$duree" required> <br>
                
                <input type="submit" value="Modifier le spectacle">
            </form>
            HTML;

        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $spectacle_name = filter_var($_POST['spectacle_name'], FILTER_SANITIZE_STRING);
            $spectacle_date = filter_var($_POST['spectacle_date'], FILTER_SANITIZE_STRING);
            $spectacle_style = filter_var($_POST['spectacle_style'], FILTER_SANITIZE_STRING);
            $spectacle_horaire = filter_var($_POST['spectacle_horaire'], FILTER_SANITIZE_STRING);
            $spectacle_description = filter_var($_POST['spectacle_description'], FILTER_SANITIZE_STRING);
            $spectacle_artistes = filter_var($_POST['spectacle_artistes'], FILTER_SANITIZE_STRING);
            $spectacle_duree = filter_var($_POST['spectacle_duree'], FILTER_SANITIZE_STRING);


            $nom = $spectacle->getNom();
            $date = $spectacle->getDate();
            $horaire = $spectacle->getHoraire();

            $r= NrvRepository::getInstance();

            $idspectacle = $r->getIdSpectacle($nom, $date, $horaire);

            $r->updateSpectacle($idspectacle, $spectacle_name, $spectacle_date, $spectacle_style, $spectacle_horaire, $spectacle_description, $spectacle_duree);

            $spectacle = $r->getSpectacleByID($idspectacle);
            $spectacle->setSpectacleID($idspectacle);
            $_SESSION['spectacle'] = serialize($spectacle);

            $html .= 'Spectacle modifié avec succès';
        }
        return $html;
    }
}