<?php

namespace iutnc\nrv\repository;

use Exception;
use PDO;

class NrvRepository
{

    private PDO $pdo;
    private static array $config = [];
    private static ?NrvRepository $instance = null;

    private function __construct(array $config)
    {

        $this->pdo = new PDO(
            $config['dsn'],
            $config['username'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    public static function setConfig($file)
    {
        $conf = parse_ini_file($file);
        self::$config = [
            'dsn' => "{$conf['driver']}:host={$conf['host']};dbname={$conf['database']}",
            'username' => $conf['username'],
            'password' => $conf['password']
        ];
    }

    public static function getInstance(): NrvRepository
    {
        if (is_null(self::$instance)) {
            self::$instance = new self(self::$config);
        }
        return self::$instance;
    }


    /*
    exemple
    public function getListPlaylist(): array
    {
        $sql = "SELECT * FROM playlist";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    */

    public function getStylesByID(int $styleID): string
    {
        $sql = "SELECT NomStyle FROM styles WHERE styleID = :styleID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['styleID' => $styleID]);
        return $stmt->fetchColumn();
    }

    /* la liste des soirées */
    public function getSoirees(): array
    {
        $sql = "SELECT * FROM soirees";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* la liste des spectacles */
    public function getSpectaclesByID(int $soireeID): array
    {
        $sql = "SELECT * FROM spectacles WHERE soireeID = :soireeID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['soireeID' => $soireeID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* le lieu par l'id */
    public function getLieuByID(int $lieuID): string
    {
        $sql = "SELECT NomLieu FROM lieux WHERE lieuID = :lieuID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['lieuID' => $lieuID]);
        return $stmt->fetchColumn();
    }

    /*ajouter un spectacle */
    public function addSpectacle(string $nom, string $date, int $styleID, string $horaire, string $img, string $description, string $video, string $artistes, int $duree): int
    {
        $sql = "INSERT INTO spectacles(NomSpectacle, DateSpectacle, StyleID, horaire, image, description, video, artistes, duree) VALUES ( :nom, :date, :styleID, :horaire, :img, :description, :video, :artistes, :duree)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([ 'nom' => $nom, 'date' => $date, 'styleID' => $styleID, 'horaire' => $horaire, 'img' => $img, 'description' => $description, 'video' => $video, 'artistes' => $artistes, 'duree' => $duree]);
        return $this->pdo->lastInsertId();
    }

    /*supprimer un spectacle */
    public function deleteSpectacle(int $spectacleID): void
    {
        //supprimer les fichier image et video
        $sql = "SELECT image, video FROM spectacles WHERE SpectacleID = :spectacleID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['spectacleID' => $spectacleID]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (is_file('./media/'.$row['image']))
        unlink('./media/'.$row['image']);
        if (is_file('./media/'.$row['video']))
        unlink('./media/'.$row['video']);

        //supprimer les spectacles de la soirée
        $sql = "DELETE FROM soirees_pectacles WHERE SpectacleID = :spectacleID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['spectacleID' => $spectacleID]);

        //supprimer le spectacle
        $sql = "DELETE FROM spectacles WHERE SpectacleID = :spectacleID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['spectacleID' => $spectacleID]);
    }

    /*ajouter une soirée */
    public function addSoiree(string $date, int $lieuID, string $horaire, string $thematique, float $tarifs, string $nomSoiree): int
    {
        $sql = "INSERT INTO soirees(DateSoiree, LieuID, horaire, thematique, tarifs,nomSoiree) VALUES ( :date, :lieuID, :horaire, :thematique, :tarifs, :nomSoiree)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([ 'date' => $date, 'lieuID' => $lieuID, 'horaire' => $horaire, 'thematique' => $thematique, 'tarifs' => $tarifs, 'nomSoiree' => $nomSoiree]);
        return $this->pdo->lastInsertId();
    }

    /*supprimer une soirée */
    public function deleteSoiree(int $soireeID): void
    {
        //select les spectacles de la soirée
        $sql = "SELECT SpectacleID FROM spectacles WHERE SoireeID = :soireeID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['soireeID' => $soireeID]);
        $spectacles = $stmt->fetchAll(PDO::FETCH_ASSOC);


        //supprimer les spectacles
        foreach ($spectacles as $spectacle){
            $this->deleteSpectacleFromSoiree($soireeID, $spectacle['SpectacleID']);
        }


        //supprimer la soirée
        $sql = "DELETE FROM soirees WHERE SoireeID = :soireeID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['soireeID' => $soireeID]);
    }

    //getLieux
    public function getLieux(): array
    {
        $sql = "SELECT * FROM lieux";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //ajouter Spectacle à une soirée
    public function addSpectacleToSoiree(int $soireeID, int $spectacleID): void
    {
        $sql = "UPDATE spectacles SET SoireeID = :soireeID WHERE SpectacleID = :spectacleID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['soireeID' => $soireeID, 'spectacleID' => $spectacleID]);

        $sql = "INSERT INTO soirees_pectacles(SoireeID, SpectacleID) VALUES ( :soireeID, :spectacleID)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['soireeID' => $soireeID, 'spectacleID' => $spectacleID]);
    }

    //supprimer Spectacle d'une soirée
    public function deleteSpectacleFromSoiree(int $soireeID, int $spectacleID): void
    {
        $sql = "UPDATE spectacles SET SoireeID = NULL WHERE SpectacleID = :spectacleID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['spectacleID' => $spectacleID]);

        $sql = "DELETE FROM soirees_pectacles WHERE SoireeID = :soireeID AND SpectacleID = :spectacleID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['soireeID' => $soireeID, 'spectacleID' => $spectacleID]);
    }

}







