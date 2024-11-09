<?php

namespace iutnc\nrv\action;


use iutnc\nrv\auth\AuthzProvider;
use iutnc\nrv\repository\NrvRepository;
use iutnc\nrv\evenement\soiree\Soiree;

class DeleteSpectacleAction extends Action
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

        $html = '<p><b>Suppression du spectacle</b></p><br>';

        if (!isset($_GET['idSpectacle'])) {
            $html .= 'spectacle introuvable';
        } else {
            $r = NrvRepository::getInstance();
            $sp = $r->getSpectacleById($_GET['idSpectacle']);
            $r->deleteSpectacle($sp->getSpectacleID());


            unset($_SESSION['spectacle']);
            $html .= 'spectacle supprimé';
        }
        return $html;
    }
}