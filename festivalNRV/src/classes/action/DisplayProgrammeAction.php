<?php

namespace iutnc\nrv\action;

use iutnc\nrv\evenement\soiree\Soiree;
use iutnc\nrv\evenement\spectacle\Spectacle;
use iutnc\nrv\renderer\Renderer;
use iutnc\nrv\renderer\SoireeRenderer;
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

            $soiree= new Soiree($s['SoireeID'],$s['DateSoiree'],$s['LieuID'],$s['horaire'],$s['thematique'],$s['tarifs'],$s['nomSoiree']);
            //ajout des spectacles
            $spectacles = $r->getSpectaclesByIDsoiree($soiree->getSoireeID());
            foreach ($spectacles as $sp){
                $spectacle = new Spectacle($sp['SpectacleID'],$sp['DateSpectacle'],$sp['StyleID'],$sp['horaire'],$sp['image'],$sp['description'],$sp['video'],$sp['artistes'],$sp['duree']);
                $soiree->addSpectacle($spectacle);
            }
            $re = new SoireeRenderer($soiree);
            $html .= $re->render(Renderer::COMPACT);
        }
        return $html;
    }
}