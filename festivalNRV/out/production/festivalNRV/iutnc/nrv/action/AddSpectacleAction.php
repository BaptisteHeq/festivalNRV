<?php

namespace iutnc\nrv\action;

use iutnc\nrv\evenement\programme\Programme;
use iutnc\nrv\evenement\spectacle\Spectacle;

class AddSpectacleAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        $html = "";
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $html .= <<<HTML
            <h2>Créer un nouveau spectacle</h2>
            <form method="post" action="?action=add-spectacle">
                <label for="spectacle_name">Nom du spectacle</label>
                <input type="text" id="spectacle_name" name="spectacle_name" required>
                <label for="spectacle_date">Date du spectacle</label>
                <input type="date" id="spectacle_date" name="spectacle_date" required>   
                <label for="spectacle_style">Style du spectacle</label> 
                <select id="spectacle_style" name="spectacle_style" required>
                    <option value="1">Classic Rock</option>
                    <option value="2">Blue Rock</option>
                    <option value="3">Metal</option>
                </select>
                
                <input type="submit" value="Créer le spectacle">
            </form>
            HTML;

        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $spectacle_name = filter_var($_POST['spectacle_name'], FILTER_SANITIZE_STRING);
            $spectacle_date = filter_var($_POST['spectacle_date'], FILTER_SANITIZE_STRING);
            $spectacle_style = filter_var($_POST['spectacle_style'], FILTER_SANITIZE_STRING);
            $spectacle = new Spectacle($spectacle_name, $spectacle_date, $spectacle_style);

            $_SESSION['spectacle'] = serialize($spectacle);
            $html .= 'Spectacle ajouté avec succès';
        }
        return $html;
    }
}