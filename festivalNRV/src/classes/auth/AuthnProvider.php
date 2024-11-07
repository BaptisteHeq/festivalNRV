<?php

declare(strict_types=1);

namespace iutnc\nrv\auth;

use iutnc\nrv\exception\AuthnException;
use PDO;
use PDOException;

class AuthnProvider{
    public static function signin(string $nom, string $email): bool{
        $db = new PDO('mysql:host=localhost;dbname=nrv', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = $db->prepare('SELECT * FROM utilisateurs WHERE nomutilisateur = :nom');
        $query->execute(['nom' => $nom]);
        $user = $query->fetch();
        if($user === false){
            throw new AuthnException('Utilisateur inconnu');
        }
        if($_SESSION['emailutilisateur'] = $email){
            return true;
        }
        throw new AuthnException('Email incorrect');
    }
}