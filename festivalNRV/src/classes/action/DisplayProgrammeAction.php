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
        if (!AuthzProvider::isAuthorized($this->role))
            return '<div class="alert alert-danger">Vous n\'êtes pas autorisé à accéder à cette page</div>';

        $html = '<div class="container mt-4">';
        $html .= '<h3 class="mb-4">Programme des soirées</h3>';

        $r = NrvRepository::getInstance();
        $soirees = $r->getSoirees();

        if (empty($soirees)) {
            $html .= '<div class="alert alert-warning">Aucune soirée trouvée.</div>';
        } else {
            foreach ($soirees as $s) {
                $spectacles = $r->getSpectaclesByIDsoiree($s->getSoireeID());
                foreach ($spectacles as $sp) {
                    $s->addSpectacle($sp);
                }
                $re = new SoireeRenderer($s);
                $html .= '<div class="card mb-3">' . $re->render(Renderer::COMPACT) . '</div>';
            }
        }

        $html .= '</div>';
        return $html;
    }
}