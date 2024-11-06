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
        $spectacle = $this->spectacle;
        $html .= "<h2>" . $spectacle->getNom() . "</h2>";
            $html .= "<p>Date : " . $spectacle->getDate() . "</p>";
            $html .= "<p>Heure : " . $spectacle->getHeure() . "</p>";
            $html .= "<p>Durée : " . $spectacle->getDuree() . "</p>";
            $html .= "<p>Description : " . $spectacle->getDescription() . "</p>";
            $html .= "<img src='" . $spectacle->getImage() . "' alt='Image du spectacle'>";

        return $html;
    }
}