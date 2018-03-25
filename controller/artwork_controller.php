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
require_once ("./util/language.php");
require_once ("./util/formatter.php");
require_once ("./controller/translation_controller.php");
require_once ("./db/database.php");

class ArtworkController
{

    private $artwork;

    private $database;

    public function __construct ()
    {
        $this->artwork = new Artwork();
        
        $this->database = new Database();
    }

    public function __destruct ()
    {
        $this->database = null;
    }

    public function getArtwork (): Artwork
    {
        return $this->artwork;
    }

    public function setArtwork (Artwork $artwork): void
    {
        $this->artwork = $artwork;
    }

    public function insertArtwork (): int
    {
        $insertedExplanation = $this->insertTranslation(
                $this->artwork->getExplanation());
        $insertedTitle = $this->insertTranslation($this->artwork->getTitle());
        $insertedLocation = $this->insertTranslation(
                $this->artwork->getLocation());
        
        $this->artwork->setExplanation($insertedExplanation);
        $this->artwork->setTitle($insertedTitle);
        $this->artwork->setLocation($insertedLocation);
        
        $affectedRows = 3;
        
        $affectedRows += $this->database->insertArtwork($this->artwork);
        return $affectedRows;
    }

    public function updateArtwork (): int
    {
        $affectedRows = $this->updateTranslation(
                $this->artwork->getExplanation());
        $affectedRows += $this->updateTranslation($this->artwork->getTitle());
        $affectedRows += $this->updateTranslation($this->artwork->getLocation());
        
        $affectedRows += $this->database->updateArtwork($this->artwork);
        return $affectedRows;
    }

    public function deleteArtwork (): int
    {
        $affectedRows = $this->deleteTranslation(
                $this->artwork->getExplanation());
        $affectedRows += $this->deleteTranslation($this->artwork->getTitle());
        $affectedRows += $this->deleteTranslation($this->artwork->getLocation());
        
        $affectedRows += $this->database->deleteArtwork($this->artwork);
        return $affectedRows;
    }

    public function getAllArtworks (): array
    {
        return $this->database->getAllArtworks();
    }

    public function getArtworkById (int $artworkId): Artwork
    {
        return $this->database->getArtworkById($artworkId);
    }

    public function getAllArtworksGui (TranslationController $tc,
            Formatter $formatter): string
    {
        $allArtworks = $this->getAllArtworks();
        
        $allArtworksGui = $formatter->getAllArtworksGui($allArtworks, $tc);
        
        return $allArtworksGui;
    }

    private function insertTranslation (Translation $translation): Translation
    {
        $language = new Language();
        $translationController = new TranslationController($language);
        $translationController->setTranslation($translation);
        $affectedRows = $translationController->insertTranslation();
        
        $insertedTranslation = $translationController->getTranslationById(
                $translationController->getTranslation()
                    ->getId());
        
        return $insertedTranslation;
    }

    private function updateTranslation (Translation $translation): int
    {
        $language = new Language();
        $translationController = new TranslationController($language);
        $translationController->setTranslation($translation);
        $affectedRows = $translationController->updateTranslation();
        return $affectedRows;
    }

    private function deleteTranslation (Translation $translation): int
    {
        $language = new Language();
        $translationController = new TranslationController($language);
        $translationController->setTranslation($translation);
        $affectedRows = $translationController->deleteTranslation();
        return $affectedRows;
    }
}

?>