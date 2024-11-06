<?php

namespace iutnc\nrv\action;

use iutnc\nrv\evenement\soiree\Soiree;
use iutnc\nrv\repository\NrvRepository;

class DeleteSoireeAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        $html = '<p><b>Suppression de la soirée en session</b></p><br>';

        if (! isset($_SESSION['soiree'])) {
            $html .= 'soiree introuvable';
        } else
        {
            $soiree = unserialize($_SESSION['soiree']);
            $r= NrvRepository::getInstance();
            $r->deleteSoiree($soiree->getSoireeID());
            unset($_SESSION['soiree']);
            $html .= 'soiree supprimée';
        }

        return $html;

    }
}