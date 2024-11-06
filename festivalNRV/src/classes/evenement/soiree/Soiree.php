<?php

namespace iutnc\nrv\evenement\soiree;

use iutnc\nrv\evenement\programme\Programme;
use iutnc\nrv\evenement\spectacle\Spectacle;

class Soiree
{
    private int $soireeID;
    private string $date;
    private int $lieuID;
    private array $spectacles;

    public function __construct(int $soireeID, string $date, int $lieuID)
    {
        $this->soireeID = $soireeID;
        $this->date = $date;
        $this->lieuID = $lieuID;
    }

    public function addSpectacle(Spectacle $spectacle): void
    {
        $this->spectacles[] = $spectacle;
    }


}