<?php

declare(strict_types=1);

namespace iutnc\nrv\exception;

use Exception;

class AuthnException extends \Exception{
    public function __construct($message = "Echec Authentication", $code = 0, Exception $previous = null){
        parent::__construct($message, $code, $previous);
    }
}