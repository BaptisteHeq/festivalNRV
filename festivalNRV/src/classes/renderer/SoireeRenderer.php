<?php

namespace iutnc\nrv\renderer;

use iutnc\nrv\evenement\soiree\Soiree;
use iutnc\nrv\evenement\spectacle\Spectacle;

class SoireeRenderer implements Renderer
{

    private Soiree $soiree;

    public function __construct($soiree)
    {
        $this->soiree = $soiree;
    }


    /* Affichage du détail d’une soirée : nom de la soirée, thématique, date et horaire, lieu,
tarifs, ainsi que la liste des spectacles : titre, artistes, description, style de musique, vidéo,*/
    public function render(int $selector): string
    {


        $html = "";
        if ($selector == Renderer::COMPACT) {
            $html .= '<p><b>' . $this->soiree->getNom() . '</b></p>';
            $html .= '<p>Thématique: ' . $this->soiree->getThematique() . '</p>';
            $html .= '<p>Date: ' . $this->soiree->getDate() . '</p>';
            $html .= '<p>Horaire: ' . $this->soiree->getHoraire() . '</p>';
            $html .= '<p>Lieu: ' . $this->soiree->getLieu() . '</p>';
            $html .= '<p>Tarifs: ' . $this->soiree->getPrix() . '</p>';
            $html .= '<p><b>Liste des spectacles</b></p>';
            $html .= '<ul>';
            /*En cliquant sur un spectacle dans la liste, le détail de la soirée correspondante est affiché, */
            foreach ($this->soiree->getSpectacles() as $spectacle) {
                $html .= '<a href="?action=spectacle-detail">';
                $html .= '<li>' . $spectacle->getNom() . '</li>';
                $html .= '<p>Artistes: ' . $spectacle->getArtistes() . '</p>';
                $html .= '<p>Description: ' . $spectacle->getDescription() . '</p>';
                $html .= '<p>Style: ' . $spectacle->getStyle() . '</p>';
                $html .= '<p><video controls><source src="./media/' . $spectacle->getVideo() . '" type="video/mp4"></video></p>';
                $html .= '</a>';
            }

            $html .= '</ul>';
        }


        //ajouter lien pour supprimer soirée
        $html .= '<a href="index.php?action=delete-soiree">Supprimer soirée</a>';
        //ajouter lien pour ajouter spectacle à la soirée
        return $html;
    }

}