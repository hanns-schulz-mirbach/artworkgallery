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

require_once ("./model/material.php");
require_once ("./util/language.php");
require_once ("./controller/translation_controller.php");
require_once ("./db/database.php");

class MaterialController
{

    private $material;

    private $database;

    public function __construct ()
    {
        $this->material = new Material();
        
        $this->database = new Database();
    }

    public function __destruct ()
    {
        $this->database = null;
    }

    public function getMaterial (): Material
    {
        return $this->material;
    }

    public function setMaterial (Material $material): void
    {
        $this->material = $material;
    }

    public function insertMaterial (): int
    {
        $insertedTranslation = $this->insertTranslation();
        
        $this->material->setTranslation($insertedTranslation);
        
        $affectedRows = $this->database->insertMaterial($this->material);
        return $affectedRows;
    }

    public function updateMaterial (): int
    {
        // in case of a material update only translation related data will
        // change
        return $this->updateTranslation();
    }

    public function deleteMaterial (): int
    {
        $this->deleteTranslation();
        $affectedRows = $this->database->deleteMaterial($this->material);
        return $affectedRows;
    }

    public function getAllMaterials (): array
    {
        return $this->database->getAllMaterials();
    }

    public function getMaterialById (int $materialId): Material
    {
        return $this->database->getMaterialById($materialId);
    }

    private function translationExistsInDatabase (): bool
    {
        return $this->translationController->translationExistsInDatabase(
                $this->material->getTranslation());
    }

    private function insertTranslation (): Translation
    {
        $language = new Language();
        $translationController = new TranslationController($language);
        $translationController->setTranslation(
                $this->material->getTranslation());
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
                $this->material->getTranslation());
        $affectedRows = $translationController->updateTranslation();
        return $affectedRows;
    }

    private function deleteTranslation (): int
    {
        $language = new Language();
        $translationController = new TranslationController($language);
        $translationController->setTranslation(
                $this->material->getTranslation());
        $affectedRows = $translationController->deleteTranslation();
        return $affectedRows;
    }
}

?>