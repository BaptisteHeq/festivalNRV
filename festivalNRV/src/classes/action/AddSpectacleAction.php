<?php

namespace iutnc\nrv\action;

use iutnc\nrv\evenement\programme\Programme;
use iutnc\nrv\evenement\spectacle\Spectacle;

class AddSpectacleAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        $html = "";
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $html .= <<<HTML
            <h2>Créer un nouveau spectacle</h2>
            <form method="post" action="?action=add-spectacle">
                <label for="spectacle_name">Nom du spectacle</label>
                <input type="text" id="spectacle_name" name="spectacle_name" required>
                <label for="spectacle_date">Date du spectacle</label>
                <input type="date" id="spectacle_date" name="spectacle_date" required>
                <label for="spectacle_heure">Heure du spectacle</label>
                <input type="time" id="spectacle_heure" name="spectacle_heure" required>
                <label for="spectacle_duree">Durée du spectacle</label>
                <input type="text" id="spectacle_duree" name="spectacle_duree" required>
                <label for="spectacle_lieu">Lieu du spectacle</label>
                <input type="text" id="spectacle_lieu" name="spectacle_lieu" required>
                <label for="spectacle_description">Description du spectacle</label>
                <textarea id="spectacle_description" name="spectacle_description" required></textarea>
                <input type="submit" value="Créer le spectacle">
            </form>
            HTML;

        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

                $spectacle_name = filter_var($_POST['spectacle_name'], FILTER_SANITIZE_STRING);
                $spectacle_date = filter_var($_POST['spectacle_date'], FILTER_SANITIZE_STRING);
                $spectacle_heure = filter_var($_POST['spectacle_heure'], FILTER_SANITIZE_STRING);
                $spectacle_duree = filter_var($_POST['spectacle_duree'], FILTER_SANITIZE_STRING);
                $spectacle_lieu = filter_var($_POST['spectacle_lieu'], FILTER_SANITIZE_STRING);
                $spectacle_description = filter_var($_POST['spectacle_description'], FILTER_SANITIZE_STRING);

                $spectacle = new Spectacle($spectacle_name, $spectacle_date, $spectacle_heure, $spectacle_duree, $spectacle_lieu, $spectacle_description);


                $_SESSION['spectacle'] = serialize($spectacle);
                $html .= 'Spectacle ajouté avec succès';
        }
        return $html;
    }
}