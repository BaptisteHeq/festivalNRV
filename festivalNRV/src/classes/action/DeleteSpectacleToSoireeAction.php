<?php

namespace iutnc\nrv\action;

use iutnc\nrv\evenement\soiree\Soiree;
use iutnc\nrv\repository\NrvRepository;

class DeleteSpectacleToSoireeAction extends Action
{
    public function execute(): string
    {
        $html = '<p><b>Suppression du spectacle de la soirée</b></p><br>';

        if (!isset($_SESSION['spectacle']) || !isset($_SESSION['soiree'])) {
            $html .= 'spectacle introuvable';
        } else {
            $sp = unserialize($_SESSION['spectacle']);
            $soiree = unserialize($_SESSION['soiree']);
            $r = NrvRepository::getInstance();
            $r->deleteSpectacleFromSoiree( $soiree->getSoireeID(), $sp->getSpectacleID());
            $soiree->deleteSpectacle($sp->getSpectacleID());
            $_SESSION['soiree'] = serialize($soiree);
            unset($_SESSION['spectacle']);
            $html .= 'spectacle supprimé de la soirée en session';
        }
        return $html;
    }
}
