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
            //afficher les images
            $html .= '<p>Images: ';
            foreach ($this->spectacle->getImg() as $img) {
                $html .= '<img src="./media/' . $img . '" alt="image spectacle" width="100px">';
            }
            $html .= '</p>';
        } else if ($selector == Renderer::DETAIL) {
            /*ffichage détaillé d’un spectacle : titre, artistes, description, style, durée, image(s), extrait audio/vidéo*/
            $html .= '<p><b>' . $this->spectacle->getNom() . '</b></p>';
            //afficher les artistes
            $html .= '<p>Artistes: ';
            foreach ($this->spectacle->getArtistes() as $artiste) {
                $html .= $artiste . ', ';
            }
            $html .= '</p>';
            $html .= '<p>Description: ' . $this->spectacle->getDescription() . '</p>';
            $html .= '<p>Style: ' . $this->spectacle->getStyle() . '</p>';
            $html .= '<p>Durée: ' . $this->spectacle->getDuree() . '</p>';
            //afficher les images
            $html .= '<p>Images: ';
            foreach ($this->spectacle->getImg() as $img) {
                $html .= '<img src="./media/' . $img . '" alt="image spectacle" width="100px">';
            }
            $html .= '</p>';
            //afficher les vidéos
            $html .= '<p>Vidéos: ';
            foreach ($this->spectacle->getVideo() as $video) {
                $html .= '<video controls><source src="./media/' . $video . '" type="video/mp4"></video>';
            }
            $html .= '</p>';

        }
        //ajouter lien pour supprimer spectacle
        $html .= '<ul>';
        $html .= '<li><a href="index.php?action=delete-spectacle&idSpectacle=' . $this->spectacle->getSpectacleID() . '">Supprimer spectacle</a></li>';
        $html .= '<li><a href="index.php?action=update-spectacle&idSpectacle=' . $this->spectacle->getSpectacleID() . '">Modifier spectacle</a></li>';
        $html .= '<li><a href="index.php?action=add-spec-to-soiree">Ajouter spectacle à une soirée</a></li>';
        $html .= '<li><a href="index.php?action=delete-spectacle-to-soiree">Supprimer spectacle à une soirée</a></li>';
        $html .= '</ul>';
        return $html;
    }
}