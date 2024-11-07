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
        if(!AuthzProvider::isAuthorized($this->role))
            return "Vous n'êtes pas autorisé à accéder à cette page";

        $html = "";
        if (! isset($_SESSION['soiree'])) {
            $html .= 'soiree introuvable';
        } else
        {
            $soiree = unserialize($_SESSION['soiree']);
            $re = new SoireeRenderer($soiree);
            $html .= $re->render(Renderer::COMPACT);

        }
        return $html;

    }
}



