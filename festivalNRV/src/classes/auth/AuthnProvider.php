<?php

declare(strict_types=1);

namespace iutnc\nrv\auth;

use iutnc\nrv\exception\AuthnException;
use iutnc\nrv\repository\NrvRepository;

class AuthnProvider{
    public static function signin(string $email, string $password): bool{
        $r = NrvRepository::getInstance();
        $hash = $r->getHashUser($email);

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
}