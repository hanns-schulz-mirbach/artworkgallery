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

require_once ("./model/gallery_photo.php");
require_once ("./model/translation.php");
require_once ("./util/language.php");

class Gallery
{

    private $id;

    private $title;

    private $description;

    private $galleryPhotos;

    function __construct ()
    {
        // default id. The final id is generated later automatically by the
        // database
        $this->id = - 1;
        
        $this->name = "";
        $this->title = new Translation();
        $this->description = new Translation();
        $this->galleryPhotos = [];
    }

    public function __toString (): string
    {
        $photosTableRows = "";
        foreach ($this->galleryPhotos as $galleryPhoto) {
            $photosTableRows = $photosTableRows . '<tr><td>Foto Id:</td><td>' .
                     $galleryPhoto->getPhoto()->getId() . '</td></tr>';
        }
        
        $galleryAsHTMLTable = '<table><tr><th>Attribut</th><th>Wert</th></tr><tr><td>Id:</td><td>' .
                 $this->id . '</td></tr><tr><td>Titel</td><td>' .
                 $this->title->getDecodedTranslation_de() .
                 '</td></tr><tr><td>Beschreibung</td><td>' .
                 $this->description->getDecodedTranslation_de() . '</td></tr>' .
                 $photosTableRows . '</table>';
        
        return $galleryAsHTMLTable;
    }

    public function isGalleryValid (): bool
    {
        return ($this->idIsValid() && $this->galleryPhotosAreValid());
    }

    public function getId (): int
    {
        return $this->id;
    }

    public function setId (int $id): void
    {
        $this->id = $id;
    }

    public function getTitle (): Translation
    {
        return $this->title;
    }

    public function setTitle (Translation $title): void
    {
        $this->title = $title;
    }

    public function getDescription (): Translation
    {
        return $this->description;
    }

    public function setDescription (Translation $description): void
    {
        $this->description = $description;
    }

    public function getGalleryPhotos (): array
    {
        return $this->galleryPhotos;
    }

    public function getGalleryPhotoByIndex (int $index): GalleryPhoto
    {
        return $this->galleryPhotos[$index];
    }

    public function setGalleryPhotos (array $galleryPhotos): void
    {
        $this->galleryPhotos = $galleryPhotos;
    }

    public function addGalleryPhoto (GalleryPhoto $galleryPhoto): void
    {
        $this->galleryPhotos[$galleryPhoto->getIndexOfPhoto()] = $galleryPhoto;
    }

    public function removeGalleryPhoto (int $index): void
    {
        unset($this->galleryPhotos[$index]);
    }

    public function getNumberOfGalleryPhotos (): int
    {
        return count($this->galleryPhotos);
    }

    public static function comparePhotos (GalleryPhoto $leftGalleryPhoto,
            GalleryPhoto $rightGalleryPhoto): int
    {
        if ((! isset($leftGalleryPhoto)) || (! isset($rightGalleryPhoto))) {
            return 0;
        }
        
        if ($leftGalleryPhoto->getIndexOfPhoto() <
                 $rightGalleryPhoto->getIndexOfPhoto()) {
            return - 1;
        } else {
            return 1;
        }
    }

    public function sortGalleryPhotos (): void
    {
        usort($this->galleryPhotos,
                array(
                        $this,
                        "comparePhotos"
                ));
    }

    public function setLanguage (Language $language): void
    {
        $this->getTitle()->setLanguage($language);
        $this->getDescription()->setLanguage($language);
    }

    private function idIsValid (): bool
    {
        return (isset($this->id) && is_int($this->id));
    }

    private function galleryPhotosAreValid (): bool
    {
        return isset($this->galleryPhotos);
    }
}
?>
