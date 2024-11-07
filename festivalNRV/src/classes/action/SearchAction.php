<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthzProvider;
use iutnc\nrv\evenement\programme\Programme;
use iutnc\nrv\evenement\spectacle\Spectacle;
use iutnc\nrv\renderer\Renderer;
use iutnc\nrv\renderer\SpectacleRenderer;
use iutnc\nrv\repository\NrvRepository;

class SearchAction extends Action
{
    private string $nomSp;

    public function __construct(string $nomSp)
    {
        parent::__construct();
        $this->role = 1;
        $this->nomSp = $nomSp;
    }

    public function execute(): string
    {
        $r = NrvRepository::getInstance();
        $results = $r->rechercherSpectacles($this->nomSp);

        if (empty($results)) {
            return "Aucun spectacle trouvé pour '$this->nomSp'.";
        }

        $html = '<p><b>Spectacles trouvés: </b></p><br>';
        foreach ($results as $spectacle) {
            $renderer = new SpectacleRenderer($spectacle);
            $html .= $renderer->render(Renderer::DETAIL);
        }

        return $html;
    }
}
