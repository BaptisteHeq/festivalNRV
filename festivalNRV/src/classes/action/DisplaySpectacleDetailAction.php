<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthzProvider;
use iutnc\nrv\evenement\programme\Programme;
use iutnc\nrv\evenement\spectacle\Spectacle;
use iutnc\nrv\renderer\Renderer;
use iutnc\nrv\renderer\SpectacleRenderer;
use iutnc\nrv\repository\NrvRepository;

class DisplaySpectacleDetailAction extends Action
{
    public function __construct()
    {
        parent::__construct();
        $this->role = 1;
    }

    public function execute(): string
    {
        if(!AuthzProvider::isAuthorized($this->role))
            return "Vous n'êtes pas autorisé à accéder à cette page";

        $html = '<p><b>Affichage du spectacle en session</b></p><br>';

        if (! isset($_GET['idSpectacle'])) {
            $html .= 'spectacle introuvable';
        } else
        {
            $r = NrvRepository::getInstance();
            $pl = $r->getSpectacleById($_GET['idSpectacle']);
            $r = new SpectacleRenderer($pl);
            $html .= $r->render(Renderer::DETAIL);
        }


        return $html;
    }
}