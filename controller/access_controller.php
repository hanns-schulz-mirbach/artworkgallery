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

class AccessController
{

    private $userRole;

    public function getUserRole (): UserRole
    {
        return $this->userRole;
    }

    public function setUserRole (UserRole $userRole)
    {
        $this->userRole = $userRole;
    }

    public function __construct (UserRole $userRole)
    {
        $this->userRole = $userRole;
    }

    public function __toString (): string
    {
        return $this->userRole->getUserRoleDescription();
    }

    public function access_Translation (): bool
    {
        return ($this->userRole->isAdmin());
    }

    public function access_Report (): bool
    {
        return ($this->userRole->isAdmin());
    }

    public function access_ArtistMasterData (): bool
    {
        return ($this->userRole->isAdmin());
    }

    public function access_ArtworkMasterData (): bool
    {
        return ($this->userRole->isAdmin());
    }

    public function access_PhotoMasterData (): bool
    {
        return ($this->userRole->isAdmin());
    }

    public function access_AvailabilityMasterData (): bool
    {
        return ($this->userRole->isAdmin());
    }

    public function access_MaterialMasterData (): bool
    {
        return ($this->userRole->isAdmin());
    }

    public function access_TechniqueMasterData (): bool
    {
        return ($this->userRole->isAdmin());
    }

    public function access_ExhibitionMasterData (): bool
    {
        return ($this->userRole->isAdmin());
    }

    public function access_GalleryMasterData (): bool
    {
        return ($this->userRole->isAdmin());
    }

    public function access_AdminFunctions (): bool
    {
        return ($this->userRole->isAdmin());
    }

    public function access_PHP_Info (): bool
    {
        return ($this->userRole->isAdmin());
    }
}

?>