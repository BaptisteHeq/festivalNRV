<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthzProvider;
use iutnc\nrv\evenement\soiree\Soiree;
use iutnc\nrv\evenement\spectacle\Spectacle;
use iutnc\nrv\renderer\Renderer;
use iutnc\nrv\renderer\SoireeRenderer;
use iutnc\nrv\renderer\SpectacleRenderer;
use iutnc\nrv\repository\NrvRepository;


class DisplaySoireeAction extends Action
{
    public function __construct()
    {
        parent::__construct();
        $this->role = 1;
    }


    public function execute(): string
    {
        if (!AuthzProvider::isAuthorized($this->role))
            return '<div class="alert alert-danger">Vous n\'êtes pas autorisé à accéder à cette page</div>';

        $html = '<div class="container mt-4">';
        $html .= '<h3 class="mb-4">Détails de la soirée</h3>';

        if (!isset($_SESSION['soiree'])) {
            $html .= '<div class="alert alert-warning">Soirée introuvable</div>';
        } else {
            $soiree = unserialize($_SESSION['soiree']);
            $re = new SoireeRenderer($soiree);
            $html .= '<div class="card p-3">' . $re->render(Renderer::COMPACT) . '</div>';
        }

        $html .= '</div>';
        return $html;
    }
}



