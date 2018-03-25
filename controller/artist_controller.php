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

require_once ("./model/artist.php");
require_once ("./db/database.php");

class ArtistController
{

    private $artist;

    private $database;

    public function __construct ()
    {
        $this->artist = new Artist();
        $this->database = new Database();
    }

    public function __destruct ()
    {
        $this->database = null;
    }

    public function getArtist (): Artist
    {
        return $this->artist;
    }

    public function setArtist (Artist $artist): void
    {
        $this->artist = $artist;
    }

    public function instantiateSkeleton (string $firstname, string $lastname,
            DateTime $birthdate, string $mail, string $telephone,
            string $cellphone, string $address): void
    {
        $this->artist->setFirstname($firstname);
        $this->artist->setLastname($lastname);
        $this->artist->setBirthdate($birthdate);
        $this->artist->setMail($mail);
        $this->artist->setTelephone($telephone);
        $this->artist->setCellphone($cellphone);
        $this->artist->setAddress($address);
    }

    public function insertArtist (): int
    {
        $affectedRows = $this->database->insertArtist($this->artist);
        return $affectedRows;
    }

    public function updateArtist (): int
    {
        $affectedRows = $this->database->updateArtist($this->artist);
        return $affectedRows;
    }

    public function deleteArtist (): int
    {
        $affectedRows = $this->database->deleteArtist($this->artist);
        return $affectedRows;
    }

    public function getAllArtists (): array
    {
        $allArtists = [];
        
        $allArtists = $this->database->getAllArtists();
        
        return $allArtists;
    }

    public function getArtistById (int $artistId): Artist
    {
        return $this->database->getArtistById($artistId);
    }
}

?>