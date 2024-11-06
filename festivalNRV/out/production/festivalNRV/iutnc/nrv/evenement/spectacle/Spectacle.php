<?php

namespace iutnc\nrv\evenement\spectacle;



use iutnc\nrv\repository\NrvRepository;

class Spectacle
{
    private string $nom;
    private string $date;
    private int $spectacleID;
    private int $soireeID;
    private int $styleID;

    public function __construct(string $nom, string $date, int $styleID)
    {
        $this->nom = $nom;
        $this->date = $date;
        $this->styleID = $styleID;
    }


    public function getNom(): string
    {
        return $this->nom;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getStyleID(): int
    {
        return $this->styleID;
    }

    public function getSpectacleID(): int
    {
        return $this->spectacleID;
    }

    public function getSoireeID(): int
    {
        return $this->soireeID;
    }

    public function setSpectacleID(int $spectacleID): void
    {
        $this->spectacleID = $spectacleID;
    }

    public function setSoireeID(int $soireeID): void
    {
        $this->soireeID = $soireeID;
    }

    /* renvoie le nom du style du spectacle en utilisant getStyleById*/
    public function getStyle(): string
    {
        $r = NrvRepository::getInstance();
        return $r->getStylesByID($this->styleID);
    }


}