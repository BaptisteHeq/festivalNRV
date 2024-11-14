<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthzProvider;
use iutnc\nrv\evenement\soiree\Soiree;
use iutnc\nrv\repository\NrvRepository;

class DeleteSpectacleToSoireeAction extends Action
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
        $html = '<p><b>Suppression du spectacle de la soirée</b></p><br>';

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $r = NrvRepository::getInstance();
            $soirees = $r->getSoirees();
            $html .= '<form action="?action=delete-spectacle-to-soiree" method="post">';
            $html .= '<h2>Supprimer un spectacle d\'une soirée</h2>';

            //liste déroulante des spectacles
            $html .= '<select name="spectacle">';

            $spectacles = $r->getSpectacles();
            foreach ($spectacles as $spectacle) {
                $html .= '<option value="' . $spectacle->getSpectacleId() . '">' . $spectacle->getNom() . '</option>';
            }
            $html .= '</select>';


            //liste déroulante des soirees
            $html .= '<select name="soiree">';
            foreach ($soirees as $soiree) {
                $html .= '<option value="' . $soiree->getSoireeID() . '">' . $soiree->getNom() . '</option>';
            }
            $html .= '</select>';

            $html .= '<input type="submit" value="Supprimer">';
            $html .= '</form>';
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (isset($_POST['spectacle']) && isset($_POST['soiree'])) {
                $r = NrvRepository::getInstance();
                if ($r->estSpectacleInSoiree($_POST['spectacle'],$_POST['soiree'])==0) {
                    $html .= 'Le spectacle n\'est pas dans la soirée';
                } else {
                    $r = NrvRepository::getInstance();
                    $r->deleteSpectacleFromSoiree($_POST['soiree'], $_POST['spectacle']);
                    $html .= 'Spectacle supprimé de la soirée';
                }
            }
        }

        return $html;
    }
}
