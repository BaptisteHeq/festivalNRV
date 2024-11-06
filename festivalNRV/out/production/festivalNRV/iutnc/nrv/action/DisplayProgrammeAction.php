<?php

namespace iutnc\nrv\action;

use iutnc\nrv\evenement\soiree\Soiree;
use iutnc\nrv\evenement\spectacle\Spectacle;
use iutnc\nrv\renderer\Renderer;
use iutnc\nrv\renderer\SpectacleRenderer;
use iutnc\nrv\repository\NrvRepository;

class DisplayProgrammeAction extends Action
{

    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        $html = "";
        $r = NrvRepository::getInstance();
        $soirees = $r->getSoirees();
        $html .= '<p><b>Affichage des soirees</b></p><br>';
        foreach ($soirees as $s){

            $soiree= new Soiree($s['SoireeID'],$s['DateSoiree'],$s['LieuID'],$s['horaire'],$s['thematique'],$s['tarifs']);
            $html .= '<h2> Le ' . $soiree->getDate() . '</h2>';
            $html .= '<p> à ' . $r->getLieuByID($soiree->getLieuID()) . '</p>';
            $html .= '<p>Spectacles : </p>';
            $spectacles = $r->getSpectaclesByID($soiree->getSoireeID());
            foreach ($spectacles as $spectacle){
                $sp = new Spectacle($spectacle['NomSpectacle'],$spectacle['DateSpectacle'],$spectacle['StyleID'],$spectacle['horaire'],$spectacle['image'],$spectacle['description'],$spectacle['video'],$spectacle['artistes']);
                $re = new SpectacleRenderer($sp);
                $html .= $re->render(Renderer::COMPACT);
            }
        }
        return $html;
    }
}