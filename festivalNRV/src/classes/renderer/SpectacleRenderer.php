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

        //Utilisation du mode COMPACT
        if ($selector == Renderer::COMPACT) {
            $html .= '<div class="container my-4">';
            $html .= '<div class="card">';
            $html .= '<div class="card-body">';

            // Titre du spectacle
            $html .= '<h5 class="card-title">' . htmlspecialchars($this->spectacle->getNom()) . '</h5>';
            $html .= '<p class="card-text"><strong>Date :</strong> ' . htmlspecialchars($this->spectacle->getDate()) . '</p>';
            $html .= '<p class="card-text"><strong>Style :</strong> ' . htmlspecialchars($this->spectacle->getStyle()) . '</p>';

            //Annulation ou horaire et lieu
            if ($this->spectacle->getEstAnnule() == 1) {
                $html .= '<p class="card-text text-danger"><strong>Annulé</strong></p>';
            } else {
                $html .= '<p class="card-text"><strong>Horaire :</strong> ' . htmlspecialchars($this->spectacle->getHoraire()) . '</p>';
                $html .= '<p class="card-text"><strong>Lieu :</strong> ' . htmlspecialchars($this->spectacle->getLieu()['nomLieu']) . '</p>';
            }

            //Affichage des images
            $html .= '<div class="mt-3"><strong>Images :</strong> ';
            foreach ($this->spectacle->getImg() as $img) {
                $html .= '<img src="./media/' . htmlspecialchars($img) . '" alt="image spectacle" class="img-thumbnail me-2" width="100px">';
            }
            $html .= '</div>';

            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }

        //Utilisation du mode DETAIL
        else if ($selector == Renderer::DETAIL) {
            $html .= '<div class="container my-4">';
            $html .= '<div class="card">';
            $html .= '<div class="card-body">';

            //Titre et artistes
            $html .= '<h5 class="card-title">' . htmlspecialchars($this->spectacle->getNom()) . '</h5>';
            $html .= '<p class="card-text"><strong>Artistes :</strong> ' . implode(', ', array_map('htmlspecialchars', $this->spectacle->getArtistes())) . '</p>';

            //Description et détails
            $html .= '<p class="card-text"><strong>Description :</strong> ' . htmlspecialchars($this->spectacle->getDescription()) . '</p>';
            if ($this->spectacle->getEstAnnule() == 1) {
                $html .= '<p class="card-text text-danger"><strong>Annulé</strong></p>';
            }
            $html .= '<p class="card-text"><strong>Style :</strong> ' . htmlspecialchars($this->spectacle->getStyle()) . '</p>';
            $html .= '<p class="card-text"><strong>Durée :</strong> ' . htmlspecialchars($this->spectacle->getDuree()) .' minutes'. '</p>';
            $html .= '<p class="card-text"><strong>Lieu :</strong> ' . htmlspecialchars($this->spectacle->getLieu()['nomLieu']) . '</p>';

            //Affichage des images
            $html .= '<div class="mt-3"><strong>Images :</strong> ';
            foreach ($this->spectacle->getImg() as $img) {
                $html .= '<img src="./media/' . htmlspecialchars($img) . '" alt="image spectacle" class="img-thumbnail me-2" width="100px">';
            }
            $html .= '</div>';

            //Affichage des vidéos
            $html .= '<div class="mt-3"><strong>Vidéos :</strong> ';
            foreach ($this->spectacle->getVideo() as $video) {
                $html .= '<video controls class="me-2 mt-2" width="200px"><source src="./media/' . htmlspecialchars($video) . '" type="video/mp4"></video>';
            }
            $html .= '</div>';

            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }

        $html .= '<ul class="list-group list-group-flush my-4">';
        $html .= '<li class="list-group-item d-flex flex-wrap gap-2">';

        $html .= '<a href="index.php?action=delete-spectacle&idSpectacle=' . $this->spectacle->getSpectacleID() . '" class="btn btn-danger btn-sm">Supprimer spectacle</a>';
        $html .= '<a href="index.php?action=update-spectacle&idSpectacle=' . $this->spectacle->getSpectacleID() . '" class="btn btn-warning btn-sm">Modifier spectacle</a>';
        $html .= '<a href="index.php?action=annuler&idSpectacle=' . $this->spectacle->getSpectacleID() . '" class="btn btn-secondary btn-sm">Annuler le spectacle</a>';
        $html .= '<a href="index.php?action=add-spec-to-soiree" class="btn btn-success btn-sm">Ajouter spectacle à une soirée</a>';
        $html .= '<a href="index.php?action=delete-spectacle-to-soiree" class="btn btn-danger btn-sm">Supprimer spectacle à une soirée</a>';

        $html .= '</li>';
        $html .= '</ul>';


        return $html;
    }

}