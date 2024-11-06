<?php

namespace iutnc\nrv\dispatch;

use iutnc\nrv\action\DefaultAction;
use iutnc\nrv\action\AddSpectacleAction;
use iutnc\nrv\action\DisplaySpectacleAction;

class Dispatcher
{
    private string $action;

    public function __construct(string $action)
    {
        $this->action = $action;
    }

    public function run()
    {
        switch ($this->action) {
            case 'spectacle':
                $action = new DisplaySpectacleAction();
                $html = $action->execute();
                break;
            case 'add-spectacle':
                $action = new AddSpectacleAction();
                $html = $action->execute();
                break;
            default:
                $action = new DefaultAction();
                $html = $action->execute();
                break;


        }
        $this->renderPage($html);
    }

    public function renderPage ($html)
    {
        echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Festival NRV</title>
    </head>
    <header>
    <h1>Festival NRV</h1>
    <nav>
    <ul>
    <li><a href="?action=spectacle">Spectacle en session</a></li>
    <li><a href="?action=add-spectacle">Ajouter un spectacle</a></li>
    </ul>
    </header>
    
    <main>
    $html
    </main>
    
</html>

HTML;

    }

}