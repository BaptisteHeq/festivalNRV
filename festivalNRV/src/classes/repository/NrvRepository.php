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
        return $this->creerListeSpectacle($liste);
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


        // Ajouter les images
        foreach ($img as $i) {
            $sql = "INSERT INTO media(cheminFichier) VALUES (:cheminFichier)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['cheminFichier' => $i]);
            $mediaID = $this->pdo->lastInsertId();

            // Associer l'image au spectacle
            $sql = "INSERT INTO spectacle_media(spectacleID, mediaID) VALUES (:spectacleID, :mediaID)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['spectacleID' => $spectacleID, 'mediaID' => $mediaID]);
        }

        // Ajouter les vidéos
        foreach ($video as $v) {
            $sql = "INSERT INTO media(cheminFichier) VALUES (:cheminFichier)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['cheminFichier' => $v]);
            $mediaID = $this->pdo->lastInsertId();

            // Associer la vidéo au spectacle
            $sql = "INSERT INTO spectacle_media(spectacleID, mediaID) VALUES (:spectacleID, :mediaID)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['spectacleID' => $spectacleID, 'mediaID' => $mediaID]);
        }

        // Ajouter les artistes
        foreach ($artistes as $a) {
            $sql = "INSERT INTO artiste(nomArtiste) VALUES (:nomArtiste)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['nomArtiste' => $a]);
            $artisteID = $this->pdo->lastInsertId();

            // Associer l'artiste au spectacle
            $sql = "INSERT INTO spectacle_artiste(spectacleID, artisteID) VALUES (:spectacleID, :artisteID)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['spectacleID' => $spectacleID, 'artisteID' => $artisteID]);
        }
        return $spectacleID;

    }

    /*supprimer un spectacle */
    public function deleteSpectacle(int $spectacleID): void
    {
        //supprimer les fichiers dans le dossier media
        $sql = "SELECT cheminFichier FROM media WHERE mediaID IN (SELECT mediaID FROM spectacle_media WHERE spectacleID = :spectacleID)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['spectacleID' => $spectacleID]);
        $files = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($files as $f){
            unlink($f);
        }
        //stocker les id des médias
        $sql = "SELECT mediaID FROM spectacle_media WHERE spectacleID = :spectacleID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['spectacleID' => $spectacleID]);
        $mediaIDs = $stmt->fetchAll(PDO::FETCH_COLUMN);

        //supprimer les liens entre spectacle et médias
        $sql = "DELETE FROM spectacle_media WHERE spectacleID = :spectacleID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['spectacleID' => $spectacleID]);

        //supprimer les médias
        $sql = "DELETE FROM media WHERE mediaID IN (:mediaIDs)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['mediaIDs' => $mediaIDs]);



        //stocker les id des artistes
        $sql = "SELECT artisteID FROM spectacle_artiste WHERE spectacleID = :spectacleID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['spectacleID' => $spectacleID]);
        $artisteIDs = $stmt->fetchAll(PDO::FETCH_COLUMN);

        //supprimer les liens entre spectacle et artistes
        $sql = "DELETE FROM spectacle_artiste WHERE spectacleID = :spectacleID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['spectacleID' => $spectacleID]);

        //supprimer les artistes
        $sql = "DELETE FROM artiste WHERE artisteID IN (:artisteIDs)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['artisteIDs' => $artisteIDs]);

        //supprimer les liens entre spectacle et soiree
        $sql = "DELETE FROM soiree_spectacle WHERE spectacleID = :spectacleID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['spectacleID' => $spectacleID]);

        //supprimer le spectacle
        $sql = "DELETE FROM spectacle WHERE spectacleID = :spectacleID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['spectacleID' => $spectacleID]);

    }

    /*ajouter une soirée */
    public function addSoiree(string $date, int $lieuID, string $horaire, string $thematique, float $tarifs, string $nomSoiree): int
    {
        $sql = "INSERT INTO soiree(DateSoiree, LieuID, horaire, thematique, tarifs,nomSoiree) VALUES ( :date, :lieuID, :horaire, :thematique, :tarifs, :nomSoiree)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([ 'date' => $date, 'lieuID' => $lieuID, 'horaire' => $horaire, 'thematique' => $thematique, 'tarifs' => $tarifs, 'nomSoiree' => $nomSoiree]);
        return $this->pdo->lastInsertId();
    }

    /*supprimer une soirée */
    public function deleteSoiree(int $soireeID): void
    {
        //select les spectacles de la soirée
        $sql = "SELECT SpectacleID FROM spectacle WHERE SoireeID = :soireeID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['soireeID' => $soireeID]);
        $spectacles = $stmt->fetchAll(PDO::FETCH_ASSOC);


        //supprimer les spectacles
        foreach ($spectacles as $spectacle){
            $this->deleteSpectacleFromSoiree($soireeID, $spectacle['SpectacleID']);
        }


        //supprimer la soirée
        $sql = "DELETE FROM soiree WHERE SoireeID = :soireeID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['soireeID' => $soireeID]);
    }

    //getLieux
    public function getLieux(): array
    {
        $sql = "SELECT * FROM lieu";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //ajouter Spectacle à une soirée
    public function addSpectacleToSoiree(int $soireeID, int $spectacleID): void
    {
        $sql = "INSERT INTO soiree_spectacle(soireeID, spectacleID) VALUES ( :soireeID, :spectacleID)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['soireeID' => $soireeID, 'spectacleID' => $spectacleID]);
    }

    //supprimer Spectacle d'une soirée
    public function deleteSpectacleFromSoiree(int $soireeID, int $spectacleID): void
    {

        $sql = "DELETE FROM soiree_spectacle WHERE soireeID = :soireeID AND spectacleID = :spectacleID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['soireeID' => $soireeID, 'spectacleID' => $spectacleID]);
    }

    public function getSpectacles(): array
    {
        $sql = "SELECT * FROM spectacle";
        $stmt = $this->pdo->query($sql);
        $liste = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->creerListeSpectacle($liste);
    }



    public function getSoireeByID(int $soireeID) : Soiree
    {
        $sql = "SELECT * FROM soiree WHERE SoireeID = :soireeID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['soireeID' => $soireeID]);
        $soiree = $stmt->fetch(PDO::FETCH_ASSOC);

        $so = new Soiree($soiree['dateSoiree'], $soiree['lieuID'], $soiree['horaire'], $soiree['thematique'], $soiree['tarifs'], $soiree['nomSoiree']);
        $so->setSoireeID($soiree['soireeID']);

        return $so;
    }

    /* get Spectacle sans soiree */
    public function getSpectaclesSansSoiree() : array
    {
        //récupérer les spectacles sans soiree
        $sql = "SELECT * FROM spectacle WHERE spectacleID NOT IN (SELECT spectacleID FROM soiree_spectacle)";
        $stmt = $this->pdo->query($sql);
        $liste = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->creerListeSpectacle($liste);
    }

    /* update spectacle (pas img/vid)*/
    public function updateSpectacle(int $spectacleID, string $nom, string $date, int $styleID, string $horaire, string $description,  int $duree): void
    {
        $sql = "UPDATE spectacle SET nomSpectacle = :nom, dateSpectacle = :date, styleID = :styleID, horaire = :horaire, description = :description,  duree = :duree WHERE spectacleID = :spectacleID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['nom' => $nom, 'date' => $date, 'styleID' => $styleID, 'horaire' => $horaire, 'description' => $description, 'duree' => $duree, 'spectacleID' => $spectacleID]);

    }

    public function getIdSpectacle(string $nom, string $date, string $horaire): int
    {
        $sql = "SELECT SpectacleID FROM spectacle WHERE NomSpectacle = :nom AND DateSpectacle = :date AND horaire = :horaire";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['nom' => $nom, 'date' => $date, 'horaire' => $horaire]);
        return $stmt->fetchColumn();
    }

    /* fonction qui cherche un spectacle par son ID, le construit et le renvoie*/
    public function getSpectacleByID(int $spectacleID): Spectacle{
        $sql = "SELECT * FROM spectacle WHERE spectacleID = :spectacleID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['spectacleID' => $spectacleID]);
        $sp = $stmt->fetch(PDO::FETCH_ASSOC);

        $media = [];
        $sql = "SELECT cheminFichier FROM media WHERE mediaID IN (SELECT mediaID FROM spectacle_media WHERE spectacleID = :spectacleID)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['spectacleID' => $spectacleID]);
        $media = $stmt->fetchAll(PDO::FETCH_COLUMN);

        //tri des images et vidéos
        $images = [];
        $videos = [];
        foreach ($media as $m){
            if (strpos($m, 'mp4') !== false){
                $videos[] = $m;
            } else {
                $images[] = $m;
            }
        }

        $artistes = [];
        $sql = "SELECT nomArtiste FROM artiste WHERE artisteID IN (SELECT artisteID FROM spectacle_artiste WHERE spectacleID = :spectacleID)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['spectacleID' => $spectacleID]);
        $artistes = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $spectacle = new Spectacle($sp['nomSpectacle'], $sp['dateSpectacle'], $sp['styleID'], $sp['horaire'], $images, $sp['description'], $videos, $artistes, $sp['duree']);
        $spectacle->setSpectacleID($sp['spectacleID']);
        return $spectacle;
    }

    public function getHashUser(string $email): ?String
    {
        $sql = "SELECT password FROM utilisateur WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        if($stmt->rowCount() == 0){
            return null;
        }
        return $stmt->fetch(PDO::FETCH_ASSOC)['password'];
    }

    public function addUser(string $nom, string $email, string $hash): void
    {
        $sql = "INSERT INTO utilisateur(nom, email, password, role) VALUES ( :nom, :email, :hash, 1)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([ 'nom' => $nom, 'email' => $email, 'hash' => $hash]);
    }

    public function getNomUser(string $email): string
    {
        $sql = "SELECT nom FROM utilisateur WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetchColumn();
    }

    public function getRoleUser(string $email): int
    {
        $sql = "SELECT role FROM utilisateur WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetchColumn();
    }

    public function getLstUsers(): array
    {
        $sql = "SELECT * FROM utilisateur";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateRole(int $id, int $role): void
    {
        $sql = "UPDATE utilisateur SET role = :role WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['role' => $role, 'id' => $id]);
    }

    public function rechercherSpectacles(string $nomSp): array
    {
        try {
            $sql = "SELECT * FROM spectacle WHERE nomSpectacle LIKE :nom";
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

    public function getStyles()
    {
        $sql = "SELECT * FROM style";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //un spectacle est dans une soirée
    public function estSpectacleInSoiree(int $spectacleID, int $soireeID): bool
    {
        $sql = "SELECT * FROM soiree_spectacle WHERE spectacleID = :spectacleID AND soireeID = :soireeID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['spectacleID' => $spectacleID, 'soireeID' => $soireeID]);
        return $stmt->rowCount() > 0;
    }

    /**
     * @param $liste
     * @return array
     */
    public function creerListeSpectacle($liste): array
    {
        $spectacles = [];
        foreach ($liste as $sp) {
            $media = [];
            $sql = "SELECT cheminFichier FROM media WHERE mediaID IN (SELECT mediaID FROM spectacle_media WHERE spectacleID = :spectacleID)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['spectacleID' => $sp['spectacleID']]);
            $media = $stmt->fetchAll(PDO::FETCH_COLUMN);

            //tri des images et vidéos
            $images = [];
            $videos = [];
            foreach ($media as $m) {
                if (strpos($m, 'mp4') !== false) {
                    $videos[] = $m;
                } else {
                    $images[] = $m;
                }
            }

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

}

