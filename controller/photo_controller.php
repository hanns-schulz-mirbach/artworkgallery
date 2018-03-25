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
require_once ("./model/artwork.php");
require_once ("./util/formatter.php");
require_once ("./controller/translation_controller.php");
require_once ("./db/database.php");

class PhotoController
{

    private $photo;

    private $database;

    public function __construct ()
    {
        $this->photo = new Photo();
        $this->database = new Database();
    }

    public function __destruct ()
    {
        $this->database = null;
    }

    public function getPhoto (): Photo
    {
        return $this->photo;
    }

    public function setPhoto (Photo $photo): void
    {
        $this->photo = $photo;
    }

    public function insertPhoto (): int
    {
        $affectedRows = $this->database->insertPhoto($this->photo);
        return $affectedRows;
    }

    public function updatePhoto (): int
    {
        $affectedRows = $this->database->updatePhoto($this->photo);
        return $affectedRows;
    }

    public function deletePhoto (): int
    {
        $affectedRows = $this->database->deletePhoto($this->photo);
        return $affectedRows;
    }

    public function getAllPhotos (): array
    {
        $allPhotos = [];
        
        $allPhotos = $this->database->getAllPhotos();
        
        return $allPhotos;
    }
    
    public function getAllOtherPhotos (): array
    {
        $allOtherPhotos = [];
        
        $allOtherPhotos = $this->database->getAllOtherPhotos();
        
        return $allOtherPhotos;
    }

    public function getPhotoById (int $photoId): Photo
    {
        return $this->database->getPhotoById($photoId);
    }
    
    public function getOtherPhotoById (int $photoId): Photo
    {
        return $this->database->getOtherPhotoById($photoId);
    }

    public function getRandomPhoto (): Photo
    {
        return $this->database->getRandomPhoto();
    }

    public function getPhotosForArtwork (Artwork $artwork): array
    {
        $allPhotos = [];
        
        $allPhotos = $this->database->getPhotosForArtwork($artwork->getId());
        
        return $allPhotos;
    }

    public function getGuiForThreeDifferentPhotos (TranslationController $tc,
            Formatter $formatter): string
    {
        $numberOfPhotos = $this->database->getNumberOfPhotos();
        $gui = "";
        
        if ($numberOfPhotos > 3) {
            $photo1 = $this->getRandomPhoto();
            $caption1 = $tc->getShortCaptionForPhoto($photo1);
            $photoGui1 = $formatter->getPhotoGui($photo1, $caption1);
            
            $newPhotoFound = false;
            while (! $newPhotoFound) {
                $photo2 = $this->getRandomPhoto();
                if ($photo2->getId() != $photo1->getId()) {
                    $newPhotoFound = true;
                }
            }
            $caption2 = $tc->getShortCaptionForPhoto($photo2);
            $photoGui2 = $formatter->getPhotoGui($photo2, $caption2);
            
            $newPhotoFound = false;
            while (! $newPhotoFound) {
                $photo3 = $this->getRandomPhoto();
                if (($photo3->getId() != $photo2->getId()) &&
                         ($photo3->getId() != $photo1->getId())) {
                    $newPhotoFound = true;
                }
            }
            $caption3 = $tc->getShortCaptionForPhoto($photo3);
            $photoGui3 = $formatter->getPhotoGui($photo3, $caption3);
            
            $guiHeader = '<div class="image-set"><br /><br />';
            $guiBody = $photoGui1 . $photoGui2 . $photoGui3;
            $guiFooter = '</div>';
            
            $gui = $guiHeader . $guiBody . $guiFooter;
        }
        
        return $gui;
    }

    public function getGuiForPhoto (TranslationController $tc,
            Formatter $formatter, bool $addCaption = false): string
    {
        $gui = "";
        
        $caption = "";
        
        if ($addCaption) {
            $caption = $tc->getCaptionForPhoto($this->photo);
        }
        
        $photoGui = $formatter->getPhotoGui($this->photo, $caption);
        
        $guiHeader = '<div class="fullscalephoto">';
        $guiBody = $photoGui;
        $guiFooter = '</div>';
        
        $gui = $guiHeader . $guiBody . $guiFooter;
        
        return $gui;
    }
}

?>