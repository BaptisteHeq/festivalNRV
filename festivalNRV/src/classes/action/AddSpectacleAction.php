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
HTML;

// Génération des options de style depuis la base de données
            $styles = NrvRepository::getInstance()->getStyles();
            foreach ($styles as $style) {
                $html .= '<option value=' . $style['styleID'] . '>' . $style['nomStyle'] . '</option>';
            }

            $html .= <<<HTML
    </select> <br>
    <label for="spectacle_horaire">Horaire du spectacle</label>
    <input type="time" id="spectacle_horaire" name="spectacle_horaire" required> <br>
    
    <label for="spectacle_description">Description du spectacle</label>
    <input type="text" id="spectacle_description" name="spectacle_description" required> <br>
    <label for="spectacle_duree">Durée du spectacle</label>
    <input type="number" id="spectacle_duree" name="spectacle_duree" required> <br>

    <label for="spectacle_artistes">Artistes du spectacle</label>
    <div id="artistes_container">
        <input type="text" name="spectacle_artistes[]" required> <br>
    </div>
    <button type="button" onclick="addArtisteField()">Ajouter un artiste</button> <br><br>

    <!-- Images Section -->
    <label>Images du spectacle</label> <br>
    <div id="images_container">
        <input type="file" accept="image/png" name="spectacle_images[]" required> <br>
    </div>
    <button type="button" onclick="addImageField()">Ajouter une image</button> <br><br>

    <!-- Videos Section -->
    <label>Vidéos du spectacle</label> <br>
    <div id="videos_container">
        <input type="file" accept="video/mp4" name="spectacle_videos[]" required> <br>
    </div>
    <button type="button" onclick="addVideoField()">Ajouter une vidéo</button> <br><br>
    
    <input type="submit" value="Créer le spectacle">
</form>

<script>
    function addImageField() {
        let container = document.getElementById('images_container');
        let input = document.createElement('input');
        input.type = 'file';
        input.name = 'spectacle_images[]';
        input.accept = 'image/png';
        container.appendChild(input);
        container.appendChild(document.createElement('br'));
    }

    function addVideoField() {
        let container = document.getElementById('videos_container');
        let input = document.createElement('input');
        input.type = 'file';
        input.name = 'spectacle_videos[]';
        input.accept = 'video/mp4';
        container.appendChild(input);
        container.appendChild(document.createElement('br'));
    }
    
    function addArtisteField() {
        let container = document.getElementById('artistes_container');
        let input = document.createElement('input');
        input.type = 'text';
        input.name = 'spectacle_artistes[]';
        container.appendChild(input);
        container.appendChild(document.createElement('br'));
    }
</script>
HTML;


        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $spectacle_name = filter_var($_POST['spectacle_name'], FILTER_SANITIZE_STRING);
            $spectacle_date = filter_var($_POST['spectacle_date'], FILTER_SANITIZE_STRING);
            $spectacle_style = filter_var($_POST['spectacle_style'], FILTER_SANITIZE_NUMBER_INT);
            $spectacle_horaire = filter_var($_POST['spectacle_horaire'], FILTER_SANITIZE_STRING);
            $spectacle_description = filter_var($_POST['spectacle_description'], FILTER_SANITIZE_STRING);
            $spectacle_duree = filter_var($_POST['spectacle_duree'], FILTER_SANITIZE_STRING);
            // Récupérer les artistes (tableau de noms)
            $spectacle_artistes = array_map(function ($artiste) {
                return filter_var($artiste, FILTER_SANITIZE_STRING);
            }, $_POST['spectacle_artistes']);

            // Gestion des images
            $images = [];
            foreach ($_FILES['spectacle_images']['name'] as $index => $image_name) {
                if (substr($image_name, -4) === '.png' && $_FILES['spectacle_images']['type'][$index] === 'image/png') {
                    $unique_image_name = uniqid() . '.png';
                    move_uploaded_file($_FILES['spectacle_images']['tmp_name'][$index], './media/' . $unique_image_name);
                    $images[] = $unique_image_name;  // Stockage du chemin de l'image
                } else {
                    $html .= '<b>Le fichier ' . $image_name . ' n\'est pas une image PNG valide.</b><br>';
                    return $html;
                }
            }

            // Gestion des vidéos
            $videos = [];
            foreach ($_FILES['spectacle_videos']['name'] as $index => $video_name) {
                if (substr($video_name, -4) === '.mp4' && $_FILES['spectacle_videos']['type'][$index] === 'video/mp4') {
                    $unique_video_name = uniqid() . '.mp4';
                    move_uploaded_file($_FILES['spectacle_videos']['tmp_name'][$index], './media/' . $unique_video_name);
                    $videos[] = $unique_video_name;  // Stockage du chemin de la vidéo
                } else {
                    $html .= '<b>Le fichier ' . $video_name . ' n\'est pas un fichier vidéo MP4 valide.</b><br>';
                    return $html;
                }
            }




            $spectacle = new Spectacle($spectacle_name, $spectacle_date, (int)$spectacle_style, $spectacle_horaire, $images, $spectacle_description, $videos, $spectacle_artistes, $spectacle_duree);

            $r= NrvRepository::getInstance();
            $id = $r->addSpectacle($spectacle_name,(int) $spectacle_style,$spectacle_date,0, $spectacle_horaire, $images, $spectacle_description, $videos, $spectacle_artistes, $spectacle_duree);
            $spectacle->setSpectacleID($id);
            $_SESSION['spectacle'] = serialize($spectacle);
            $html .= 'Spectacle ajouté avec succès';
        }
        return $html;
    }
}