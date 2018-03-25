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

require_once ("./model/artwork.php");

class Photo
{

    private $id;

    private $filename;

    private $artwork;

    private $uploadDate;

    function __construct ()
    {
        // default id. The final id is generated later automatically by the
        // database
        $this->id = - 1;
        
        $this->filename = "";
        $this->artwork = new Artwork();
        $this->uploadDate = new DateTime();
    }

    public function __toString (): string
    {
        $photoAsHTMLTable = '<table><tr><th>Attribut</th><th>Wert</th></tr><tr><td>Id:</td><td>' .
                 $this->id . '</td></tr><tr><td>Filename</td><td>' .
                 $this->filename . '</td></tr><tr><td>Speicherdatum</td><td>' .
                 $this->uploadDate->format('d.m.Y') .
                 '</td></tr><tr><td>Kunstwerk Id</td><td>' .
                 $this->artwork->getId() . '</td></tr></table>';
        
        return $photoAsHTMLTable;
    }

    public function isPhotoValid (): bool
    {
        return ($this->idIsValid() && $this->artwork->isArtworkValid() &&
                 $this->filenameIsValid() && $this->uplodaDateIsValid());
    }

    public function getId (): int
    {
        return $this->id;
    }

    public function setId (int $id): void
    {
        $this->id = $id;
    }

    public function getFilename (): string
    {
        return $this->filename;
    }

    public function getUploadDate (): DateTime
    {
        return $this->uploadDate;
    }

    public function setFilename (string $filename): void
    {
        $this->filename = $filename;
    }

    public function setUploadDate (DateTime $uploadDate): void
    {
        $this->uploadDate = $uploadDate;
    }

    public function setArtwork (Artwork $artwork): void
    {
        $this->artwork = $artwork;
    }

    public function getArtwork (): Artwork
    {
        return $this->artwork;
    }
    
    public function setLanguage (Language $language): void
    {
        $this->getArtwork()->setLanguage($language);
    }

    private function idIsValid (): bool
    {
        return (isset($this->id) && is_int($this->id));
    }

    private function uploadDateIsValid (): bool
    {
        return isset($this->uploadDate);
    }

    private function filenameIsValid (): bool
    {
        return isset($this->filename);
    }
}
?>
