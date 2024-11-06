<?php

namespace iutnc\nrv\renderer;

use iutnc\nrv\evenement\programme\Programme;
use iutnc\nrv\evenement\spectacle\Spectacle;

class SpectacleRenderer implements Renderer
{
    private Spectacle $spectacle;

    public function __construct(Spectacle $spectacle)
    {
        $this->spectacle = $spectacle;
    }

    public function render(int $selector): string
    {
        $html = "";
        if ($selector == Renderer::COMPACT) {
            $html .= '<p><b>Spectacle</b></p>';
            $html .= '<p>Nom: ' . $this->spectacle->getNom() . '</p>';
            $html .= '<p>Date: ' . $this->spectacle->getDate() . '</p>';
            $html .= '<p>Style: ' . $this->spectacle->getStyle() . '</p>';
        }
        return $html;
    }
}