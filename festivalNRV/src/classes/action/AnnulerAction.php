<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NrvRepository;

class AnnulerAction extends Action
{

    public function execute(): string
    {
        $html = "";
        if (! isset($_GET['idSpectacle'])) {
            $html .= 'spectacle introuvable';
        } else
        {
            $idSpectacle = $_GET['idSpectacle'];
            $r = NrvRepository::getInstance();
            $spectacle = $r->getSpectacleByID($idSpectacle);
            $r->AnnulerSpectacle($idSpectacle);
            $spectacle->setEstAnnule(1);
            $html .= 'Le spectacle a été annulée';
        }
        return $html;

    }
}