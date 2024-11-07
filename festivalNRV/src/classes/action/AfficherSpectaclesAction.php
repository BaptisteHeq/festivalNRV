<?php

namespace iutnc\nrv\action;

use iutnc\nrv\evenement\programme\Programme;
use iutnc\nrv\evenement\spectacle\Spectacle;
use iutnc\nrv\renderer\Renderer;
use iutnc\nrv\renderer\SpectacleRenderer;
use iutnc\nrv\repository\NrvRepository;

class AfficherSpectaclesAction extends Action
{

    public function execute(): string
    {
        $html = '<p><b>Affichage de la liste de tous les spectacles</b></p><br>';

        $r  =  NrvRepository::getInstance();
        $s= $r->getSpectacles();
        foreach($s as $sp){
            $renderer = new SpectacleRenderer($sp);
            $html .= $renderer->render(Renderer::DETAIL);
        }
        return $html;
    }
}

