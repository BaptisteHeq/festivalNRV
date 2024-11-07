<?php

namespace iutnc\nrv\evenement\soiree;

use iutnc\nrv\evenement\programme\Programme;
use iutnc\nrv\evenement\spectacle\Spectacle;
use iutnc\nrv\repository\NrvRepository;

class Soiree
{
    private int $soireeID;
    private string $date;
    private int $lieuID;
    private string $horaire;
    private string $thematique;
    private float $prix;
    private string $nom;
    private array $spectacles=[];

    public function __construct(int $soireeID, string $date, int $lieuID, string $horaire, string $thematique, float $prix, string $nom)
    {
        $this->soireeID = $soireeID;
        $this->date = $date;
        $this->lieuID = $lieuID;
        $this->horaire = $horaire;
        $this->thematique = $thematique;
        $this->prix = $prix;
        $this->nom = $nom;
    }


    public function addSpectacle(Spectacle $spectacle): void
    {
        $this->spectacles[] = $spectacle;
    }

    //supprimer spectacle
    public function deleteSpectacleDeSoiree(int $spectacleID): void
    {
        $newSpectacles = [];


        foreach ($this->spectacles as $sp){
            if ($sp->getSpectacleID() != $spectacleID){
                $newSpectacles[] = $sp;
                echo 'id dans la boucle'.$sp->getSpectacleID();
            }
        }
        $this->spectacles = $newSpectacles;
    }

    public function getSoireeID(): int
    {
        return $this->soireeID;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getLieu(): string
    {
        $r = NrvRepository::getInstance();
        return $r->getLieuByID($this->lieuID);

    }

    public function getPrix(): float
    {
        return $this->prix;
    }

    public function getSpectacles(): array
    {
        return $this->spectacles;
    }

    public function getThematique(): string
    {
        return $this->thematique;
    }

    public function getHoraire(): string
    {
        return $this->horaire;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getLieuID(): int
    {
        return $this->lieuID;
    }

    //supprimer tous les spectacles
    public function deleteAllSpectacles(): void
    {
        $this->spectacles = [];
    }





}