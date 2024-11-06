<?php

namespace iutnc\nrv\evenement\programme;

use iutnc\nrv\evenement\spectacle\Spectacle;

class Programme
{
    private array $spectacles;

    public function __construct()
    {
        $this->spectacles = [];
    }

    public function addSpectacle(Spectacle $spectacle): void
    {
        $this->spectacles[] = $spectacle;
    }

    public function getSpectacles(): array
    {
        return $this->spectacles;
    }
}