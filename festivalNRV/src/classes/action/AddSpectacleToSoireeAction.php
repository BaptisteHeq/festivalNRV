<?php

namespace iutnc\nrv\action;

use iutnc\nrv\evenement\soiree\Soiree;
use iutnc\nrv\evenement\spectacle\Spectacle;
use iutnc\nrv\repository\NrvRepository;

class AddSpectacleToSoireeAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    public function execute(): string
    {
        $html = '<p><b>Ajout d\'un spectacle à la soirée en session</b></p><br>';

        /* AJOUTER UN SPECTACLE A UNE SOIREE */
        //liste déroulante des spectacles
        //liste déroulante des soirees
        //bouton ajouter
        //affichage de la soirée avec le nouveau spectacle

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $r = NrvRepository::getInstance();
                $spectacles = $r->getSpectaclesSansSoiree();
                $html .= '<form action="?action=add-spec-to-soiree" method="post">';
                $html .= '<h2>Ajouter un spectacle à une soirée</h2>';

                //liste déroulante des spectacles
                $html .= '<select name="spectacle">';
                foreach ($spectacles as $spectacle) {
                    $html .= '<option value="' . $spectacle['SpectacleID'] . '">' . $spectacle['NomSpectacle'] . '</option>';
                }
                $html .= '</select>';

                //liste déroulante des soirees
                $html .= '<select name="soiree">';
                $soirees = $r->getSoirees();
                foreach ($soirees as $soiree) {
                    $html .= '<option value="' . $soiree['SoireeID'] . '">' . $soiree['nomSoiree'] . '</option>';
                }
                $html .= '</select>';


                $html .= '<input type="submit" value="Ajouter">';
                $html .= '</form>';
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST'){
                if (isset($_POST['spectacle']) && isset($_POST['soiree'])) {
                    $r = NrvRepository::getInstance();
                    $r->addSpectacleToSoiree( $_POST['soiree'] , $_POST['spectacle']);
                    $html .= 'Spectacle ajouté à la soirée';

                    //mise à jour de la soirée en session
                    $soiree = $r->getSoireeByID($_POST['soiree']);
                    $so = new Soiree($soiree['SoireeID'],$soiree['DateSoiree'],$soiree['LieuID'],$soiree['horaire'],$soiree['thematique'],$soiree['tarifs'],$soiree['nomSoiree']);
                    $sp = $r->getSpectacleByID($_POST['spectacle']);
                    $so -> addSpectacle($sp);

                    $_SESSION['soiree'] = serialize($so);

                }
            }
        return $html;
    }

}
