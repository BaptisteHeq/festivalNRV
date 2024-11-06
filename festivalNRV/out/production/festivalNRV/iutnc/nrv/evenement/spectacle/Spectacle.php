<?php

namespace iutnc\nrv\evenement\spectacle;

class Spectacle
{
    private string $nom;
    private string $date;
    private string $heure;
    private string $duree;
    private string $description;
    private string $image;

    public function __construct(string $nom, string $date, string $heure, string $duree, string $description, string $image)
    {
        $this->nom = $nom;
        $this->date = $date;
        $this->heure = $heure;
        $this->duree = $duree;
        $this->description = $description;
        $this->image = $image;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getHeure(): string
    {
        return $this->heure;
    }

    public function getDuree(): string
    {
        return $this->duree;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getImage(): string
    {
        return $this->image;
    }
}