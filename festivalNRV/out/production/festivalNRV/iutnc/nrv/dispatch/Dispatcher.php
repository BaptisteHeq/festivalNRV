<?php

namespace iutnc\nrv\dispatch;

use iutnc\nrv\action\DefaultAction;

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
            case 'default':
                $action = new DefaultAction();
                $html = $action->execute();
                break;

        }
        $this->renderPage($html);
    }

    public function renderPage ($html)
    {
        echo $html;
    }

}