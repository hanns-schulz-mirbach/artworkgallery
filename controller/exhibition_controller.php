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

require_once ("./model/exhibition.php");
require_once ("./util/language.php");
require_once ("./util/formatter.php");
require_once ("./controller/translation_controller.php");
require_once ("./controller/report_controller.php");
require_once ("./db/database.php");

class ExhibitionController
{

    private $exhibition;

    private $database;

    public function __construct ()
    {
        $this->exhibition = new Exhibition();
        
        $this->database = new Database();
    }

    public function __destruct ()
    {
        $this->database = null;
    }

    public function getExhibition (): Exhibition
    {
        return $this->exhibition;
    }

    public function setExhibition (Exhibition $exhibition): void
    {
        $this->exhibition = $exhibition;
    }

    public function insertExhibition (): int
    {
        $insertedTitle = $this->insertTranslation(
                $this->getExhibition()
                    ->getTitle());
        
        $this->getExhibition()->setTitle($insertedTitle);
        
        $insertedDescription = $this->insertTranslation(
                $this->getExhibition()
                    ->getDescription());
        
        $affectedRows = $this->database->insertExhibition($this->exhibition);
        return $affectedRows;
    }

    public function updateExhibition (): int
    {
        // Update translations
        $affectedRows = $this->updateTranslation(
                $this->getExhibition()
                    ->getTitle());
        $affectedRows += $this->updateTranslation(
                $this->exhibition->getDescription());
        
        // Update exhibition
        $affectedRows += $this->database->updateExhibition($this->exhibition);
        
        return $affectedRows;
    }

    public function deleteExhibition (): int
    {
        $this->deleteTranslation($this->getExhibition()
            ->getTitle());
        $this->deleteTranslation($this->getExhibition()->getDescription());
        
        $affectedRows = $this->database->deleteExhibition($this->exhibition);
        return $affectedRows;
    }

    public function getAllExhibitions (): array
    {
        return $this->database->getAllExhibitions();
    }

    public function getExhibitionById (int $exhibitionId): Exhibition
    {
        return $this->database->getExhibitionById($exhibitionId);
    }

    public function getAllExhibitionsGui (TranslationController $tc,
            Formatter $formatter): string
    {
        $allExhibitions = $this->getAllExhibitions();
        
        $allExhibitionsGui = $formatter->getAllExhibitionsGui($allExhibitions,
                $tc);
        
        return $allExhibitionsGui;
    }

    public function getExhibitionGui (TranslationController $tc,
            Formatter $formatter): string
    {
        $reportController = new ReportController();
        $exhibitionReports = $reportController->getAllReportsForExhibition(
                $this->getExhibition());
        $exhibitionGui = $formatter->getExhibitionGui($this->getExhibition(),
                $exhibitionReports, $tc);
        
        return $exhibitionGui;
    }

    public function getAllExhibitionSelectionGui (string $guiName,
            Formatter $formatter, int $exhibitionId = -1): string
    {
        $allExhibitions = $this->getAllExhibitions();
        
        $allExhibitionsSelectionGui = $formatter->getExhibitionsForSelection(
                $allExhibitions, $guiName, $exhibitionId);
        
        return $allExhibitionsSelectionGui;
    }

    private function insertTranslation (Translation $translation): Translation
    {
        $translationController = new TranslationController(
                $translation->getLanguage());
        $translationController->setTranslation($translation);
        $affectedRows = $translationController->insertTranslation();
        
        $insertedTranslation = $translationController->getTranslationById(
                $translationController->getTranslation()
                    ->getId());
        
        return $insertedTranslation;
    }

    private function updateTranslation (Translation $translation): int
    {
        $translationController = new TranslationController(
                $translation->getLanguage());
        $translationController->setTranslation($translation);
        $affectedRows = $translationController->updateTranslation();
        return $affectedRows;
    }

    private function deleteTranslation (Translation $translation): int
    {
        $translationController = new TranslationController(
                $translation->getLanguage());
        $translationController->setTranslation($translation);
        $affectedRows = $translationController->deleteTranslation();
        return $affectedRows;
    }
}

?>