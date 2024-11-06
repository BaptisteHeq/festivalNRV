<?php

namespace iutnc\nrv\action;

class DisplayProgrammeAction extends Action
{

    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        $html = "";
        if (!isset($_SESSION['programme'])) {
            $html .= 'Connectez-vous pour voir les programmes!';
        } else {
            $p = unserialize($_SESSION['programme']);

            /* Afficher la liste des programmes */
            $html .= '<b>Liste des Programmes</b>';



        }
        return $html;
    }
}