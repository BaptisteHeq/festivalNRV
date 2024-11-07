<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthzProvider;
use iutnc\nrv\evenement\programme\Programme;
use iutnc\nrv\evenement\spectacle\Spectacle;
use iutnc\nrv\repository\NrvRepository;

class AddSpectacleAction extends Action
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

        $html = "";
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $html .= <<<HTML
            <h2>Créer un nouveau spectacle</h2>
            <form method="post" action="?action=add-spectacle" enctype="multipart/form-data">
                <label for="spectacle_name">Nom du spectacle</label> 
                <input type="text" id="spectacle_name" name="spectacle_name" required> <br>
                <label for="spectacle_date">Date du spectacle</label>
                <input type="date" id="spectacle_date" name="spectacle_date" required>  <br>
                <label for="spectacle_style">Style du spectacle</label> 
                <select id="spectacle_style" name="spectacle_style" required>
                    <option value="1">Classic Rock</option>
                    <option value="2">Blue Rock</option>
                    <option value="3">Metal</option>
                </select> <br>
                <label for="spectacle_horaire">Horaire du spectacle</label>
                <input type="time" id="spectacle_horaire" name="spectacle_horaire" required> <br>
                <label for="spectacle_image">Image du spectacle</label>
                <input type="file" id="spectacle_image" accept="image/png" name="spectacle_image" required> <br>
                <label for="spectacle_description">Description du spectacle</label>
                <input type="text" id="spectacle_description" name="spectacle_description" required> <br>
                <label for="spectacle_video">Vidéo du spectacle</label>
                <input type="file" id="spectacle_video" name="spectacle_video" accept="video/mp4" required> <br>
                <label for="spectacle_artistes">Artistes du spectacle</label>
                <input type="text" id="spectacle_artistes" name="spectacle_artistes" required> <br>
                <label for="spectacle_duree">Durée du spectacle</label>
                <input type="number" id="spectacle_duree" name="spectacle_duree" required> <br>
                
                <input type="submit" value="Créer le spectacle">
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

            /* vérification des fichiers image */
            if (substr($_FILES['spectacle_image']['name'],-4) !== '.png' || $_FILES['spectacle_image']['type'] !== 'image/png') {
                $html .= '<b>Le fichier n\'est pas une image png.</b>';
                return $html;
            } else {
                $imagename = uniqid() . '.png';
                move_uploaded_file($_FILES['spectacle_image']['tmp_name'],'./media/' . $imagename);
            }
            /* vérificaation des vidéos */
            if (substr($_FILES['spectacle_video']['name'],-4) !== '.mp4' || $_FILES['spectacle_video']['type'] !== 'video/mp4') {
                $html .= '<b>Le fichier n\'est pas un fichier vidéo mp4.</b>';
                return $html;
            } else {
                $videoname = uniqid() . '.mp4';
                move_uploaded_file($_FILES['spectacle_video']['tmp_name'],'./media/' . $videoname);
            }




            $spectacle = new Spectacle($spectacle_name, $spectacle_date, $spectacle_style, $spectacle_horaire, $imagename, $spectacle_description, $videoname, $spectacle_artistes, $spectacle_duree);

            $r= NrvRepository::getInstance();
            $id = $r->addSpectacle($spectacle_name, $spectacle_date, $spectacle_style, $spectacle_horaire, $imagename, $spectacle_description, $videoname, $spectacle_artistes, $spectacle_duree);
            $spectacle->setSpectacleID($id);
            $_SESSION['spectacle'] = serialize($spectacle);
            $html .= 'Spectacle ajouté avec succès';
        }
        return $html;
    }
}