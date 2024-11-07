<?php

declare(strict_types=1);

namespace iutnc\nrv\auth;

use iutnc\nrv\repository\NrvRepository;

class AuthzProvider{
    public static function isAuthorized(int $rolerequis): bool{
        $r = NrvRepository::getInstance();
        $role = 1; // role par défaut pour utilisateur non connecté
        if(isset($_SESSION['email'])){
            $role = $r->getRoleUser($_SESSION['email']);
        }
        return $role >= $rolerequis;
    }
}