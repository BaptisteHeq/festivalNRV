<?php

namespace iutnc\nrv\action;

class DefaultAction extends Action
{

    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        return "Hello World";
    }
}