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

require_once ("./model/photo.php");

class GalleryPhoto
{

    private $id;

    private $photo;

    private $indexOfPhoto;

    private $galleryId;

    function __construct ()
    {
        // default id. The final id is generated later automatically by the
        // database
        $this->id = - 1;
        
        $this->galleryId = - 1;
        $this->indexOfPhoto = - 1;
        $this->photo = new Photo();
    }

    public function __toString (): string
    {
        $galleryPhotoAsHTMLTable = '<table><tr><th>Attribut</th><th>Wert</th></tr><tr><td>Id:</td><td>' .
                 $this->id . '</td></tr><tr><td>Index</td><td>' . $this->indexOfPhoto .
                 '</td></tr><tr><td>Galerie Id</td><td>' . $this->galleryId .
                 '</td></tr><tr><td>Foto</td><td>' . $this->photo .
                 '</td></tr></table>';
        
        return $galleryPhotoAsHTMLTable;
    }

    public function isGalleryPhotoValid (): bool
    {
        return ($this->idIsValid() && $this->photo->isPhotoValid() &&
                 $this->indexOfPhotoIsValid());
    }

    public function getId (): int
    {
        return $this->id;
    }

    public function setId (int $id): void
    {
        $this->id = $id;
    }

    public function getGalleryId (): int
    {
        return $this->galleryId;
    }

    public function setGalleryId (int $galleryId): void
    {
        $this->galleryId = $galleryId;
    }

    public function getIndexOfPhoto (): int
    {
        return $this->indexOfPhoto;
    }

    public function getPhoto (): Photo
    {
        return $this->photo;
    }

    public function setIndexOfPhoto (int $indexOfPhoto): void
    {
        $this->indexOfPhoto = $indexOfPhoto;
    }

    public function setPhoto (Photo $photo): void
    {
        $this->photo = $photo;
    }
    
    public function setLanguage (Language $language): void
    {
        $this->getPhoto()->setLanguage($language);
    }

    private function idIsValid (): bool
    {
        return (isset($this->id) && is_int($this->id));
    }

    private function indexOfPhotoIsValid (): bool
    {
        return isset($this->indexOfPhoto);
    }
}
?>
