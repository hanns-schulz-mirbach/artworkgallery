<?php
declare(strict_types = 1);

/*
 * Copyright (C) 2018 Hanns Schulz-Mirbach, <http://www.schulz-mirbach.info/>
 *
 * This file is part of the ArtworkGallery program.
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or any later
 * version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/gpl.html/>
 */

require_once ("./util/user_role.php");

class LoginController
{

    private $e_mail;

    private $passwd;

    private $userRole;

    public function __construct (string $a_email, string $a_passwd)
    {
        $this->e_mail = $a_email;
        $this->passwd = $a_passwd;
        
        $this->setUserRole();
    }

    public function addUserRoleToSession (): void
    {
        $_SESSION['user_role'] = $this->userRole->getUserRoleDescription();
    }

    public function getUserRoleDescription (): string
    {
        return $this->userRole->getUserRoleDescription();
    }

    public function userIsAdmin (): bool
    {
        return $this->userRole->isAdmin();
    }

    private function setUserRole (): void
    {
        $this->userRole = new UserRole();
        $adminMail = "your-namen@your-domain.your-domain-extension";
        $adminPassword = "your-password";
        
        if (($this->e_mail == $adminMail) && ($this->passwd == $adminPassword)) {
            $this->userRole->setUserRoleDescription("Administrator");
        } else {
            $this->userRole->setUserRoleDescription("Anonymous");
        }
    }
}

?>