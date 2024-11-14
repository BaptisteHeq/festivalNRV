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
        if (!AuthzProvider::isAuthorized($this->role))
            return '<div class="alert alert-danger">Vous n\'êtes pas autorisé à accéder à cette page</div>';

        $html = "";
        if ($_SERVER['REQUEST_METHOD'] === 'GET'){
            $html .= <<<HTML
<form method="post">
    <h2>Ajouter une soirée</h2>
    <p>Remplissez les champs suivants pour ajouter une soirée</p>

    <div class="form-group">
        <label for="nom">Nom de la soirée</label>
        <input type="text" name="nom" id="nom" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="date">Date de la soirée</label>
        <input type="date" name="date" id="date" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="lieu">Lieu de la soirée</label>
        <select name="lieu" id="lieu" class="form-control" required>
HTML;

            $r = NrvRepository::getInstance();
            $lieux = $r->getLieux();
            foreach ($lieux as $lieu) {
                $html .= '<option value="' . $lieu['lieuID'] . '">' . $lieu['nomLieu'] . '</option>';
            }

            $html .= <<<HTML
        </select>
    </div>

    <div class="form-group">
        <label for="horaire">Horaire de la soirée</label>
        <input type="time" name="horaire" id="horaire" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="thematique">Thématique de la soirée</label>
        <input type="text" name="thematique" id="thematique" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="tarifs">Tarifs de la soirée</label>
        <input type="number" name="tarifs" id="tarifs" class="form-control" required>
    </div>

    <input type="submit" value="Ajouter" class="btn btn-success">
</form>
HTML;


        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $nom = filter_var($_POST['nom'], FILTER_SANITIZE_STRING);
            $date = filter_var($_POST['date'], FILTER_SANITIZE_STRING);
            $lieu = filter_var($_POST['lieu'], FILTER_SANITIZE_STRING);
            $horaire = filter_var($_POST['horaire'], FILTER_SANITIZE_STRING);
            $thematique = filter_var($_POST['thematique'], FILTER_SANITIZE_STRING);
            $tarifs = filter_var($_POST['tarifs'], FILTER_SANITIZE_STRING);

            $r = NrvRepository::getInstance();
            $id = $r->addSoiree($date,$lieu,$horaire,$thematique,$tarifs,$nom);

            $soiree = new Soiree($date,$lieu,$horaire,$thematique,$tarifs,$nom);
            $soiree->setSoireeID($id);

            $_SESSION['soiree'] = serialize($soiree);

            $html .= 'Soirée ajoutée';
        }

        return $html;
    }
}