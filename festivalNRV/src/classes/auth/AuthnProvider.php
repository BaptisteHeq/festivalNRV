<?php

declare(strict_types=1);

namespace iutnc\nrv\auth;

use iutnc\nrv\exception\AuthnException;
use iutnc\nrv\repository\NrvRepository;

class AuthnProvider{
    public static function signin(string $email, string $password): bool{
        $r = NrvRepository::getInstance();
        $hash = $r->getHashUser($email);

        /* filtrer les attributs*/
        $email = filter_var($email, FILTER_SANITIZE_STRING);
        $password = filter_var($password, FILTER_SANITIZE_STRING);

        /* verifier si pas null*/
        if($hash === null){
            throw new AuthnException('Utilisateur inconnu');
        }

        if (password_verify($password, $hash)) {
            $_SESSION['email'] = $email;
            return true;
        }
        return false;
    }

    public static function signout(): void{
        unset($_SESSION['email']);
    }

    public static function register(string $nom, string $email, string $password): void{
        $r = NrvRepository::getInstance();

        /* filtrer les attributs*/
        $nom = filter_var($nom, FILTER_SANITIZE_STRING);
        $email = filter_var($email, FILTER_SANITIZE_STRING);
        $password = filter_var($password, FILTER_SANITIZE_STRING);

        /* verifier taille password */
        if(strlen($password) < 10){
            throw new AuthnException('Mot de passe trop court');
        }

        /* verifier si email deja utilise */
        if($r->getHashUser($email) !== null){
            throw new AuthnException('Email déjà utilisé');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $r->addUser($nom, $email, $hash);
    }
}