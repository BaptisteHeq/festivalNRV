<?php

namespace iutnc\nrv\renderer;

use iutnc\nrv\evenement\soiree\Soiree;
use iutnc\nrv\evenement\spectacle\Spectacle;
use iutnc\nrv\repository\NrvRepository;

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
            $html .= '<p><h3>' . $this->soiree->getNom() . '</h3></p>';
            $html .= '<p>Thématique: ' . $this->soiree->getThematique() . '</p>';
            $html .= '<p>Date: ' . $this->soiree->getDate() . '</p>';
            $html .= '<p>Horaire: ' . $this->soiree->getHoraire() . '</p>';
            $html .= '<p>Lieu: ' . $this->soiree->getLieu() . '</p>';
            $html .= '<p>Tarifs: ' . $this->soiree->getPrix() . '</p>';
            $html .= '<p><b>Liste des spectacles</b></p>';
            $html .= '<ul>';
            /*En cliquant sur un spectacle dans la liste, le détail de la soirée correspondante est affiché, */
            $r = NrvRepository::getInstance();
            $spectacles = $r->getSpectaclesByIDsoiree($this->soiree->getSoireeID());
            foreach ($spectacles as $sp) {
                $html .= '<a href="?action=spectacle-detail">';
                $re = new SpectacleRenderer($sp);
                $html .= '<li>' . $re->render(Renderer::COMPACT) . '</li>';
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