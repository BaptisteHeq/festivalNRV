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
        if(!AuthzProvider::isAuthorized($this->role))
            return "Vous n'êtes pas autorisé à accéder à cette page";
        $html = '<p><b>Suppression du spectacle de la soirée</b></p><br>';

        if (!isset($_SESSION['spectacle']) || !isset($_SESSION['soiree'])) {
            $html .= 'spectacle introuvable';
        } else {
            $sp = unserialize($_SESSION['spectacle']);
            $soiree = unserialize($_SESSION['soiree']);
            $r = NrvRepository::getInstance();
            $r->deleteSpectacleFromSoiree( $soiree->getSoireeID(), $sp->getSpectacleID());
            $_SESSION['soiree'] = serialize($soiree);
            $html .= 'spectacle supprimé de la soirée';
        }
        return $html;
    }
}
