<?php

namespace iutnc\nrv\evenement\soiree;

use iutnc\nrv\evenement\programme\Programme;
use iutnc\nrv\evenement\spectacle\Spectacle;

class Soiree
{
    private int $soireeID;
    private string $date;
    private int $lieuID;
    private string $horaire;
    private string $thematique;
    private float $prix;
    private array $spectacles;

    public function __construct(int $soireeID, string $date, int $lieuID, string $horaire, string $thematique, float $prix)
    {
        $this->soireeID = $soireeID;
        $this->date = $date;
        $this->lieuID = $lieuID;
        $this->horaire = $horaire;
        $this->thematique = $thematique;
        $this->prix = $prix;
    }


    public function addSpectacle(Spectacle $spectacle): void
    {
        $this->spectacles[] = $spectacle;
    }

    public function getSoireeID(): int
    {
        return $this->soireeID;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getLieuID(): int
    {
        return $this->lieuID;
    }





}