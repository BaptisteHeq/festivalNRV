<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthzProvider;
use iutnc\nrv\evenement\programme\Programme;
use iutnc\nrv\evenement\spectacle\Spectacle;
use iutnc\nrv\renderer\Renderer;
use iutnc\nrv\renderer\SpectacleRenderer;
use iutnc\nrv\repository\NrvRepository;

class AfficherSpectaclesAction extends Action
{

    public function __construct()
    {
        parent::__construct();
        $this->role = 1;
    }

    public function execute(): string
    {
        if(!AuthzProvider::isAuthorized($this->role))
            return "Vous n'êtes pas autorisé à accéder à cette page";

        $html = '<p><b>Affichage de la liste de tous les spectacles</b></p><br>';

        //menu déroulant pour filtrer les spectacles par style, par lieu, par date
        //affichage de la liste des spectacles
        //affichage des spectacles par style, par lieu, par date

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $html .= '<form action="?action=display_spectacle" method="post">';

            // Liste déroulante du filtre principal
            $html .= '<label for="filtre">Choisissez un filtre : </label>';
            $html .= '<select name="filtre" id="filtre" onchange="SaisiInfo()">';
            $html .= '<option value="">-- Sélectionnez un filtre --</option>';
            $html .= '<option value="style">Style</option>';
            $html .= '<option value="lieu">Lieu</option>';
            $html .= '<option value="date">Date</option>';
            $html .= '</select>';

            // Liste déroulante pour les styles (cachée par défaut)
            $html .= '<div id="styleFilter" style="display:none;">';
            $html .= '<label for="style">Sélectionnez un style : </label>';
            $html .= '<select name="style">';
            // Remplir la liste de styles à partir de la base de données
            $styles = NrvRepository::getInstance()->getStyles();
            foreach ($styles as $style) {
                $html .= '<option value="' . $style['styleID'] . '">' . $style['nomStyle'] . '</option>';
            }
            $html .= '</select>';
            $html .= '</div>';

            // Liste déroulante pour le lieu (cachée par défaut)
            $html .= '<div id="lieuFilter" style="display:none;">';
            $html .= '<label for="lieu">Sélectionnez un lieu : </label>';
            $html .= '<select name="lieu">';
            $lieux = NrvRepository::getInstance()->getLieux();
            foreach ($lieux as $lieu) {
                $html .= '<option value="' . $lieu['lieuID'] . '">' . $lieu['nomLieu'] . '</option>';
            }
            $html .= '</select>';
            $html .= '</div>';

            // Input pour la date (caché par défaut)
            $html .= '<div id="dateFilter" style="display:none;">';
            $html .= '<label for="date">Choisissez une date : </label>';
            $html .= '<input type="date" name="date">';
            $html .= '</div>';

            // Bouton pour afficher les résultats
            $html .= '<br><input type="submit" value="Afficher">';
            $html .= '</form>';

            //afficher tous les spectacles
            $r = NrvRepository::getInstance();
            $spectacles = $r->getSpectacles();
            foreach ($spectacles as $spectacle) {
                $renderer = new SpectacleRenderer($spectacle);
                $html .= $renderer->render(Renderer::COMPACT);
            }

            // Script JavaScript pour afficher le bon filtre
            $html .= <<<HTML
            
            
        <script>
            function SaisiInfo() {
                // Masquer tous les filtres au départ
                document.getElementById("styleFilter").style.display = "none";
                document.getElementById("lieuFilter").style.display = "none";
                document.getElementById("dateFilter").style.display = "none";
                
                // Afficher le filtre sélectionné
                var filter = document.getElementById("filtre").value;
                if (filter === "style") {
                    document.getElementById("styleFilter").style.display = "block";
                } else if (filter === "lieu") {
                    document.getElementById("lieuFilter").style.display = "block";
                } else if (filter === "date") {
                    document.getElementById("dateFilter").style.display = "block";
                }
            }
        </script>
    HTML;
        }
        elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $html = '<h2>Résultats du filtre</h2>';

            // Vérifiez quel filtre est sélectionné dans le formulaire
            $filtre = $_POST['filtre'];

            // Récupérez les spectacles en fonction du filtre
            $r = NrvRepository::getInstance();
            $spectacles = [];

            if ($filtre === 'style' && !empty($_POST['style'])) {
                $styleID = $_POST['style'];
                $spectacles = $r->getSpectaclesByStyle($styleID);
            } elseif ($filtre === 'lieu' && !empty($_POST['lieu'])) {
                $lieu = $_POST['lieu'];
                $spectacles = $r->getSpectaclesByLieu($lieu);
            } elseif ($filtre === 'date' && !empty($_POST['date'])) {
                $date = $_POST['date'];
                $spectacles = $r->getSpectaclesByDate($date);
            } else {
                $html .= "<p>Aucun filtre valide n'a été sélectionné.</p>";
            }

            // Affichez les résultats
            if (!empty($spectacles)) {
                foreach ($spectacles as $spectacle) {
                    $renderer = new SpectacleRenderer($spectacle);
                    $html .= $renderer->render(Renderer::COMPACT);
                }
            } else {
                $html .= "<p>Aucun spectacle trouvé pour ce filtre.</p>";
            }
        }

        return $html;
    }
}

