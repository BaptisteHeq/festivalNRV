-- Table des lieux
CREATE TABLE lieu (
    lieuID INT PRIMARY KEY,
    nomLieu VARCHAR(100) NOT NULL,
    adresseLieu VARCHAR(255),
    nbPlaceAssis INT,
    nbPlaceDebout INT
);

-- Table des styles
CREATE TABLE style (
    styleID INT PRIMARY KEY,
    nomStyle VARCHAR(50) NOT NULL
);

-- Table des soirées
CREATE TABLE soiree (
    soireeID INT PRIMARY KEY AUTO_INCREMENT,
    dateSoiree DATE NOT NULL,
    lieuID INT,
    thematique VARCHAR(255),
    nomSoiree VARCHAR(255),
    horaire TIME,
    tarifs DECIMAL(10,2),
    FOREIGN KEY (lieuID) REFERENCES lieu(lieuID)
);

-- Table des spectacles
CREATE TABLE spectacle (
    spectacleID INT PRIMARY KEY AUTO_INCREMENT,
    nomSpectacle VARCHAR(100) NOT NULL,
    styleID INT,
    dateSpectacle DATE NOT NULL,
    estAnnule INT,
    horaire TIME,
    duree INT,
    description TEXT,
    FOREIGN KEY (styleID) REFERENCES style(styleID)
);


-- Table des utilisateurs
CREATE TABLE utilisateur (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role INT NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Table de liaison Soirees_spectacles
CREATE TABLE soiree_spectacle (
    soireeID INT,
    spectacleID INT,
    PRIMARY KEY (soireeID, spectacleID),
    FOREIGN KEY (soireeID) REFERENCES soiree(soireeID),
    FOREIGN KEY (spectacleID) REFERENCES spectacle(spectacleID)
);

-- Table des artistes
CREATE TABLE artiste (
    artisteID INT PRIMARY KEY AUTO_INCREMENT,
    nomArtiste VARCHAR(100)
);

-- Table des médias
CREATE TABLE media (
    mediaID INT PRIMARY KEY AUTO_INCREMENT,
    cheminFichier VARCHAR(255)
);

-- Table de liaison Spectacle_Media
CREATE TABLE spectacle_media (
    spectacleID INT,
    mediaID INT,
    PRIMARY KEY (spectacleID, mediaID),
    FOREIGN KEY (spectacleID) REFERENCES spectacle(spectacleID),
    FOREIGN KEY (mediaID) REFERENCES media(mediaID)
);

-- Table de liaison Spectacle_Artiste
CREATE TABLE spectacle_artiste (
    spectacleID INT,
    artisteID INT,
    PRIMARY KEY (spectacleID, artisteID),
    FOREIGN KEY (spectacleID) REFERENCES spectacle(spectacleID),
    FOREIGN KEY (artisteID) REFERENCES artiste(artisteID)
);

-- Table de liaison Lieu_Media
CREATE TABLE lieu_media (
    lieuID INT,
    mediaID INT,
    PRIMARY KEY (lieuID, mediaID),
    FOREIGN KEY (lieuID) REFERENCES lieu(lieuID),
    FOREIGN KEY (mediaID) REFERENCES media(mediaID)
);

CREATE TABLE lieu_spectacle (
	lieuID INT,
	spectacleID INT,
	PRIMARY KEY (lieuID, spectacleID),
FOREIGN KEY (lieuID) REFERENCES lieu(lieuID),
	FOREIGN KEY (spectacleID) REFERENCES spectacle(spectacleID)
);

-- Insertion des exemples
-- Insertion d'exemples dans la table Lieu
INSERT INTO lieu VALUES (1, 'Grand Hall', '123 Rue de la Musique', 100, 500), (2, 'Petit Théâtre', '456 Avenue des Arts', 200, 1000),  (3, 'Open Air', '789 Rue de Trocourt', 200, 500);

-- Insertion d'exemples dans la table Style
INSERT INTO style VALUES (1, 'Classic Rock'), (2, 'Blues Rock'), (3, 'Metal');

-- Insertion d'exemples dans la table Soiree
INSERT INTO soiree VALUES (1, '2024-05-15', 1,  'Nuit du Rock', 'Rock Night', '20:00', 25.50), (2, '2024-06-10', 2,  'Jazz et Blues', 'Blue Jazz Fest', '18:30', 30.00);

-- Insertion d'exemples dans la table Spectacle
INSERT INTO spectacle VALUES (1, 'Guitar Heroes', 1, '2024-05-15', 0, '20:30', 120, 'Un spectacle qui rend hommage aux plus grands guitaristes.'), (2, 'Blues Legends', 2, '2024-06-10', 0, '19:00', 90, 'Les légendes du blues réunies pour une soirée exceptionnelle.');

-- Insertion d'exemples dans la table Utilisateur
INSERT INTO utilisateur VALUES (1, 'Alice Dupont', 'alice@example.com', 1, '$2y$12$e9DCiDKOGpVs9s.9u2ENEOiq7wGvx7sngyhPvKXo2mUbI3ulGWOdC'), (2, 'Bob Martin', 'bob@example.com', 100, '$2y$12$4EuAiwZCaMouBpquSVoiaOnQTQTconCP9rEev6DMiugDmqivxJ3AG');
/*Mdp = user1 pour Alice et user2 pour Bob*/

-- Insertion d'exemples dans la table Soiree_Spectacle
INSERT INTO soiree_spectacle VALUES (1, 2), (1, 2);

-- Insertion d'exemples dans la table Artiste
INSERT INTO artiste VALUES (1, 'Carlos'), (2, 'Francis Lalanne');

-- Insertion d'exemples dans la table Media
INSERT INTO media VALUES (1, 'rock_poster.jpg'), (2, 'blues_flyer.png');

-- Insertion d'exemples dans la table Spectacle_Media
INSERT INTO spectacle_media VALUES (1, 1), (2, 2);

-- Insertion d'exemples dans la table Spectacle_Artiste
INSERT INTO spectacle_artiste VALUES (1, 1), (2, 2);

-- Insertion d'exemples dans la table Lieu_Media
INSERT INTO lieu_media VALUES (1, 1), (2, 2);

INSERT INTO lieu_spectacle VALUES (1,1);
INSERT INTO lieu_spectacle VALUES (2,2);
