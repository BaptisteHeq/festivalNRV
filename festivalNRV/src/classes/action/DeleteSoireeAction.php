<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthzProvider;
use iutnc\nrv\evenement\soiree\Soiree;
use iutnc\nrv\repository\NrvRepository;

class DeleteSoireeAction extends Action
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