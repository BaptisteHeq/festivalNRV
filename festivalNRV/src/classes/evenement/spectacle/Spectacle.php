<?php

namespace iutnc\nrv\evenement\spectacle;



use iutnc\nrv\repository\NrvRepository;

class Spectacle
{
    private string $nom;
    private string $date;
    private int $spectacleID=0;
    private int $soireeID;
    private int $styleID;
    private string $horaire="";
    private array $img=[];
    private string $description="";
    private array $video=[];
    private array $artistes=[];
    private int $duree=0;

    public function __construct(string $nom, string $date, int $styleID, string $horaire, array $img, string $description, array $video, array $artistes, int $duree)
    {
        $this->nom = $nom;
        $this->date = $date;
        $this->styleID = $styleID;
        $this->horaire = $horaire;
        $this->img = $img;
        $this->description = $description;
        $this->video = $video;
        $this->artistes = $artistes;
        $this->duree = $duree;
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

    public function getHoraire(): string
    {
        return $this->horaire;
    }

    public function getImg(): array
    {
        return $this->img;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getArtistes(): array
    {
        return $this->artistes;
    }

    public function getVideo(): array
    {
        return $this->video;
    }

    public function getDuree(): int
    {
        return $this->duree;
    }




}