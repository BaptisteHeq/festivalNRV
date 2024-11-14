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
        if (!AuthzProvider::isAuthorized($this->role))
            return '<div class="alert alert-danger">Vous n\'êtes pas autorisé à accéder à cette page</div>';

        $html = "";
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $html .= <<<HTML
<h2>Créer un nouveau spectacle</h2>
<form method="post" action="?action=add-spectacle" enctype="multipart/form-data">
    <div class="form-group">
        <label for="spectacle_name">Nom du spectacle</label> 
        <input type="text" id="spectacle_name" name="spectacle_name" class="form-control" required>
    </div>
    
    <div class="form-group">
        <label for="spectacle_date">Date du spectacle</label>
        <input type="date" id="spectacle_date" name="spectacle_date" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="spectacle_style">Style du spectacle</label> 
        <select id="spectacle_style" name="spectacle_style" class="form-control" required>
HTML;

            $styles = NrvRepository::getInstance()->getStyles();
            foreach ($styles as $style) {
                $html .= '<option value=' . $style['styleID'] . '>' . $style['nomStyle'] . '</option>';
            }

            $html .= <<<HTML
        </select>
    </div>
    
    <div class="form-group">
        <label for="spectacle_lieu">Lieu du spectacle</label> 
        <select id="spectacle_lieu" name="spectacle_lieu" class="form-control" required>
HTML;

            $lieux = NrvRepository::getInstance()->getLieux();
            foreach ($lieux as $lieu) {
                $html .= '<option value=' . $lieu['lieuID'] . '>' . $lieu['nomLieu'] . '</option>';
            }

            $html .= <<<HTML
        </select>
    </div>
    
    <div class="form-group">
        <label for="spectacle_horaire">Horaire du spectacle</label>
        <input type="time" id="spectacle_horaire" name="spectacle_horaire" class="form-control" required>
    </div>
    
    <div class="form-group">
        <label for="spectacle_description">Description du spectacle</label>
        <input type="text" id="spectacle_description" name="spectacle_description" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="spectacle_duree">Durée du spectacle (en minutes)</label>
        <input type="number" id="spectacle_duree" name="spectacle_duree" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="spectacle_artistes">Artistes du spectacle</label>
        <div id="artistes_container">
            <input type="text" name="spectacle_artistes[]" class="form-control" required> <br>
        </div>
        <button type="button" class="btn btn-add" onclick="addArtisteField()">Ajouter un artiste</button>
    </div>
    
    <div class="form-group">
        <label>Images du spectacle</label>
        <div id="images_container">
            <input type="file" accept="image/png" name="spectacle_images[]" class="form-control-file" required> <br>
        </div>
        <button type="button" class="btn btn-add" onclick="addImageField()">Ajouter une image</button>
    </div>
    
    <div class="form-group">
        <label>Vidéos du spectacle</label>
        <div id="videos_container">
            <input type="file" accept="video/mp4" name="spectacle_videos[]" class="form-control-file" required> <br>
        </div>
        <button type="button" class="btn btn-add" onclick="addVideoField()">Ajouter une vidéo</button>
    </div>

    <input type="submit" value="Créer le spectacle" class="btn btn-primary btn-block mt-3">
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
            $spectacle_lieu = filter_var($_POST['spectacle_lieu'], FILTER_SANITIZE_NUMBER_INT);

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



            $r= NrvRepository::getInstance();
            $spectacle = new Spectacle($spectacle_name, $spectacle_date, (int)$spectacle_style, $spectacle_horaire, $images, $spectacle_description, $videos, $spectacle_artistes, $spectacle_duree, 0, $r->getLieuById($spectacle_lieu));

            $id = $r->addSpectacle($spectacle_name,(int) $spectacle_style,$spectacle_date,0, $spectacle_horaire, $images, $spectacle_description, $videos, $spectacle_artistes, $spectacle_duree, $spectacle_lieu);
            $spectacle->setSpectacleID($id);
            $_SESSION['spectacle'] = serialize($spectacle);
            $html .= 'Spectacle ajouté avec succès';
        }
        return $html;
    }
}