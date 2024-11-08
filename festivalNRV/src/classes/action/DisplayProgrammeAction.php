<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthzProvider;
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
        $this->role = 1;
    }

    public function execute(): string
    {
        if(!AuthzProvider::isAuthorized($this->role))
            return "Vous n'êtes pas autorisé à accéder à cette page";

        $html = "";
        $r = NrvRepository::getInstance();
        $soirees = $r->getSoirees();
        $html .= '<p><b>Affichage des soirees</b></p><br>';
        foreach ($soirees as $s){

            //ajout des spectacles
            $spectacles = $r->getSpectaclesByIDsoiree($s->getSoireeID());
            foreach ($spectacles as $sp){
                $s->addSpectacle($sp);
            }
            $re = new SoireeRenderer($s);
            $html .= $re->render(Renderer::COMPACT);
        }
        return $html;
    }
}