<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthzProvider;
use iutnc\nrv\evenement\programme\Programme;
use iutnc\nrv\evenement\spectacle\Spectacle;
use iutnc\nrv\renderer\Renderer;
use iutnc\nrv\renderer\SpectacleRenderer;

class DisplaySpectacleAction extends Action
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
        $html .= '<h3 class="mb-4">Détails du spectacle</h3>';

        if (!isset($_SESSION['spectacle'])) {
            $html .= '<div class="alert alert-warning">Spectacle introuvable</div>';
        } else {
            $pl = unserialize($_SESSION['spectacle']);
            $r = new SpectacleRenderer($pl);
            $html .= '<div class="card p-3">' . $r->render(Renderer::COMPACT) . '</div>';
        }

        $html .= '</div>';
        return $html;
    }
}