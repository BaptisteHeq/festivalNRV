<?php

namespace iutnc\nrv\repository;

use Exception;
use iutnc\nrv\evenement\soiree\Soiree;
use iutnc\nrv\evenement\spectacle\Spectacle;
use PDO;
use PDOException;


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



    public function getStylesByID(int $styleID): string
    {
        $sql = "SELECT NomStyle FROM style WHERE styleID = :styleID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['styleID' => $styleID]);
        return $stmt->fetchColumn();
    }

    /* la liste des soirées */
    public function getSoirees(): array
    {
        $sql = "SELECT * FROM soiree";
        $stmt = $this->pdo->query($sql);
        $liste = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $soirees = [];
        foreach ($liste as $soiree){
            $so = new Soiree($soiree['dateSoiree'], $soiree['lieuID'], $soiree['horaire'], $soiree['thematique'], $soiree['tarifs'], $soiree['nomSoiree']);
            $so->setSoireeID($soiree['soireeID']);
            $soirees[] = $so;
        }
        return $soirees;
    }

    /* la liste des spectacles */
    public function getSpectaclesByIDsoiree(int $soireeID): array
    {
        //soiree_Spectacle(#soireeID, #spectacleID)
        $sql = "SELECT * FROM spectacle WHERE spectacleID IN (SELECT spectacleID FROM soiree_spectacle WHERE soireeID = :soireeID)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['soireeID' => $soireeID]);
        //créer un tableau de spectacles
        $liste= $stmt->fetchAll(PDO::FETCH_ASSOC);
        $spectacles = [];
        foreach ($liste as $sp){
            $images = [];
            $sql = "SELECT cheminFichier FROM media WHERE mediaID IN (SELECT mediaID FROM spectacle_media WHERE spectacleID = :spectacleID)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['spectacleID' => $sp['spectacleID']]);
            $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $videos = [];
            $sql = "SELECT cheminFichier FROM media WHERE mediaID IN (SELECT mediaID FROM spectacle_media WHERE spectacleID = :spectacleID)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['spectacleID' => $sp['spectacleID']]);
            $videos = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $artistes = [];
            $sql = "SELECT nomArtiste FROM artiste WHERE artisteID IN (SELECT artisteID FROM spectacle_artiste WHERE spectacleID = :spectacleID)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['spectacleID' => $sp['spectacleID']]);
            $artistes = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $spectacle = new Spectacle($sp['nomSpectacle'], $sp['dateSpectacle'], $sp['styleID'], $sp['horaire'], $images, $sp['description'], $videos, $artistes, $sp['duree']);
            $spectacle->setSpectacleID($sp['spectacleID']);
            $spectacles[] = $spectacle;


        }
        return $spectacles;
    }

    /* le lieu par l'id */
    public function getLieuByID(int $lieuID): string
    {
        $sql = "SELECT NomLieu FROM lieu WHERE lieuID = :lieuID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['lieuID' => $lieuID]);
        return $stmt->fetchColumn();
    }

    /*ajouter un spectacle */
    public function addSpectacle(string $nom,int $styleID, string $date,int $estAnnule, string $horaire, array $img, string $description, array $video, array $artistes, int $duree): int
    {
        //spectacle(spectacleID, nomSpectacle, #styleID, dateSpectacle, estAnnule , horaire, duree)
        $sql = "INSERT INTO spectacle(nomSpectacle, styleID, dateSpectacle,estAnnule, horaire,duree, description) VALUES ( :nom, :styleID, :date,:estAnnule, :horaire, :duree, :description)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([ 'nom' => $nom, 'styleID' => $styleID, 'date' => $date, 'estAnnule' => $estAnnule, 'horaire' => $horaire, 'duree' => $duree, 'description' => $description]);
        $spectacleID = $this->pdo->lastInsertId();

        //ajouter les images (Media(mediaID, cheminFichier))
        $sql = "INSERT INTO media(cheminFichier) VALUES (:cheminFichier)";
        $stmt = $this->pdo->prepare($sql);
        foreach ($img as $i){
            $stmt->execute(['cheminFichier' => $i]);
            $mediaID = $this->pdo->lastInsertId();
            //ajouter les images du spectacle (spectacle_media(spectacleID, mediaID))
            $sql = "INSERT INTO spectacle_media(spectacleID, mediaID) VALUES (:spectacleID, :mediaID)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['spectacleID' => $spectacleID, 'mediaID' => $mediaID]);
        }

        //ajouter les videos (Media(mediaID, cheminFichier))
        $sql = "INSERT INTO media(cheminFichier) VALUES (:cheminFichier)";
        $stmt = $this->pdo->prepare($sql);
        foreach ($video as $v){
            $stmt->execute(['cheminFichier' => $v]);
            $mediaID = $this->pdo->lastInsertId();
            //ajouter les videos du spectacle (spectacle_media(spectacleID, mediaID))
            $sql = "INSERT INTO spectacle_media(spectacleID, mediaID) VALUES (:spectacleID, :mediaID)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['spectacleID' => $spectacleID, 'mediaID' => $mediaID]);
        }

        //ajouter les artistes (Artiste(artisteID, nomArtiste))
        $sql = "INSERT INTO artiste(nomArtiste) VALUES (:nomArtiste)";
        $stmt = $this->pdo->prepare($sql);
        foreach ($artistes as $a){
            $stmt->execute(['nomArtiste' => $a]);
            $artisteID = $this->pdo->lastInsertId();
            //ajouter les artistes du spectacle (spectacle_artiste(spectacleID, artisteID))
            $sql = "INSERT INTO spectacle_artiste(spectacleID, artisteID) VALUES (:spectacleID, :artisteID)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['spectacleID' => $spectacleID, 'artisteID' => $artisteID]);
        }

        return $spectacleID;

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
        $sql = "DELETE FROM soirees_spectacles WHERE SpectacleID = :spectacleID";
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

        $sql = "INSERT INTO soirees_spectacles(SoireeID, SpectacleID) VALUES ( :soireeID, :spectacleID)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['soireeID' => $soireeID, 'spectacleID' => $spectacleID]);
    }

    //supprimer Spectacle d'une soirée
    public function deleteSpectacleFromSoiree(int $soireeID, int $spectacleID): void
    {
        $sql = "UPDATE spectacles SET SoireeID = NULL WHERE SpectacleID = :spectacleID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['spectacleID' => $spectacleID]);

        $sql = "DELETE FROM soirees_spectacles WHERE SoireeID = :soireeID AND SpectacleID = :spectacleID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['soireeID' => $soireeID, 'spectacleID' => $spectacleID]);
    }

    public function getSpectacles(): array
    {
        $sql = "SELECT * FROM spectacles";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $spectacles = [];
        foreach ($result as $row) {
            $spectacles[] = new Spectacle(
                $row['NomSpectacle'],
                $row['DateSpectacle'],
                $row['StyleID'],
                $row['horaire'],
                $row['image'],
                $row['description'],
                $row['video'],
                $row['artistes'],
                $row['duree']
            );
        }

        return $spectacles;
    }



    public function getSoireeByID(int $soireeID) : array
    {
        $sql = "SELECT * FROM soirees WHERE SoireeID = :soireeID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['soireeID' => $soireeID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* get Spectacle sans soiree */
    public function getSpectaclesSansSoiree()
    {
        $sql = "SELECT * FROM spectacles WHERE SoireeID IS NULL";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* update spectacle (pas img/vid)*/
    public function updateSpectacle(int $spectacleID, string $nom, string $date, int $styleID, string $horaire, string $description, string $artistes, int $duree): void
    {
        $sql = "UPDATE spectacles SET NomSpectacle = :nom, DateSpectacle = :date, StyleID = :styleID, horaire = :horaire, description = :description, artistes = :artistes, duree = :duree WHERE SpectacleID = :spectacleID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([ 'nom' => $nom, 'date' => $date, 'styleID' => $styleID, 'horaire' => $horaire, 'description' => $description, 'artistes' => $artistes, 'duree' => $duree, 'spectacleID' => $spectacleID]);
    }

    public function getIdSpectacle(string $nom, string $date, string $horaire): int
    {
        $sql = "SELECT SpectacleID FROM spectacles WHERE NomSpectacle = :nom AND DateSpectacle = :date AND horaire = :horaire";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['nom' => $nom, 'date' => $date, 'horaire' => $horaire]);
        return $stmt->fetchColumn();
    }

    /* fonction qui cherche un spectacle par son ID, le construit et le renvoie*/
    public function getSpectacleByID(int $spectacleID): Spectacle{
        $sql = "SELECT * FROM spectacles WHERE SpectacleID = :spectacleID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['spectacleID' => $spectacleID]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            throw new Exception("Spectacle non trouvé à l'id $spectacleID");
        }
        return new Spectacle($row['NomSpectacle'], $row['DateSpectacle'], $row['StyleID'], $row['horaire'], $row['image'], $row['description'], $row['video'], $row['artistes'], $row['duree']);
    }

    public function getHashUser(string $email): ?String
    {
        $sql = "SELECT password FROM Utilisateurs WHERE EmailUtilisateur = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        if($stmt->rowCount() == 0){
            return null;
        }
        return $stmt->fetch(PDO::FETCH_ASSOC)['password'];
    }

    public function addUser(string $nom, string $email, string $hash): void
    {
        $sql = "INSERT INTO Utilisateurs(NomUtilisateur, EmailUtilisateur, password, role) VALUES ( :nom, :email, :hash, 1)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([ 'nom' => $nom, 'email' => $email, 'hash' => $hash]);
    }

    public function getNomUser(string $email): string
    {
        $sql = "SELECT NomUtilisateur FROM Utilisateurs WHERE EmailUtilisateur = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetchColumn();
    }

    public function getRoleUser(string $email): int
    {
        $sql = "SELECT role FROM Utilisateurs WHERE EmailUtilisateur = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetchColumn();
    }

    public function getLstUsers(): array
    {
        $sql = "SELECT * FROM Utilisateurs";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateRole(int $id, int $role): void
    {
        $sql = "UPDATE Utilisateurs SET role = :role WHERE UtilisateurID = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['role' => $role, 'id' => $id]);
    }

    public function rechercherSpectacles(string $nomSp): array
    {
        try {
            $sql = "SELECT * FROM spectacles WHERE nomSpectacle LIKE :nom";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['nom' => '%' . $nomSp . '%']);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($rows)) {
                throw new Exception("Aucun spectacle trouvé avec le terme '$nomSp'");
            }

            $spectacles = [];
            foreach ($rows as $row) {
                $spectacles[] = new Spectacle(
                    $row['NomSpectacle'],
                    $row['DateSpectacle'],
                    $row['StyleID'],
                    $row['horaire'],
                    $row['image'],
                    $row['description'],
                    $row['video'],
                    $row['artistes'],
                    $row['duree']
                );
            }

            return $spectacles;
        }
        catch (PDOException $e) {
            error_log("Erreur PDO lors de la recherche de spectacles : " . $e->getMessage());
            return [];
        } catch (Exception $e) {
            error_log("Erreur dans la recherche des spectacles : " . $e->getMessage());
            return [];
        }
    }
}







