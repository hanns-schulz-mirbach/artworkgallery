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

require_once ("./util/language.php");
require_once ("./util/user_role.php");

class SessionManager
{

    private $language;

    public function __construct ()
    {
        $this->language = new Language();
        
        if (! isset($_SESSION["user_language"])) {
            $this->language->setLanguageFromBrowserSettings();
            $_SESSION["user_language"] = $this->language->getLanguageId();
        } else {
            $this->language->setLanguageId($_SESSION["user_language"]);
        }
    }

    public function getUserLanguage (): Language
    {
        return $this->language;
    }

    public function setUserLanguage (Language $language): void
    {
        $this->language = $language;
        $_SESSION["user_language"] = $this->language->getLanguageId();
    }

    public function getUserRole (): UserRole
    {
        if (isset($_SESSION["user_role"])) {
            $userRoleDescription = $_SESSION["user_role"];
        } else {
            $userRoleDescription = 'Anonymous';
        }
        
        $userRole = new UserRole();
        $userRole->setUserRoleDescription($userRoleDescription);
        return $userRole;
    }

    public function isUserRoleSet (): bool
    {
        return isset($_SESSION["user_role"]);
    }
}

?>