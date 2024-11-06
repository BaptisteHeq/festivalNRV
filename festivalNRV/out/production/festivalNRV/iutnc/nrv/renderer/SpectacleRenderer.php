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
            $html .= '<p><b>' . $this->spectacle->getNom() . '</b></p>';
            $html .= '<p>Date: ' . $this->spectacle->getDate() . '</p>';
            $html .= '<p>Style: ' . $this->spectacle->getStyle() . '</p>';
            $html .= '<p>Horaire: ' . $this->spectacle->getHoraire() . '</p>';
            $html .= '<p><img src="' . $this->spectacle->getImg() . '" alt="image spectacle"></p>';
        } else if ($selector == Renderer::DETAIL) {
            /*ffichage détaillé d’un spectacle : titre, artistes, description, style, durée, image(s), extrait audio/vidéo*/
            $html .= '<p><b>' . $this->spectacle->getNom() . '</b></p>';
            $html .= '<p>Artistes: ' . $this->spectacle->getArtistes() . '</p>';
            $html .= '<p>Description: ' . $this->spectacle->getDescription() . '</p>';
            $html .= '<p>Style: ' . $this->spectacle->getStyle() . '</p>';
            $html .= '<p>Durée: ' . $this->spectacle->getDuree() . '</p>';
            $html .= '<p><img src="./media/' . $this->spectacle->getImg() . '" alt="image spectacle" width="100px"></p>';
            $html .= '<p><video controls><source src="./media/' . $this->spectacle->getVideo() . '" type="video/mp4"></video></p>';

        }
        //ajouter lien pour supprimer spectacle
        $html .= '<a href="index.php?action=delete-spectacle">Supprimer spectacle</a>';
        return $html;
    }
}