<?php

namespace iutnc\nrv\evenement\programme;

use iutnc\nrv\evenement\soiree\Soiree;
use iutnc\nrv\evenement\spectacle\Spectacle;

class Programme
{
    private array $soirees;

    public function __construct()
    {
        $this->soirees = [];
    }

    public function addSoiree(Soiree $soiree): void
    {
        $this->soirees[] = $soiree;
    }

    public function getSpectacles(): array
    {
        return $this->soirees;
    }
}