<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthzProvider;
use iutnc\nrv\evenement\soiree\Soiree;
use iutnc\nrv\evenement\spectacle\Spectacle;
use iutnc\nrv\repository\NrvRepository;

class AddSoireeAction extends Action
{


    public function __construct()
    {
        $this->role = 50;
    }

    public function execute(): string
    {
        if(!AuthzProvider::isAuthorized($this->role))
            return "Vous n'êtes pas autorisé à accéder à cette page";

        $html = "";
        if ($_SERVER['REQUEST_METHOD'] === 'GET'){
            $html .= '<form method="post">';
            $html .= '<h2>Ajouter une soirée</h2>';
            $html .= '<p>Remplissez les champs suivants pour ajouter une soirée</p>';
            $html .= '<label for="nom">Nom de la soirée</label>';
            $html .= '<input type="text" name="nom" id="nom" required>';
            $html .= '<label for="date">Date de la soirée</label>';
            $html .= '<input type="date" name="date" id="date" required>';
            $html .= '<label for="lieu">Lieu de la soirée</label>';
            $html .= '<select name="lieu" id="lieu" required>';
            $r = NrvRepository::getInstance();
            $lieux = $r->getLieux();
            foreach ($lieux as $lieu){
                $html .= '<option value="' . $lieu['LieuID'] . '">' . $lieu['NomLieu'] . '</option>';
            }
            $html .= '</select>';
            $html .= '<label for="horaire">Horaire de la soirée</label>';
            $html .= '<input type="time" name="horaire" id="horaire" required>';
            $html .= '<label for="thematique">Thématique de la soirée</label>';
            $html .= '<input type="text" name="thematique" id="thematique" required>';
            $html .= '<label for="tarifs">Tarifs de la soirée</label>';
            $html .= '<input type="number" name="tarifs" id="tarifs" required>';
            $html .= '<input type="submit" value="Ajouter">';
            $html .= '</form>';

        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $nom = filter_var($_POST['nom'], FILTER_SANITIZE_STRING);
            $date = filter_var($_POST['date'], FILTER_SANITIZE_STRING);
            $lieu = filter_var($_POST['lieu'], FILTER_SANITIZE_STRING);
            $horaire = filter_var($_POST['horaire'], FILTER_SANITIZE_STRING);
            $thematique = filter_var($_POST['thematique'], FILTER_SANITIZE_STRING);
            $tarifs = filter_var($_POST['tarifs'], FILTER_SANITIZE_STRING);

            $r = NrvRepository::getInstance();
            $id = $r->addSoiree($date,$lieu,$horaire,$thematique,$tarifs,$nom);

            $soiree = new Soiree($id,$date,$lieu,$horaire,$thematique,$tarifs,$nom);

            $_SESSION['soiree'] = serialize($soiree);

            $html .= 'Soirée ajoutée';
        }

        return $html;
    }
}