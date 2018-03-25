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

require_once ("./model/report.php");
require_once ("./model/exhibition.php");
require_once ("./util/language.php");
require_once ("./util/formatter.php");
require_once ("./controller/translation_controller.php");
require_once ("./db/database.php");

class ReportController
{

    private $report;

    private $database;

    public function __construct ()
    {
        $this->report = new Report();
        
        $this->database = new Database();
    }

    public function __destruct ()
    {
        $this->database = null;
    }

    public function getReport (): Report
    {
        return $this->report;
    }

    public function setReport (Report $report): void
    {
        $this->report = $report;
    }

    public function insertReport (): int
    {
        $insertedTitle = $this->insertTranslation(
                $this->report->getTitleTranslation());
        $insertedText = $this->insertTranslation(
                $this->report->getTextTranslation());
        
        $this->report->setTitleTranslation($insertedTitle);
        $this->report->setTextTranslation($insertedText);
        
        $affectedRows = $this->database->insertReport($this->report);
        return $affectedRows;
    }

    public function updateReport (): int
    {
        $affectedRowsTitle = $this->updateTranslation(
                $this->report->getTitleTranslation());
        $affectedRowsText = $this->updateTranslation(
                $this->report->getTextTranslation());
        
        $affectedRows = $this->database->updateReport($this->report);
        return $affectedRows;
    }

    public function deleteReport (): int
    {
        $affectedRowsTitle = $this->deleteTranslation(
                $this->report->getTitleTranslation());
        $affectedRowsText = $this->deleteTranslation(
                $this->report->getTextTranslation());
        
        $affectedRows = $this->database->deleteReport($this->report);
        return $affectedRows;
    }

    public function deleteObsoleteReports (): int
    {
        $affectedRows = $this->deleteObsoleteTranslations();
        
        $affectedRows += $this->database->deleteObsoleteReports();
        
        return $affectedRows;
    }

    public function getAllReports (): array
    {
        return $this->database->getAllReports();
    }
    
    public function getAllReportsForExhibition (Exhibition $exhibition): array
    {
        return $this->database->getAllReportsForExhibition($exhibition->getId());
    }

    public function getReportById (int $reportId): Report
    {
        return $this->database->getReportById($reportId);
    }

    public function getAllReportsGui (TranslationController $tc,
            Formatter $formatter): string
    {
        $allReports = $this->getAllReports();
        
        $allReportsGui = $formatter->getAllReportsGui($allReports, $tc);
        
        return $allReportsGui;
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

    private function deleteObsoleteTranslations (): int
    {
        $affectedRows = $this->database->deleteObsoleteReportTranslations();
        return $affectedRows;
    }
}

?>