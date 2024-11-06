<?php

namespace iutnc\nrv\action;


use iutnc\nrv\repository\NrvRepository;

class DeleteSpectacleAction extends Action
{

    public function execute(): string
    {
        $html = '<p><b>Suppression du spectacle</b></p><br>';

        if (!isset($_SESSION['spectacle'])) {
            $html .= 'spectacle introuvable';
        } else {
            $sp = unserialize($_SESSION['spectacle']);
            $r = NrvRepository::getInstance();
            $r->deleteSpectacle($sp->getSpectacleID());

            unset($_SESSION['spectacle']);
            $html .= 'spectacle supprimé';
        }
        return $html;
    }
}