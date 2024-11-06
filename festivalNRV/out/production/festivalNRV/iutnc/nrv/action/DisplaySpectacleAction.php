<?php

namespace iutnc\nrv\action;

use iutnc\nrv\evenement\programme\Programme;
use iutnc\nrv\evenement\spectacle\Spectacle;
use iutnc\nrv\renderer\Renderer;
use iutnc\nrv\renderer\SpectacleRenderer;

class DisplaySpectacleAction extends Action
{

    public function execute(): string
    {
        $html = '<p><b>Affichage du spectacle en session</b></p><br>';

        if (! isset($_SESSION['spectacle'])) {
            $html .= 'spectacle introuvable';
        } else
        {
            $pl = unserialize($_SESSION['spectacle']);
            $r = new SpectacleRenderer($pl);
            $html .= $r->render(Renderer::COMPACT);
        }


        return $html;
    }
}