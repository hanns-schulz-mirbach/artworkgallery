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

require_once ("./model/technique.php");
require_once ("./util/language.php");
require_once ("./controller/translation_controller.php");
require_once ("./db/database.php");

class TechniqueController
{

    private $technique;

    private $database;

    public function __construct ()
    {
        $this->technique = new Technique();
        
        $this->database = new Database();
    }

    public function __destruct ()
    {
        $this->database = null;
    }

    public function getTechnique (): Technique
    {
        return $this->technique;
    }

    public function setTechnique (Technique $technique): void
    {
        $this->technique = $technique;
    }

    public function insertTechnique (): int
    {
        $insertedTranslation = $this->insertTranslation();
        
        $this->technique->setTranslation($insertedTranslation);
        
        $affectedRows = $this->database->insertTechnique($this->technique);
        return $affectedRows;
    }

    public function updateTechnique (): int
    {
        // in case of a technique update only translation related data will
        // change
        return $this->updateTranslation();
    }

    public function deleteTechnique (): int
    {
        $this->deleteTranslation();
        $affectedRows = $this->database->deleteTechnique($this->technique);
        return $affectedRows;
    }

    public function getAllTechniques (): array
    {
        return $this->database->getAllTechniques();
    }

    public function getTechniqueById (int $techniqueId): Technique
    {
        return $this->database->getTechniqueById($techniqueId);
    }

    private function insertTranslation (): Translation
    {
        $language = new Language();
        $translationController = new TranslationController($language);
        $translationController->setTranslation(
                $this->technique->getTranslation());
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
                $this->technique->getTranslation());
        $affectedRows = $translationController->updateTranslation();
        return $affectedRows;
    }

    private function deleteTranslation (): int
    {
        $language = new Language();
        $translationController = new TranslationController($language);
        $translationController->setTranslation(
                $this->technique->getTranslation());
        $affectedRows = $translationController->deleteTranslation();
        return $affectedRows;
    }
}

?>