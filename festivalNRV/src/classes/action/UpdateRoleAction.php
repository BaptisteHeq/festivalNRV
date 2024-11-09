<?php

declare(strict_types=1);

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthzProvider;
use iutnc\nrv\repository\NrvRepository;

class UpdateRoleAction extends Action
{
    public function __construct()
    {
        parent::__construct();
        $this->role = 100;
    }

    public function execute(): string{
        if(!AuthzProvider::isAuthorized($this->role))
            return "Vous n'êtes pas autorisé à accéder à cette page";

        $html = "";

        if($_SERVER['REQUEST_METHOD'] === 'GET') {
            $r = NrvRepository::getInstance();
            $users = $r->getLstUsers();

            $html .= "<form method='post' action='?action=update-role'>";
            $html .= '<select name="id" required>';
            foreach ($users as $user) {
                $id = $user["id"];

                if ($id != null)
                    $html .= '<option value=' . $id . '> email: ' . $user['email'] . "/role: " . $user['role'] . '</option>';
            }
            $html .= '</select>';
            $html .= '<label> role : </label>';
            $html .= '<input type="number" name="role" required>';
            $html .= '<button type="submit" name="submit" value="submit">Submit</button>';
            $html .= '</form>';
                }elseif($_SERVER['REQUEST_METHOD'] === 'POST'){
                    $id = intval($_POST['id']);
                    $role = intval($_POST['role']);
                    $r = NrvRepository::getInstance();
                    $r->updateRole($id, $role);
                    $html .= 'Role updated';
        }


        return($html);
    }
}