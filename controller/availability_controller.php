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

require_once ("./model/availability.php");
require_once ("./util/language.php");
require_once ("./controller/translation_controller.php");
require_once ("./db/database.php");

class AvailabilityController
{

    private $availability;

    private $database;

    public function __construct ()
    {
        $this->availability = new Availability();
        
        $this->database = new Database();
    }

    public function __destruct ()
    {
        $this->database = null;
    }

    public function getAvailability (): Availability
    {
        return $this->availability;
    }

    public function setAvailability (Availability $availability): void
    {
        $this->availability = $availability;
    }

    public function insertAvailability (): int
    {
        $insertedTranslation = $this->insertTranslation();
        
        $this->availability->setTranslation($insertedTranslation);
        
        $affectedRows = $this->database->insertAvailability($this->availability);
        return $affectedRows;
    }

    public function updateAvailability (): int
    {
        // in case of an availability update only translation related data will change
        return $this->updateTranslation();
    }

    public function deleteAvailability (): int
    {
        $this->deleteTranslation();
        $affectedRows = $this->database->deleteAvailability($this->availability);
        return $affectedRows;
    }

    public function getAllAvailabilities (): array
    {
        return $this->database->getAllAvailabilities();
    }

    public function getAvailabilityById (int $availabilityId): Availability
    {
        return $this->database->getAvailabilityById($availabilityId);
    }

    private function translationExistsInDatabase (): bool
    {
        return $this->translationController->translationExistsInDatabase(
                $this->availability->getTranslation());
    }

    private function insertTranslation (): Translation
    {
        $language = new Language();
        $translationController = new TranslationController($language);
        $translationController->setTranslation(
                $this->availability->getTranslation());
        $affectedRows = $translationController->insertTranslation();
        
        $insertedTranslation = $translationController->getTranslationById(
                $translationController->getTranslation()
                ->getId());
        
        return $insertedTranslation;
    }

    private function updateTranslation (): int
    {
        $language = new Language();
        $translationController = new TranslationController($language);
        $translationController->setTranslation(
                $this->availability->getTranslation());
        $affectedRows = $translationController->updateTranslation();
        return $affectedRows;
    }
    
    private function deleteTranslation (): int
    {
        $language = new Language();
        $translationController = new TranslationController($language);
        $translationController->setTranslation(
                $this->availability->getTranslation());
        $affectedRows = $translationController->deleteTranslation();
        return $affectedRows;
    }
}

?>