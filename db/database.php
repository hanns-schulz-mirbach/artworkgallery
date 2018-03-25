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

require_once ("./model/translation.php");
require_once ("./util/translation_type.php");
require_once ("./model/availability.php");
require_once ("./model/material.php");
require_once ("./model/technique.php");
require_once ("./model/artist.php");
require_once ("./model/exhibition.php");
require_once ("./model/report.php");
require_once ("./model/artwork.php");
require_once ("./model/photo.php");
require_once ("./model/gallery.php");
require_once ("./model/gallery_photo.php");

class Database
{

    private $dbHost;

    private $dbName;

    private $dbUser;

    private $dbPassword;

    private $mySQLDatabase;

    private $dbPort;

    public function __construct ()
    {
        
        
        $this->dbHost = 'your-db-host';
        $this->dbName = 'your-db-name';
        this->dbUser = 'your-db-user;
        this->dbPassword = 'your-db-passwd';
        $this->dbPort = 3306;
        
        
        // $this->mySQLDatabase = new mysqli($this->dbHost, $this->dbUser,
        // $this->dbPassword, $this->dbName, $this->dbPort);
        
        $this->mySQLDatabase = new mysqli($this->dbHost, $this->dbUser,
                $this->dbPassword, $this->dbName);
        
        if ($this->mySQLDatabase->connect_errno) {
            echo "Keine Verbindung zur MySQL Datenbank: (" .
                    $this->mySQLDatabase->connect_errno . ") " .
                    $this->mySQLDatabase->connect_error;
        }
    }

    public function __destruct ()
    {
        $this->mySQLDatabase = null;
    }

    public function insertTranslation (Translation $translation): int
    {
        $sqlStatement = "INSERT INTO translation (code, type, text_de, text_en) values ('" .
                trim($translation->getCode()) . "' , '" .
                $translation->getType()->getTypeCode() . "' , '" .
                trim($translation->getEncodedTranslation_de()) . "' , '" .
                trim($translation->getEncodedTranslation_en()) . "' )";
        
        $this->executeSQLStatement($sqlStatement, "Übersetzungsdaten");
        
        $generatedTranslationId = $this->mySQLDatabase->insert_id;
        $translation->setId($generatedTranslationId);
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function updateTranslation (Translation $translation): int
    {
        $sqlStatement = "UPDATE translation SET " . "code = '" .
                trim($translation->getCode()) . "', " . "type = " .
                $translation->getType()->getTypeCode() . ", " . "text_de = '" .
                trim($translation->getEncodedTranslation_de()) . "', " .
                "text_en = '" . trim($translation->getEncodedTranslation_en()) .
                "' " . " WHERE id = " . $translation->getId();
        
        $this->executeSQLStatement($sqlStatement, "Übersetzungsdaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deleteTranslation (Translation $translation): int
    {
        $sqlStatement = "DELETE FROM translation WHERE id = " .
                $translation->getId();
        
        $this->executeSQLStatement($sqlStatement, "Übersetzungsdaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deleteObsoleteReportTranslations (): int
    {
        $sqlStatement = "DELETE FROM translation WHERE id IN  (SELECT text_translation FROM report WHERE obsolescence_date < NOW() UNION DISTINCT SELECT title_translation FROM report WHERE obsolescence_date < NOW())";
        
        $this->executeSQLStatement($sqlStatement, "Übersetzungsdaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deleteAllSystemTranslations (): int
    {
        $sqlStatement = "DELETE FROM translation WHERE type=1";
        
        $this->executeSQLStatement($sqlStatement, "Übersetzungsdaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deleteUserTranslationsByCode (string $code): int
    {
        $sqlStatement = "DELETE FROM translation WHERE type=2 AND code='" . $code .
                "'";
        
        $this->executeSQLStatement($sqlStatement, "Übersetzungsdaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function getAllTranslationsByType (TranslationType $translationType): array
    {
        if ($translationType->isEndUserText()) {
            $sqlStatement = "SELECT * from translation where type=2 ORDER BY code";
        } elseif ($translationType->isSystemText()) {
            $sqlStatement = "SELECT * from translation where type=1 ORDER BY code";
        }
        
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        $translationsResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $translation = $this->extractTranslationFromResultSet($resultRow);
            $translationsResultArray[$i] = $translation;
            $i ++;
        }
        
        $resultSet->free();
        
        return $translationsResultArray;
    }

    public function getTranslationById (int $translationId): Translation
    {
        $sqlStatement = "SELECT * from translation where id=" . $translationId;
        
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        if ($resultSet->num_rows == 1) {
            $resultRow = $resultSet->fetch_assoc();
            $translation = $this->extractTranslationFromResultSet($resultRow);
            $resultSet->free();
            
            return $translation;
        } else {
            // there is either no matching translation or the result is
            // ambiguous
            $resultSet->free();
            return $translation = new Translation();
        }
    }

    public function getTranslationByCode (string $code): Translation
    {
        $sqlStatement = "SELECT * from translation where code= '" . trim($code) .
                "'";
        
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        if ($resultSet->num_rows == 1) {
            $resultRow = $resultSet->fetch_assoc();
            $translation = $this->extractTranslationFromResultSet($resultRow);
            $resultSet->free();
            
            return $translation;
        } else {
            // there is either no matching translation or the result is
            // ambiguous
            $resultSet->free();
            return $translation = new Translation();
        }
    }

    public function getEndUserTtranslationByCode (string $code): Translation
    {
        $sqlStatement = "SELECT * from translation where code= '" . trim($code) .
                "' AND type=2";
        
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        if ($resultSet->num_rows == 1) {
            $resultRow = $resultSet->fetch_assoc();
            $translation = $this->extractTranslationFromResultSet($resultRow);
            $resultSet->free();
            
            return $translation;
        } else {
            // there is either no matching translation or the result is
            // ambiguous
            $resultSet->free();
            return $translation = new Translation();
        }
    }

    public function translationExistsInDatabase (Translation $translation): bool
    {
        $sqlStatement = 'select count(*) as objectcount from translation t where t.id=' .
                $translation->getId();
        $objectCount = $this->getObjectCountFromDatabase($sqlStatement);
        if ($objectCount == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function insertArtist (Artist $artist): int
    {
        $sqlStatement = "INSERT INTO artist (first_name, last_name, date_of_birth, e_mail, telephone, cellphone, address) values ('" .
                trim($artist->getFirstname()) . "' , '" .
                trim($artist->getLastname()) . "' , '" .
                $artist->getBirthdate()->format('Y-m-d') . "' , '" .
                trim($artist->getMail()) . "' , '" .
                trim($artist->getTelephone()) . "' , '" .
                trim($artist->getCellphone()) . "' , '" .
                trim($artist->getAddress()) . "' )";
        
        $this->executeSQLStatement($sqlStatement, "Künstlerdaten");
        
        $generatedArtistId = $this->mySQLDatabase->insert_id;
        $artist->setId($generatedArtistId);
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function updateArtist (Artist $artist): int
    {
        $birthdate = $artist->getBirthdate()->format('Y-m-d');
        $sqlStatement = "UPDATE artist SET " . "first_name = '" .
                trim($artist->getFirstname()) . "', " . "last_name = '" .
                trim($artist->getLastname()) . "', " . "date_of_birth = '" .
                "$birthdate" . "', " . "telephone = '" .
                trim($artist->getTelephone()) . "', " . "cellphone = '" .
                trim($artist->getCellphone()) . "', " . "address = '" .
                trim($artist->getAddress()) . "', " . "e_mail = '" .
                trim($artist->getMail()) . "' " . " WHERE id =" .
                $artist->getId();
        
        $this->executeSQLStatement($sqlStatement, "Künstlerdaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deleteArtist (Artist $artist): int
    {
        $sqlStatement = "DELETE FROM artist WHERE id =" . $artist->getId();
        
        $this->executeSQLStatement($sqlStatement, "Künstlerdaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function getAllArtists (): array
    {
        $sqlStatement = "SELECT * from artist ORDER BY last_name";
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        $artistsResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $artist = $this->extractArtistFromResultSet($resultRow);
            $artistsResultArray[$i] = $artist;
            $i ++;
        }
        
        $resultSet->free();
        
        return $artistsResultArray;
    }

    public function getArtistById (int $artistId): Artist
    {
        $sqlStatement = "SELECT * from artist where id=" . $artistId;
        
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        if ($resultSet->num_rows == 1) {
            $resultRow = $resultSet->fetch_assoc();
            $artist = $this->extractArtistFromResultSet($resultRow);
            $resultSet->free();
            
            return $artist;
        } else {
            // there is either no matching translation or the result is
            // ambiguous
            $resultSet->free();
            return $artist = new Artist();
        }
    }

    public function insertAvailability (Availability $availability): int
    {
        $sqlStatement = "INSERT INTO availability (translation) values ( " .
                $availability->getTranslation()->getId() . " )";
        
        $this->executeSQLStatement($sqlStatement, "Verfügbarkeit");
        
        $generatedAvailabilityId = $this->mySQLDatabase->insert_id;
        $availability->setId($generatedAvailabilityId);
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deleteAvailability (Availability $availability): int
    {
        $sqlStatement = "DELETE FROM availability WHERE id =" .
                $availability->getId();
        
        $this->executeSQLStatement($sqlStatement, "Verfügbarkeit");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function getAllAvailabilities (): array
    {
        $sqlStatement = "SELECT a.id as a_id, t.id as t_id, t.code as code, t.type as type, t.text_de as text_de, t.text_en as text_en from availability a join translation t ON t.id = a.translation ORDER BY t.code";
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        $availabilitiesResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $availability = $this->extractAvailabilityFromResultSet($resultRow);
            $availabilitiesResultArray[$i] = $availability;
            $i ++;
        }
        
        $resultSet->free();
        
        return $availabilitiesResultArray;
    }

    public function getAvailabilityById (int $availabilityId): Availability
    {
        $sqlStatement = "SELECT a.id as a_id, t.id as t_id, t.code as code, t.type as type, t.text_de as text_de, t.text_en as text_en from availability a join translation t ON t.id = a.translation  where a.id=" .
                $availabilityId;
        
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        if ($resultSet->num_rows == 1) {
            $resultRow = $resultSet->fetch_assoc();
            $availability = $this->extractAvailabilityFromResultSet($resultRow);
            $resultSet->free();
            
            return $availability;
        } else {
            // there is either no matching availability or the result is
            // ambiguous
            $resultSet->free();
            return $availability = new Availability();
        }
    }

    public function insertMaterial (Material $material): int
    {
        $sqlStatement = "INSERT INTO material (translation) values ( " .
                $material->getTranslation()->getId() . " )";
        
        $this->executeSQLStatement($sqlStatement, "Material");
        
        $generatedMaterialId = $this->mySQLDatabase->insert_id;
        $material->setId($generatedMaterialId);
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deleteMaterial (Material $material): int
    {
        $sqlStatement = "DELETE FROM material WHERE id =" . $material->getId();
        
        $this->executeSQLStatement($sqlStatement, "Material");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function getAllMaterials (): array
    {
        $sqlStatement = "SELECT m.id as m_id, t.id as t_id, t.code as code, t.type as type, t.text_de as text_de, t.text_en as text_en from material m join translation t ON t.id = m.translation ORDER BY t.code";
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        $materialsResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $material = $this->extractMaterialFromResultSet($resultRow);
            $materialsResultArray[$i] = $material;
            $i ++;
        }
        
        $resultSet->free();
        
        return $materialsResultArray;
    }

    public function getMaterialById (int $materialId): Material
    {
        $sqlStatement = "SELECT m.id as m_id, t.id as t_id, t.code as code, t.type as type, t.text_de as text_de, t.text_en as text_en from material m join translation t ON t.id = m.translation  where m.id=" .
                $materialId;
        
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        if ($resultSet->num_rows == 1) {
            $resultRow = $resultSet->fetch_assoc();
            $material = $this->extractMaterialFromResultSet($resultRow);
            $resultSet->free();
            
            return $material;
        } else {
            // there is either no matching material or the result is
            // ambiguous
            $resultSet->free();
            return $material = new Material();
        }
    }

    public function insertTechnique (Technique $technique): int
    {
        $sqlStatement = "INSERT INTO technique (translation) values ( " .
                $technique->getTranslation()->getId() . " )";
        
        $this->executeSQLStatement($sqlStatement, "Technik");
        
        $generatedTechniqueId = $this->mySQLDatabase->insert_id;
        $technique->setId($generatedTechniqueId);
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deleteTechnique (Technique $technique): int
    {
        $sqlStatement = "DELETE FROM technique WHERE id =" . $technique->getId();
        
        $this->executeSQLStatement($sqlStatement, "Technik");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function getAllTechniques (): array
    {
        $sqlStatement = "SELECT te.id as te_id, t.id as t_id, t.code as code, t.type as type, t.text_de as text_de, t.text_en as text_en from technique te join translation t ON t.id = te.translation ORDER BY t.code";
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        $techniquesResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $technique = $this->extractTechniqueFromResultSet($resultRow);
            $techniquesResultArray[$i] = $technique;
            $i ++;
        }
        
        $resultSet->free();
        
        return $techniquesResultArray;
    }

    public function getTechniqueById (int $techniqueId): Technique
    {
        $sqlStatement = "SELECT te.id as te_id, t.id as t_id, t.code as code, t.type as type, t.text_de as text_de, t.text_en as text_en from technique te join translation t ON t.id = te.translation  where te.id=" .
                $techniqueId;
        
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        if ($resultSet->num_rows == 1) {
            $resultRow = $resultSet->fetch_assoc();
            $technique = $this->extractTechniqueFromResultSet($resultRow);
            $resultSet->free();
            
            return $technique;
        } else {
            // there is either no matching technique or the result is
            // ambiguous
            $resultSet->free();
            return $technique = new Technique();
        }
    }

    public function insertExhibition (Exhibition $exhibition): int
    {
        $sqlStatement = "INSERT INTO exhibition (start, end, title_translation, description_translation, address, openinghours) values ( '" .
                $exhibition->getStart()->format('Y-m-d') . "' , '" .
                $exhibition->getEnd()->format('Y-m-d') . "' , " .
                $exhibition->getTitle()->getId() . " , " .
                $exhibition->getDescription()->getId() . " , '" .
                trim($exhibition->getAddress()) . "' , '" .
                trim($exhibition->getOpeningHours()) . "' )";
        
        $this->executeSQLStatement($sqlStatement, "Ausstellung");
        
        $generatedExhibitionId = $this->mySQLDatabase->insert_id;
        $exhibition->setId($generatedExhibitionId);
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function updateExhibition (Exhibition $exhibition): int
    {
        $sqlStatement = "UPDATE exhibition SET " . "start = '" .
                $exhibition->getStart()->format('Y-m-d') . "', " . "end = '" .
                $exhibition->getEnd()->format('Y-m-d') . "', " .
                "title_translation = " . $exhibition->getTitle()->getId() . ", " .
                "description_translation = " .
                $exhibition->getDescription()->getId() . ", " . "address = '" .
                trim($exhibition->getAddress()) . "', " . "openinghours = '" .
                trim($exhibition->getOpeningHours()) . "' " . " WHERE id =" .
                $exhibition->getId();
        
        $this->executeSQLStatement($sqlStatement, "Ausstellung");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deleteExhibition (Exhibition $exhibition): int
    {
        $sqlStatement = "DELETE FROM exhibition WHERE id =" .
                $exhibition->getId();
        
        $this->executeSQLStatement($sqlStatement, "Ausstellung");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function getAllExhibitions (): array
    {
        $sqlStatement = "SELECT e.id as e_id, e.start as e_s, e.end as e_e, e.address as e_a, e.openinghours as e_o, t.id as t_id, t.code as code, t.type as type, t.text_de as text_de, t.text_en as text_en, d.id as d_id, d.code as d_code, d.type as d_type, d.text_de as d_text_de, d.text_en as d_text_en from exhibition e JOIN translation t ON t.id = e.title_translation JOIN translation d ON d.id = e.description_translation ORDER BY e.start DESC";
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        $exhibitionsResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $exhibition = $this->extractExhibitionFromResultSet($resultRow);
            $exhibitionsResultArray[$i] = $exhibition;
            $i ++;
        }
        
        $resultSet->free();
        
        return $exhibitionsResultArray;
    }

    public function getExhibitionById (int $exhibitionId): Exhibition
    {
        $sqlStatement = "SELECT e.id as e_id, e.start as e_s, e.end as e_e, e.address as e_a, e.openinghours as e_o, t.id as t_id, t.code as code, t.type as type, t.text_de as text_de, t.text_en as text_en, d.id as d_id, d.code as d_code, d.type as d_type, d.text_de as d_text_de, d.text_en as d_text_en from exhibition e JOIN translation t ON t.id = e.title_translation JOIN translation d ON d.id = e.description_translation where e.id=" .
                $exhibitionId;
        
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        if ($resultSet->num_rows == 1) {
            $resultRow = $resultSet->fetch_assoc();
            $exhibition = $this->extractExhibitionFromResultSet($resultRow);
            $resultSet->free();
            
            return $exhibition;
        } else {
            // there is either no matching exhibition or the result is
            // ambiguous
            $resultSet->free();
            return $exhibition = new Exhibition();
        }
    }

    public function insertReport (Report $report): int
    {
        $sqlStatement = "INSERT INTO report (text_translation, title_translation, publication_date, obsolescence_date, author, exhibition) values ( " .
                $report->getTextTranslation()->getId() . " , " .
                $report->getTitleTranslation()->getId() . " , '" .
                $report->getPublicationDate()->format('Y-m-d') . "' , '" .
                $report->getObsolescenceDate()->format('Y-m-d') . "' , '" .
                trim($report->getAuthor()) . "' , " .
                $report->getExhibition()->getId() . " )";
        
        $this->executeSQLStatement($sqlStatement, "Report");
        
        $generatedReportId = $this->mySQLDatabase->insert_id;
        $report->setId($generatedReportId);
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function updateReport (Report $report): int
    {
        $sqlStatement = "UPDATE report SET " . "publication_date = '" .
                $report->getPublicationDate()->format('Y-m-d') . "', " .
                "obsolescence_date = '" .
                $report->getObsolescenceDate()->format('Y-m-d') . "', " .
                "text_translation = " . $report->getTextTranslation()->getId() .
                ", " . "title_translation = " .
                $report->getTitleTranslation()->getId() . ", " . "author = '" .
                trim($report->getAuthor()) . "', " . "exhibition = " .
                $report->getExhibition()->getId() . " WHERE id =" .
                $report->getId();
        
        $this->executeSQLStatement($sqlStatement, "Report");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deleteReport (Report $report): int
    {
        $sqlStatement = "DELETE FROM report WHERE id =" . $report->getId();
        
        $this->executeSQLStatement($sqlStatement, "Report");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deleteObsoleteReports (): int
    {
        $sqlStatement = "DELETE FROM report WHERE obsolescence_date < NOW()";
        
        $this->executeSQLStatement($sqlStatement, "Report");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function getAllReports (): array
    {
        $sqlStatement = "SELECT r.id as r_id, r.publication_date as r_pd, r.obsolescence_date as r_od, r.author as r_a, ti.id as ti_id, ti.code as ti_code, ti.type as ti_type, ti.text_de as ti_text_de, ti.text_en as ti_text_en, te.id as te_id, te.code as te_code, te.type as te_type, te.text_de as te_text_de, te.text_en as te_text_en, ex.id as ex_id, ex.start as ex_s, ex.end as ex_e, ex.address as ex_a, ex.openinghours as ex_o, t_ex.id as t_ex_id, t_ex.code as t_ex_code, t_ex.type as t_ex_type, t_ex.text_de as t_ex_text_de, t_ex.text_en as t_ex_text_en, d_ex.id as d_ex_id, d_ex.code as d_ex_code, d_ex.type as d_ex_type, d_ex.text_de as d_ex_text_de, d_ex.text_en as d_ex_text_en from report r JOIN translation ti ON ti.id = r.title_translation JOIN translation te ON te.id = r.text_translation LEFT JOIN exhibition ex on ex.id = r.exhibition LEFT JOIN translation t_ex on t_ex.id = ex.title_translation LEFT JOIN translation d_ex on d_ex.id = ex.description_translation ORDER BY r.publication_date DESC";
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        $reportsResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $report = $this->extractReportFromResultSet($resultRow);
            $reportsResultArray[$i] = $report;
            $i ++;
        }
        
        $resultSet->free();
        
        return $reportsResultArray;
    }

    public function getAllReportsForExhibition (int $exhibitionId): array
    {
        $sqlStatement = "SELECT r.id as r_id, r.publication_date as r_pd, r.obsolescence_date as r_od, r.author as r_a, ti.id as ti_id, ti.code as ti_code, ti.type as ti_type, ti.text_de as ti_text_de, ti.text_en as ti_text_en, te.id as te_id, te.code as te_code, te.type as te_type, te.text_de as te_text_de, te.text_en as te_text_en, ex.id as ex_id, ex.start as ex_s, ex.end as ex_e, ex.address as ex_a, ex.openinghours as ex_o, t_ex.id as t_ex_id, t_ex.code as t_ex_code, t_ex.type as t_ex_type, t_ex.text_de as t_ex_text_de, t_ex.text_en as t_ex_text_en, d_ex.id as d_ex_id, d_ex.code as d_ex_code, d_ex.type as d_ex_type, d_ex.text_de as d_ex_text_de, d_ex.text_en as d_ex_text_en from report r JOIN translation ti ON ti.id = r.title_translation JOIN translation te ON te.id = r.text_translation LEFT JOIN exhibition ex on ex.id = r.exhibition LEFT JOIN translation t_ex on t_ex.id = ex.title_translation LEFT JOIN translation d_ex on d_ex.id = ex.description_translation WHERE r.exhibition=" .
                $exhibitionId;
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        $reportsResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $report = $this->extractReportFromResultSet($resultRow);
            $reportsResultArray[$i] = $report;
            $i ++;
        }
        
        $resultSet->free();
        
        return $reportsResultArray;
    }

    public function getReportById (int $reportId): Report
    {
        $sqlStatement = "SELECT r.id as r_id, r.publication_date as r_pd, r.obsolescence_date as r_od, r.author as r_a, ti.id as ti_id, ti.code as ti_code, ti.type as ti_type, ti.text_de as ti_text_de, ti.text_en as ti_text_en, te.id as te_id, te.code as te_code, te.type as te_type, te.text_de as te_text_de, te.text_en as te_text_en, ex.id as ex_id, ex.start as ex_s, ex.end as ex_e, ex.address as ex_a, ex.openinghours as ex_o, t_ex.id as t_ex_id, t_ex.code as t_ex_code, t_ex.type as t_ex_type, t_ex.text_de as t_ex_text_de, t_ex.text_en as t_ex_text_en, d_ex.id as d_ex_id, d_ex.code as d_ex_code, d_ex.type as d_ex_type, d_ex.text_de as d_ex_text_de, d_ex.text_en as d_ex_text_en from report r JOIN translation ti ON ti.id = r.title_translation JOIN translation te ON te.id = r.text_translation LEFT JOIN exhibition ex on ex.id = r.exhibition LEFT JOIN translation t_ex on t_ex.id = ex.title_translation LEFT JOIN translation d_ex on d_ex.id = ex.description_translation WHERE r.id=" .
                $reportId;
        
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        if ($resultSet->num_rows == 1) {
            $resultRow = $resultSet->fetch_assoc();
            $report = $this->extractReportFromResultSet($resultRow);
            $resultSet->free();
            
            return $report;
        } else {
            // there is either no matching report or the result is
            // ambiguous
            $resultSet->free();
            return $report = new Report();
        }
    }

    public function insertArtwork (Artwork $artwork): int
    {
        $sqlStatement = "INSERT INTO artwork (width, height, depth, weight, title_translation, explanation_translation, signature_date, technique, material, price, location_translation, availability, artist, signature_name) values ( " .
                $artwork->getWidth() . " , " . $artwork->getHeight() . " , " .
                $artwork->getDepth() . " , " . $artwork->getWeight() . " , " .
                $artwork->getTitle()->getId() . " , " .
                $artwork->getExplanation()->getId() . " , '" .
                $artwork->getSignatureDate()->format("Y-m-d") . "' , " .
                $artwork->getTechnique()->getId() . " , " .
                $artwork->getMaterial()->getId() . " , " . $artwork->getPrice() .
                " , " . $artwork->getLocation()->getId() . " , " .
                $artwork->getAvailability()->getId() . " , " .
                $artwork->getArtist()->getId() . " , '" .
                $artwork->getSignatureName() . "' )";
        
        $this->executeSQLStatement($sqlStatement, "Kunstwerk");
        
        $generatedArtworkId = $this->mySQLDatabase->insert_id;
        $artwork->setId($generatedArtworkId);
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function updateArtwork (Artwork $artwork): int
    {
        $sqlStatement = "UPDATE artwork SET " . "width = " . $artwork->getWidth() .
                ", " . "height = " . $artwork->getHeight() . ", " . "depth = " .
                $artwork->getDepth() . ", " . "weight = " . $artwork->getWeight() .
                ", " . "title_translation = " . $artwork->getTitle()->getId() .
                ", " . "explanation_translation = " .
                $artwork->getExplanation()->getId() . ", " . "signature_date = '" .
                $artwork->getSignatureDate()->format("Y-m-d") . "', " .
                "technique = " . $artwork->getTechnique()->getId() . ", " .
                "material = " . $artwork->getMaterial()->getId() . ", " .
                "price = " . $artwork->getPrice() . ", " .
                "location_translation = " . $artwork->getLocation()->getId() .
                ", " . "availability = " . $artwork->getAvailability()->getId() .
                ", " . "artist = " . $artwork->getArtist()->getId() . ", " .
                "signature_name = '" . trim($artwork->getSignatureName()) . "' " .
                " WHERE id =" . $artwork->getId();
        
        $this->executeSQLStatement($sqlStatement, "Kunstwerk");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deleteArtwork (Artwork $artwork): int
    {
        $sqlStatement = "DELETE FROM artwork WHERE id =" . $artwork->getId();
        
        $this->executeSQLStatement($sqlStatement, "Kunstwerk");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function getAllArtworks (): array
    {
        $sqlStatement = "SELECT a.id as a_id, a.width as a_wi, a.height as a_h, a.depth as a_d, a.weight as a_we, a.price as a_p, a.signature_date as a_sd, a.signature_name as a_sn, a.title_translation as a_ti, t_ti.code as t_ti_c, t_ti.type as t_ti_t, t_ti.text_de as t_ti_de, t_ti.text_en as t_ti_en, a.explanation_translation as a_et, t_et.code as t_et_c, t_et.type as t_et_t, t_et.text_de as t_et_de, t_et.text_en as t_et_en, a.technique as a_te, t_te.id as t_te_id, t_te.code as t_te_c, t_te.type as t_te_t, t_te.text_de as t_te_de, t_te.text_en as t_te_en, a.material as a_m, t_ma.id as t_ma_id, t_ma.code as t_ma_c, t_ma.type as t_ma_t, t_ma.text_de as t_ma_de, t_ma.text_en as t_ma_en, a.location_translation as a_l, t_lo.code as t_lo_c, t_lo.type as t_lo_t, t_lo.text_de as t_lo_de, t_lo.text_en as t_lo_en, a.availability as a_av, t_av.id as t_av_id, t_av.code as t_av_c, t_av.type as t_av_t, t_av.text_de as t_av_de, t_av.text_en as t_av_en, a.artist as a_ar, ar.first_name as ar_fn, ar.last_name as ar_ln, ar.date_of_birth as ar_dob, ar.e_mail as ar_em, ar.telephone as ar_tel, ar.cellphone as ar_ce, ar.address as ar_a FROM artwork a JOIN translation t_ti ON t_ti.id = a.title_translation JOIN translation t_et on t_et.id = a.explanation_translation JOIN translation t_lo ON t_lo.id = a.location_translation JOIN technique te ON te.id = a.technique JOIN translation t_te ON t_te.id = te.translation JOIN material ma ON ma.id = a.material JOIN translation t_ma ON t_ma.id = ma.translation JOIN availability av ON av.id = a.availability JOIN translation t_av ON t_av.id = av.translation join artist ar on ar.id = a.artist ORDER BY t_ti.text_de";
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        $artworksResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $artwork = $this->extractArtworkFromResultSet($resultRow);
            $artworksResultArray[$i] = $artwork;
            $i ++;
        }
        
        $resultSet->free();
        
        return $artworksResultArray;
    }

    public function getArtworkById (int $artworkId): Artwork
    {
        $sqlStatement = "SELECT a.id as a_id, a.width as a_wi, a.height as a_h, a.depth as a_d, a.weight as a_we, a.price as a_p, a.signature_date as a_sd, a.signature_name as a_sn, a.title_translation as a_ti, t_ti.code as t_ti_c, t_ti.type as t_ti_t, t_ti.text_de as t_ti_de, t_ti.text_en as t_ti_en, a.explanation_translation as a_et, t_et.code as t_et_c, t_et.type as t_et_t, t_et.text_de as t_et_de, t_et.text_en as t_et_en, a.technique as a_te, t_te.id as t_te_id, t_te.code as t_te_c, t_te.type as t_te_t, t_te.text_de as t_te_de, t_te.text_en as t_te_en, a.material as a_m, t_ma.id as t_ma_id, t_ma.code as t_ma_c, t_ma.type as t_ma_t, t_ma.text_de as t_ma_de, t_ma.text_en as t_ma_en, a.location_translation as a_l, t_lo.code as t_lo_c, t_lo.type as t_lo_t, t_lo.text_de as t_lo_de, t_lo.text_en as t_lo_en, a.availability as a_av, t_av.id as t_av_id, t_av.code as t_av_c, t_av.type as t_av_t, t_av.text_de as t_av_de, t_av.text_en as t_av_en, a.artist as a_ar, ar.first_name as ar_fn, ar.last_name as ar_ln, ar.date_of_birth as ar_dob, ar.e_mail as ar_em, ar.telephone as ar_tel, ar.cellphone as ar_ce, ar.address as ar_a FROM artwork a JOIN translation t_ti ON t_ti.id = a.title_translation JOIN translation t_et on t_et.id = a.explanation_translation JOIN translation t_lo ON t_lo.id = a.location_translation JOIN technique te ON te.id = a.technique JOIN translation t_te ON t_te.id = te.translation JOIN material ma ON ma.id = a.material JOIN translation t_ma ON t_ma.id = ma.translation JOIN availability av ON av.id = a.availability JOIN translation t_av ON t_av.id = av.translation join artist ar on ar.id = a.artist WHERE a.id=" .
                $artworkId;
        
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        if ($resultSet->num_rows == 1) {
            $resultRow = $resultSet->fetch_assoc();
            $artwork = $this->extractArtworkFromResultSet($resultRow);
            $resultSet->free();
            
            return $artwork;
        } else {
            // there is either no matching artwork or the result is
            // ambiguous
            $resultSet->free();
            return $artwork = new Artwork();
        }
    }

    public function insertPhoto (Photo $photo): int
    {
        $sqlStatement = "INSERT INTO photo (filename, artwork, upload_date) values ( '" .
                $photo->getFilename() . "' , " . $photo->getArtwork()->getId() .
                " , '" . $photo->getUploadDate()->format('Y-m-d') . "' )";
        
        $this->executeSQLStatement($sqlStatement, "Photo");
        
        $generatedPhotoId = $this->mySQLDatabase->insert_id;
        $photo->setId($generatedPhotoId);
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function updatePhoto (Photo $photo): int
    {
        $sqlStatement = "UPDATE photo SET " . "upload_date = '" .
                $photo->getUploadDate()->format('Y-m-d') . "', " . "filename = '" .
                $photo->getFilename() . "', " . "artwork = " .
                $photo->getArtwork()->getId() . " WHERE id =" . $photo->getId();
        
        $this->executeSQLStatement($sqlStatement, "Photo");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deletePhoto (Photo $photo): int
    {
        $sqlStatement = "DELETE FROM photo WHERE id =" . $photo->getId();
        
        $this->executeSQLStatement($sqlStatement, "Photo");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function getAllPhotos (): array
    {
        $sqlStatement = "SELECT p.id as p_id, p.upload_date as p_ud, p.filename as p_f, a.id as a_id, a.width as a_wi, a.height as a_h, a.depth as a_d, a.weight as a_we, a.price as a_p, a.signature_date as a_sd, a.signature_name as a_sn, a.title_translation as a_ti, t_ti.code as t_ti_c, t_ti.type as t_ti_t, t_ti.text_de as t_ti_de, t_ti.text_en as t_ti_en, a.explanation_translation as a_et, t_et.code as t_et_c, t_et.type as t_et_t, t_et.text_de as t_et_de, t_et.text_en as t_et_en, a.technique as a_te, t_te.id as t_te_id, t_te.code as t_te_c, t_te.type as t_te_t, t_te.text_de as t_te_de, t_te.text_en as t_te_en, a.material as a_m, t_ma.id as t_ma_id, t_ma.code as t_ma_c, t_ma.type as t_ma_t, t_ma.text_de as t_ma_de, t_ma.text_en as t_ma_en, a.location_translation as a_l, t_lo.code as t_lo_c, t_lo.type as t_lo_t, t_lo.text_de as t_lo_de, t_lo.text_en as t_lo_en, a.availability as a_av, t_av.id as t_av_id, t_av.code as t_av_c, t_av.type as t_av_t, t_av.text_de as t_av_de, t_av.text_en as t_av_en, a.artist as a_ar, ar.first_name as ar_fn, ar.last_name as ar_ln, ar.date_of_birth as ar_dob, ar.e_mail as ar_em, ar.telephone as ar_tel, ar.cellphone as ar_ce, ar.address as ar_a from photo p join artwork a ON a.id = p.artwork JOIN translation t_ti ON t_ti.id = a.title_translation JOIN translation t_et on t_et.id = a.explanation_translation JOIN translation t_lo ON t_lo.id = a.location_translation JOIN technique te ON te.id = a.technique JOIN translation t_te ON t_te.id = te.translation JOIN material ma ON ma.id = a.material JOIN translation t_ma ON t_ma.id = ma.translation JOIN availability av ON av.id = a.availability JOIN translation t_av ON t_av.id = av.translation join artist ar on ar.id = a.artist ORDER BY t_ti.text_de, p.filename";
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        $photosResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $photo = $this->extractPhotoFromResultSet($resultRow);
            $photosResultArray[$i] = $photo;
            $i ++;
        }
        
        $resultSet->free();
        
        return $photosResultArray;
    }

    public function getAllOtherPhotos (): array
    {
        $sqlStatement = "SELECT p.id as p_id, p.upload_date as p_ud, p.filename as p_f FROM photo p WHERE p.artwork = -1 ORDER BY p.filename";
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        $photosResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $photo = $this->extractOtherPhotoFromResultSet($resultRow);
            $photosResultArray[$i] = $photo;
            $i ++;
        }
        
        $resultSet->free();
        
        return $photosResultArray;
    }

    public function getPhotosForArtwork (int $artworkId): array
    {
        $sqlStatement = "SELECT p.id as p_id, p.upload_date as p_ud, p.filename as p_f, a.id as a_id, a.width as a_wi, a.height as a_h, a.depth as a_d, a.weight as a_we, a.price as a_p, a.signature_date as a_sd, a.signature_name as a_sn, a.title_translation as a_ti, t_ti.code as t_ti_c, t_ti.type as t_ti_t, t_ti.text_de as t_ti_de, t_ti.text_en as t_ti_en, a.explanation_translation as a_et, t_et.code as t_et_c, t_et.type as t_et_t, t_et.text_de as t_et_de, t_et.text_en as t_et_en, a.technique as a_te, t_te.id as t_te_id, t_te.code as t_te_c, t_te.type as t_te_t, t_te.text_de as t_te_de, t_te.text_en as t_te_en, a.material as a_m, t_ma.id as t_ma_id, t_ma.code as t_ma_c, t_ma.type as t_ma_t, t_ma.text_de as t_ma_de, t_ma.text_en as t_ma_en, a.location_translation as a_l, t_lo.code as t_lo_c, t_lo.type as t_lo_t, t_lo.text_de as t_lo_de, t_lo.text_en as t_lo_en, a.availability as a_av, t_av.id as t_av_id, t_av.code as t_av_c, t_av.type as t_av_t, t_av.text_de as t_av_de, t_av.text_en as t_av_en, a.artist as a_ar, ar.first_name as ar_fn, ar.last_name as ar_ln, ar.date_of_birth as ar_dob, ar.e_mail as ar_em, ar.telephone as ar_tel, ar.cellphone as ar_ce, ar.address as ar_a from photo p join artwork a ON a.id = p.artwork JOIN translation t_ti ON t_ti.id = a.title_translation JOIN translation t_et on t_et.id = a.explanation_translation JOIN translation t_lo ON t_lo.id = a.location_translation JOIN technique te ON te.id = a.technique JOIN translation t_te ON t_te.id = te.translation JOIN material ma ON ma.id = a.material JOIN translation t_ma ON t_ma.id = ma.translation JOIN availability av ON av.id = a.availability JOIN translation t_av ON t_av.id = av.translation join artist ar on ar.id = a.artist WHERE p.artwork=" .
                $artworkId;
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        $photosResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $photo = $this->extractPhotoFromResultSet($resultRow);
            $photosResultArray[$i] = $photo;
            $i ++;
        }
        
        $resultSet->free();
        
        return $photosResultArray;
    }

    public function getPhotoById (int $photoId): Photo
    {
        $sqlStatement = "SELECT p.id as p_id, p.upload_date as p_ud, p.filename as p_f, a.id as a_id, a.width as a_wi, a.height as a_h, a.depth as a_d, a.weight as a_we, a.price as a_p, a.signature_date as a_sd, a.signature_name as a_sn, a.title_translation as a_ti, t_ti.code as t_ti_c, t_ti.type as t_ti_t, t_ti.text_de as t_ti_de, t_ti.text_en as t_ti_en, a.explanation_translation as a_et, t_et.code as t_et_c, t_et.type as t_et_t, t_et.text_de as t_et_de, t_et.text_en as t_et_en, a.technique as a_te, t_te.id as t_te_id, t_te.code as t_te_c, t_te.type as t_te_t, t_te.text_de as t_te_de, t_te.text_en as t_te_en, a.material as a_m, t_ma.id as t_ma_id, t_ma.code as t_ma_c, t_ma.type as t_ma_t, t_ma.text_de as t_ma_de, t_ma.text_en as t_ma_en, a.location_translation as a_l, t_lo.code as t_lo_c, t_lo.type as t_lo_t, t_lo.text_de as t_lo_de, t_lo.text_en as t_lo_en, a.availability as a_av, t_av.id as t_av_id, t_av.code as t_av_c, t_av.type as t_av_t, t_av.text_de as t_av_de, t_av.text_en as t_av_en, a.artist as a_ar, ar.first_name as ar_fn, ar.last_name as ar_ln, ar.date_of_birth as ar_dob, ar.e_mail as ar_em, ar.telephone as ar_tel, ar.cellphone as ar_ce, ar.address as ar_a from photo p join artwork a ON a.id = p.artwork JOIN translation t_ti ON t_ti.id = a.title_translation JOIN translation t_et on t_et.id = a.explanation_translation JOIN translation t_lo ON t_lo.id = a.location_translation JOIN technique te ON te.id = a.technique JOIN translation t_te ON t_te.id = te.translation JOIN material ma ON ma.id = a.material JOIN translation t_ma ON t_ma.id = ma.translation JOIN availability av ON av.id = a.availability JOIN translation t_av ON t_av.id = av.translation join artist ar on ar.id = a.artist where p.id=" .
                $photoId;
        
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        if ($resultSet->num_rows == 1) {
            $resultRow = $resultSet->fetch_assoc();
            $photo = $this->extractPhotoFromResultSet($resultRow);
            $resultSet->free();
        } else {
            // there is either no matching photo or the result is
            // ambiguous
            $resultSet->free();
            $photo = new Photo();
        }
        return $photo;
    }

    public function getOtherPhotoById (int $photoId): Photo
    {
        $sqlStatement = "SELECT p.id as p_id, p.upload_date as p_ud, p.filename as p_f FROM photo p WHERE p.id=" .
                $photoId;
        
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        if ($resultSet->num_rows == 1) {
            $resultRow = $resultSet->fetch_assoc();
            $photo = $this->extractOtherPhotoFromResultSet($resultRow);
            $resultSet->free();
        } else {
            // there is either no matching photo or the result is
            // ambiguous
            $resultSet->free();
            $photo = new Photo();
        }
        return $photo;
    }

    public function getRandomPhoto (): Photo
    {
        $sqlStatement = "SELECT p.id as p_id, p.upload_date as p_ud, p.filename as p_f, a.id as a_id, a.width as a_wi, a.height as a_h, a.depth as a_d, a.weight as a_we, a.price as a_p, a.signature_date as a_sd, a.signature_name as a_sn, a.title_translation as a_ti, t_ti.code as t_ti_c, t_ti.type as t_ti_t, t_ti.text_de as t_ti_de, t_ti.text_en as t_ti_en, a.explanation_translation as a_et, t_et.code as t_et_c, t_et.type as t_et_t, t_et.text_de as t_et_de, t_et.text_en as t_et_en, a.technique as a_te, t_te.id as t_te_id, t_te.code as t_te_c, t_te.type as t_te_t, t_te.text_de as t_te_de, t_te.text_en as t_te_en, a.material as a_m, t_ma.id as t_ma_id, t_ma.code as t_ma_c, t_ma.type as t_ma_t, t_ma.text_de as t_ma_de, t_ma.text_en as t_ma_en, a.location_translation as a_l, t_lo.code as t_lo_c, t_lo.type as t_lo_t, t_lo.text_de as t_lo_de, t_lo.text_en as t_lo_en, a.availability as a_av, t_av.id as t_av_id, t_av.code as t_av_c, t_av.type as t_av_t, t_av.text_de as t_av_de, t_av.text_en as t_av_en, a.artist as a_ar, ar.first_name as ar_fn, ar.last_name as ar_ln, ar.date_of_birth as ar_dob, ar.e_mail as ar_em, ar.telephone as ar_tel, ar.cellphone as ar_ce, ar.address as ar_a from photo p join artwork a ON a.id = p.artwork JOIN translation t_ti ON t_ti.id = a.title_translation JOIN translation t_et on t_et.id = a.explanation_translation JOIN translation t_lo ON t_lo.id = a.location_translation JOIN technique te ON te.id = a.technique JOIN translation t_te ON t_te.id = te.translation JOIN material ma ON ma.id = a.material JOIN translation t_ma ON t_ma.id = ma.translation JOIN availability av ON av.id = a.availability JOIN translation t_av ON t_av.id = av.translation join artist ar on ar.id = a.artist ORDER BY RAND() LIMIT 1";
        
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        $resultRow = $resultSet->fetch_assoc();
        $photo = $this->extractPhotoFromResultSet($resultRow);
        $resultSet->free();
        
        return $photo;
    }

    public function getNumberOfPhotos (): int
    {
        $sqlStatement = 'select count(*) as objectcount from photo';
        $objectCount = $this->getObjectCountFromDatabase($sqlStatement);
        return $objectCount;
    }

    public function insertGallery (Gallery $gallery): int
    {
        $sqlStatement = "INSERT INTO gallery (title_translation, description_translation) values ( " .
                $gallery->getTitle()->getId() . " , " .
                $gallery->getDescription()->getId() . " )";
        
        $this->executeSQLStatement($sqlStatement, "Gallery");
        
        $generatedGalleryId = $this->mySQLDatabase->insert_id;
        $gallery->setId($generatedGalleryId);
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function updateGallery (Gallery $gallery): int
    {
        $sqlStatement = "UPDATE gallery SET " . "title_translation = " .
                $gallery->getTitle()->getId() . ", " .
                "description_translation = " .
                $gallery->getDescription()->getId() . " WHERE id =" .
                $gallery->getId();
        
        $this->executeSQLStatement($sqlStatement, "Gallery");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deleteGallery (Gallery $gallery): int
    {
        $sqlStatement = "DELETE FROM gallery WHERE id =" . $gallery->getId();
        
        $this->executeSQLStatement($sqlStatement, "Gallery");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function getAllGalleries (): array
    {
        $sqlStatement = "SELECT g.id as g_id, g.title_translation as g_t, t_t.code as t_t_c, t_t.type as t_t_t, t_t.text_de as t_t_de, t_t.text_en as t_t_en, g.description_translation as g_d, t_g.code as t_g_c, t_g.type as t_g_t, t_g.text_de as t_g_de, t_g.text_en as t_g_en, gp.id as gp_id, gp.index_of_photo as gp_i, p.id as p_id, p.upload_date as p_ud, p.filename as p_f, a.id as a_id, a.width as a_wi, a.height as a_h, a.depth as a_d, a.weight as a_we, a.price as a_p, a.signature_date as a_sd, a.signature_name as a_sn, a.title_translation as a_ti, t_ti.code as t_ti_c, t_ti.type as t_ti_t, t_ti.text_de as t_ti_de, t_ti.text_en as t_ti_en, a.explanation_translation as a_et, t_et.code as t_et_c, t_et.type as t_et_t, t_et.text_de as t_et_de, t_et.text_en as t_et_en, a.technique as a_te, t_te.id as t_te_id, t_te.code as t_te_c, t_te.type as t_te_t, t_te.text_de as t_te_de, t_te.text_en as t_te_en, a.material as a_m, t_ma.id as t_ma_id, t_ma.code as t_ma_c, t_ma.type as t_ma_t, t_ma.text_de as t_ma_de, t_ma.text_en as t_ma_en, a.location_translation as a_l, t_lo.code as t_lo_c, t_lo.type as t_lo_t, t_lo.text_de as t_lo_de, t_lo.text_en as t_lo_en, a.availability as a_av, t_av.id as t_av_id, t_av.code as t_av_c, t_av.type as t_av_t, t_av.text_de as t_av_de, t_av.text_en as t_av_en, a.artist as a_ar, ar.first_name as ar_fn, ar.last_name as ar_ln, ar.date_of_birth as ar_dob, ar.e_mail as ar_em, ar.telephone as ar_tel, ar.cellphone as ar_ce, ar.address as ar_a from gallery g JOIN translation t_t on t_t.id = g.title_translation JOIN translation t_g on t_g.id = g.description_translation JOIN gallery_photo gp on gp.gallery = g.id JOIN photo p ON p.id = gp.photo JOIN artwork a ON a.id = p.artwork JOIN translation t_ti ON t_ti.id = a.title_translation JOIN translation t_et on t_et.id = a.explanation_translation JOIN translation t_lo ON t_lo.id = a.location_translation JOIN technique te ON te.id = a.technique JOIN translation t_te ON t_te.id = te.translation JOIN material ma ON ma.id = a.material JOIN translation t_ma ON t_ma.id = ma.translation JOIN availability av ON av.id = a.availability JOIN translation t_av ON t_av.id = av.translation JOIN artist ar on ar.id = a.artist ORDER BY g.id, gp.index_of_photo";
        
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        $galleriesResultArray = [];
        
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $gallery = $this->extractGalleryFromResultSet($resultRow);
            $galleriesResultArray[$i] = $gallery;
            ++ $i;
        }
        
        $resultSet->free();
        
        $consolidatedGallerySet = $this->consolidateGallerySet(
                $galleriesResultArray);
        
        return $consolidatedGallerySet;
    }

    public function getGalleryById (int $galleryId): Gallery
    {
        $sqlStatement = "SELECT g.id as g_id, g.title_translation as g_t, t_t.code as t_t_c, t_t.type as t_t_t, t_t.text_de as t_t_de, t_t.text_en as t_t_en, g.description_translation as g_d, t_g.code as t_g_c, t_g.type as t_g_t, t_g.text_de as t_g_de, t_g.text_en as t_g_en, gp.id as gp_id, gp.index_of_photo as gp_i, p.id as p_id, p.upload_date as p_ud, p.filename as p_f, a.id as a_id, a.width as a_wi, a.height as a_h, a.depth as a_d, a.weight as a_we, a.price as a_p, a.signature_date as a_sd, a.signature_name as a_sn, a.title_translation as a_ti, t_ti.code as t_ti_c, t_ti.type as t_ti_t, t_ti.text_de as t_ti_de, t_ti.text_en as t_ti_en, a.explanation_translation as a_et, t_et.code as t_et_c, t_et.type as t_et_t, t_et.text_de as t_et_de, t_et.text_en as t_et_en, a.technique as a_te, t_te.id as t_te_id, t_te.code as t_te_c, t_te.type as t_te_t, t_te.text_de as t_te_de, t_te.text_en as t_te_en, a.material as a_m, t_ma.id as t_ma_id, t_ma.code as t_ma_c, t_ma.type as t_ma_t, t_ma.text_de as t_ma_de, t_ma.text_en as t_ma_en, a.location_translation as a_l, t_lo.code as t_lo_c, t_lo.type as t_lo_t, t_lo.text_de as t_lo_de, t_lo.text_en as t_lo_en, a.availability as a_av, t_av.id as t_av_id, t_av.code as t_av_c, t_av.type as t_av_t, t_av.text_de as t_av_de, t_av.text_en as t_av_en, a.artist as a_ar, ar.first_name as ar_fn, ar.last_name as ar_ln, ar.date_of_birth as ar_dob, ar.e_mail as ar_em, ar.telephone as ar_tel, ar.cellphone as ar_ce, ar.address as ar_a from gallery g JOIN translation t_t on t_t.id = g.title_translation JOIN translation t_g on t_g.id = g.description_translation JOIN gallery_photo gp on gp.gallery = g.id join photo p ON p.id = gp.photo join artwork a ON a.id = p.artwork JOIN translation t_ti ON t_ti.id = a.title_translation JOIN translation t_et on t_et.id = a.explanation_translation JOIN translation t_lo ON t_lo.id = a.location_translation JOIN technique te ON te.id = a.technique JOIN translation t_te ON t_te.id = te.translation JOIN material ma ON ma.id = a.material JOIN translation t_ma ON t_ma.id = ma.translation JOIN availability av ON av.id = a.availability JOIN translation t_av ON t_av.id = av.translation join artist ar on ar.id = a.artist where g.id=" .
                $galleryId . " ORDER BY gp.index_of_photo";
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        $galleriesResultArray = [];
        
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $gallery = $this->extractGalleryFromResultSet($resultRow);
            $galleriesResultArray[$i] = $gallery;
            ++ $i;
        }
        
        $resultSet->free();
        
        $consolidatedGallerySet = $this->consolidateGallerySet(
                $galleriesResultArray);
        
        if (count($consolidatedGallerySet) == 1) {
            $resultGallery = array_pop($consolidatedGallerySet);
            return $resultGallery;
        } else {
            // there is either no matching gallery or the result is
            // ambiguous
            return $gallery = new Gallery();
        }
    }

    public function insertGalleryPhoto (GalleryPhoto $galleryPhoto): int
    {
        $sqlStatement = "INSERT INTO gallery_photo (photo, index_of_photo, gallery) values ( " .
                $galleryPhoto->getPhoto()->getId() . " , " .
                $galleryPhoto->getIndexOfPhoto() . " , " .
                $galleryPhoto->getGalleryId() . " )";
        
        $this->executeSQLStatement($sqlStatement, "GalleryPhoto");
        
        $generatedGalleryPhotoId = $this->mySQLDatabase->insert_id;
        $galleryPhoto->setId($generatedGalleryPhotoId);
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function updateGalleryPhoto (GalleryPhoto $galleryPhoto): int
    {
        $sqlStatement = "UPDATE gallery_photo SET " . "photo = " .
                $galleryPhoto->getPhoto()->getId() . ", " . "index_of_photo = " .
                $galleryPhoto->getIndexOfPhoto() . ", " . "gallery = " .
                $galleryPhoto->getGalleryId() . " WHERE id =" .
                $galleryPhoto->getId();
        
        $this->executeSQLStatement($sqlStatement, "GalleryPhoto");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deleteGalleryPhoto (GalleryPhoto $galleryPhoto): int
    {
        $sqlStatement = "DELETE FROM gallery_photo WHERE id =" .
                $galleryPhoto->getId();
        
        $this->executeSQLStatement($sqlStatement, "GalleryPhoto");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deleteAllPhotosForGallery (Gallery $gallery): int
    {
        $sqlStatement = "DELETE FROM gallery_photo WHERE gallery =" .
                $gallery->getId();
        
        $this->executeSQLStatement($sqlStatement, "GalleryPhoto");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function getAllGalleryPhotos (): array
    {
        $sqlStatement = "SELECT gp.id as gp_id, gp.index_of_photo as gp_i, gp.gallery as gp_g, p.id as p_id, p.upload_date as p_ud, p.filename as p_f, a.id as a_id, a.width as a_wi, a.height as a_h, a.depth as a_d, a.weight as a_we, a.price as a_p, a.signature_date as a_sd, a.signature_name as a_sn, a.title_translation as a_ti, t_ti.code as t_ti_c, t_ti.type as t_ti_t, t_ti.text_de as t_ti_de, t_ti.text_en as t_ti_en, a.explanation_translation as a_et, t_et.code as t_et_c, t_et.type as t_et_t, t_et.text_de as t_et_de, t_et.text_en as t_et_en, a.technique as a_te, t_te.id as t_te_id, t_te.code as t_te_c, t_te.type as t_te_t, t_te.text_de as t_te_de, t_te.text_en as t_te_en, a.material as a_m, t_ma.id as t_ma_id, t_ma.code as t_ma_c, t_ma.type as t_ma_t, t_ma.text_de as t_ma_de, t_ma.text_en as t_ma_en, a.location_translation as a_l, t_lo.code as t_lo_c, t_lo.type as t_lo_t, t_lo.text_de as t_lo_de, t_lo.text_en as t_lo_en, a.availability as a_av, t_av.id as t_av_id, t_av.code as t_av_c, t_av.type as t_av_t, t_av.text_de as t_av_de, t_av.text_en as t_av_en, a.artist as a_ar, ar.first_name as ar_fn, ar.last_name as ar_ln, ar.date_of_birth as ar_dob, ar.e_mail as ar_em, ar.telephone as ar_tel, ar.cellphone as ar_ce, ar.address as ar_a FROM gallery_photo gp JOIN photo p ON p.id = gp.photo JOIN artwork a ON a.id = p.artwork JOIN translation t_ti ON t_ti.id = a.title_translation JOIN translation t_et on t_et.id = a.explanation_translation JOIN translation t_lo ON t_lo.id = a.location_translation JOIN technique te ON te.id = a.technique JOIN translation t_te ON t_te.id = te.translation JOIN material ma ON ma.id = a.material JOIN translation t_ma ON t_ma.id = ma.translation JOIN availability av ON av.id = a.availability JOIN translation t_av ON t_av.id = av.translation join artist ar on ar.id = a.artist ORDER BY gp.gallery, gp.index_of_photo";
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        $galleryPhotosResultArray = [];
        
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $galleryPhoto = $this->extractGalleryPhotoFromResultSet($resultRow);
            $galleryPhotosResultArray[$i] = $galleryPhoto;
            $i ++;
        }
        
        $resultSet->free();
        
        return $galleryPhotosResultArray;
    }

    public function getGalleryPhotoById (int $galleryPhotoId): GalleryPhoto
    {
        $sqlStatement = "SELECT gp.id as gp_id, gp.index_of_photo as gp_i, gp.gallery as gp_g, p.id as p_id, p.upload_date as p_ud, p.filename as p_f, a.id as a_id, a.width as a_wi, a.height as a_h, a.depth as a_d, a.weight as a_we, a.price as a_p, a.signature_date as a_sd, a.signature_name as a_sn, a.title_translation as a_ti, t_ti.code as t_ti_c, t_ti.type as t_ti_t, t_ti.text_de as t_ti_de, t_ti.text_en as t_ti_en, a.explanation_translation as a_et, t_et.code as t_et_c, t_et.type as t_et_t, t_et.text_de as t_et_de, t_et.text_en as t_et_en, a.technique as a_te, t_te.id as t_te_id, t_te.code as t_te_c, t_te.type as t_te_t, t_te.text_de as t_te_de, t_te.text_en as t_te_en, a.material as a_m, t_ma.id as t_ma_id, t_ma.code as t_ma_c, t_ma.type as t_ma_t, t_ma.text_de as t_ma_de, t_ma.text_en as t_ma_en, a.location_translation as a_l, t_lo.code as t_lo_c, t_lo.type as t_lo_t, t_lo.text_de as t_lo_de, t_lo.text_en as t_lo_en, a.availability as a_av, t_av.id as t_av_id, t_av.code as t_av_c, t_av.type as t_av_t, t_av.text_de as t_av_de, t_av.text_en as t_av_en, a.artist as a_ar, ar.first_name as ar_fn, ar.last_name as ar_ln, ar.date_of_birth as ar_dob, ar.e_mail as ar_em, ar.telephone as ar_tel, ar.cellphone as ar_ce, ar.address as ar_a FROM gallery_photo gp JOIN photo p ON p.id = gp.photo JOIN artwork a ON a.id = p.artwork JOIN translation t_ti ON t_ti.id = a.title_translation JOIN translation t_et on t_et.id = a.explanation_translation JOIN translation t_lo ON t_lo.id = a.location_translation JOIN technique te ON te.id = a.technique JOIN translation t_te ON t_te.id = te.translation JOIN material ma ON ma.id = a.material JOIN translation t_ma ON t_ma.id = ma.translation JOIN availability av ON av.id = a.availability JOIN translation t_av ON t_av.id = av.translation join artist ar on ar.id = a.artist where gp.id=" .
                $galleryPhotoId;
        $resultSet = $this->mySQLDatabase->query($sqlStatement);
        
        if ($resultSet->num_rows == 1) {
            $resultRow = $resultSet->fetch_assoc();
            $galleryPhoto = $this->extractGalleryPhotoFromResultSet($resultRow);
            $resultSet->free();
            
            return $galleryPhoto;
        } else {
            // there is either no matching galleryphoto or the result is
            // ambiguous
            $resultSet->free();
            return $galleryPhoto = new GalleryPhoto();
        }
    }

    private function executeSQLStatement (string $sqlStatement,
            string $errorMessage): void
    {
        if (! $this->mySQLDatabase->query($sqlStatement)) {
            echo "Änderung der " . $errorMessage .
                    " ist wegen eines Datenbankfehlers fehlgeschlagen: (" .
                    $this->mySQLDatabase->errno . ") " .
                    $this->mySQLDatabase->error;
        }
    }

    private function getObjectCountFromDatabase (string $selectSQL): int
    {
        $objectCount = 0;
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        if ($resultSet->num_rows == 1) {
            $resultRow = $resultSet->fetch_assoc();
            if (isset($resultRow["objectcount"])) {
                $objectCount = intval($resultRow["objectcount"]);
            }
            $resultSet->free();
        }
        return $objectCount;
    }

    private function extractTranslationFromResultSet ($resultRow): Translation
    {
        $translation = new Translation();
        
        if (! isset($resultRow["id"])) {
            return $translation;
        }
        
        $translation->setId(intval($resultRow["id"]));
        $translation->setCode(trim($resultRow["code"]));
        $translation->getType()->setTypeCode(intval($resultRow["type"]));
        $translation->setRawTranslation_de(trim($resultRow["text_de"]));
        $translation->setRawTranslation_en(trim($resultRow["text_en"]));
        
        return $translation;
    }

    private function extractArtistFromResultSet ($resultRow): Artist
    {
        $artist = new Artist();
        
        if (! isset($resultRow["id"])) {
            return $artist;
        }
        
        $artist->setId(intval($resultRow["id"]));
        $artist->setFirstname(trim($resultRow["first_name"]));
        $artist->setLastname(trim($resultRow["last_name"]));
        $birthdate = DateTime::createFromFormat('Y-m-d',
                $resultRow["date_of_birth"]);
        $artist->setBirthdate($birthdate);
        $artist->setMail(trim($resultRow["e_mail"]));
        $artist->setTelephone(trim($resultRow["telephone"]));
        $artist->setCellphone(trim($resultRow["cellphone"]));
        $artist->setAddress(trim($resultRow["address"]));
        
        return $artist;
    }

    private function extractAvailabilityFromResultSet ($resultRow): Availability
    {
        $availability = new Availability();
        $translation = $availability->getTranslation();
        
        if (! isset($resultRow["a_id"])) {
            return $availability;
        }
        
        $availability->setId(intval($resultRow["a_id"]));
        $translation->setId(intval($resultRow["t_id"]));
        $translation->setCode(trim($resultRow["code"]));
        
        $translationType = new TranslationType();
        $translationType->setTypeCode(intval($resultRow["type"]));
        $translation->setType($translationType);
        
        $translation->setTranslation_de(trim($resultRow["text_de"]));
        $translation->setTranslation_en(trim($resultRow["text_en"]));
        
        return $availability;
    }

    private function extractMaterialFromResultSet ($resultRow): Material
    {
        $material = new Material();
        $translation = $material->getTranslation();
        
        if (! isset($resultRow["m_id"])) {
            return $material;
        }
        
        $material->setId(intval($resultRow["m_id"]));
        $translation->setId(intval($resultRow["t_id"]));
        $translation->setCode(trim($resultRow["code"]));
        
        $translationType = new TranslationType();
        $translationType->setTypeCode(intval($resultRow["type"]));
        $translation->setType($translationType);
        
        $translation->setTranslation_de(trim($resultRow["text_de"]));
        $translation->setTranslation_en(trim($resultRow["text_en"]));
        
        return $material;
    }

    private function extractTechniqueFromResultSet ($resultRow): Technique
    {
        $technique = new Technique();
        $translation = $technique->getTranslation();
        
        if (! isset($resultRow["te_id"])) {
            return $technique;
        }
        
        $technique->setId(intval($resultRow["te_id"]));
        $translation->setId(intval($resultRow["t_id"]));
        $translation->setCode(trim($resultRow["code"]));
        
        $translationType = new TranslationType();
        $translationType->setTypeCode(intval($resultRow["type"]));
        $translation->setType($translationType);
        
        $translation->setTranslation_de(trim($resultRow["text_de"]));
        $translation->setTranslation_en(trim($resultRow["text_en"]));
        
        return $technique;
    }

    private function extractExhibitionFromResultSet ($resultRow): Exhibition
    {
        $exhibition = new Exhibition();
        $title = $exhibition->getTitle();
        $description = $exhibition->getDescription();
        
        if (! isset($resultRow["e_id"])) {
            return $exhibition;
        }
        
        $exhibition->setId(intval($resultRow["e_id"]));
        $exhibition->setStart(
                DateTime::createFromFormat('Y-m-d', trim($resultRow["e_s"])));
        $exhibition->setEnd(
                DateTime::createFromFormat('Y-m-d', trim($resultRow["e_e"])));
        $exhibition->setAddress(trim($resultRow["e_a"]));
        $exhibition->setOpeningHours(trim($resultRow["e_o"]));
        
        $title->setId(intval($resultRow["t_id"]));
        $title->setCode(trim($resultRow["code"]));
        $title->getType()->setTypeCode(intval($resultRow["type"]));
        $title->setTranslation_de(trim($resultRow["text_de"]));
        $title->setTranslation_en(trim($resultRow["text_en"]));
        
        $description->setId(intval($resultRow["d_id"]));
        $description->setCode(trim($resultRow["d_code"]));
        $description->getType()->setTypeCode(intval($resultRow["d_type"]));
        $description->setTranslation_de(trim($resultRow["d_text_de"]));
        $description->setTranslation_en(trim($resultRow["d_text_en"]));
        
        return $exhibition;
    }

    private function extractReportFromResultSet ($resultRow): Report
    {
        $report = new Report();
        $titleTranslation = $report->getTitleTranslation();
        $textTranslation = $report->getTextTranslation();
        $exhibition = $report->getExhibition();
        
        if (! isset($resultRow["r_id"])) {
            return $report;
        }
        
        $report->setId(intval($resultRow["r_id"]));
        $report->setPublicationDate(
                DateTime::createFromFormat('Y-m-d', trim($resultRow["r_pd"])));
        $report->setObsolescenceDate(
                DateTime::createFromFormat('Y-m-d', trim($resultRow["r_od"])));
        $report->setAuthor(trim($resultRow["r_a"]));
        
        $titleTranslation->setId(intval($resultRow["ti_id"]));
        $titleTranslation->setCode(trim($resultRow["ti_code"]));
        
        $translationType = new TranslationType();
        $translationType->setTypeCode(intval($resultRow["ti_type"]));
        $titleTranslation->setType($translationType);
        
        $titleTranslation->setTranslation_de(trim($resultRow["ti_text_de"]));
        $titleTranslation->setTranslation_en(trim($resultRow["ti_text_en"]));
        
        $textTranslation->setId(intval($resultRow["te_id"]));
        $textTranslation->setCode(trim($resultRow["te_code"]));
        
        $translationType = new TranslationType();
        $translationType->setTypeCode(intval($resultRow["te_type"]));
        $textTranslation->setType($translationType);
        
        $textTranslation->setTranslation_de(trim($resultRow["te_text_de"]));
        $textTranslation->setTranslation_en(trim($resultRow["te_text_en"]));
        
        if (isset($resultRow["ex_id"])) {
            
            $exhibition->setId(intval($resultRow["ex_id"]));
            $exhibition->setStart(
                    DateTime::createFromFormat('Y-m-d', trim(
                            $resultRow["ex_s"])));
            $exhibition->setEnd(
                    DateTime::createFromFormat('Y-m-d', trim(
                            $resultRow["ex_e"])));
            $exhibition->setAddress(trim($resultRow["ex_a"]));
            $exhibition->setOpeningHours(trim($resultRow["ex_o"]));
            
            $exhibition->getTitle()->setId(intval($resultRow["t_ex_id"]));
            $exhibition->getTitle()->setCode(trim($resultRow["t_ex_code"]));
            $exhibition->getTitle()
                ->getType()
                ->setTypeCode(intval($resultRow["t_ex_type"]));
            $exhibition->getTitle()->setRawTranslation_de(
                    trim($resultRow["t_ex_text_de"]));
            $exhibition->getTitle()->setRawTranslation_en(
                    trim($resultRow["t_ex_text_en"]));
            
            $exhibition->getDescription()->setId(intval($resultRow["d_ex_id"]));
            $exhibition->getDescription()->setCode(
                    trim($resultRow["d_ex_code"]));
            $exhibition->getDescription()
                ->getType()
                ->setTypeCode(intval($resultRow["d_ex_type"]));
            $exhibition->getDescription()->setRawTranslation_de(
                    trim($resultRow["d_ex_text_de"]));
            $exhibition->getDescription()->setRawTranslation_en(
                    trim($resultRow["d_ex_text_en"]));
        }
        
        return $report;
    }

    private function extractArtworkFromResultSet ($resultRow): Artwork
    {
        $artwork = new Artwork();
        
        if (! isset($resultRow["a_id"])) {
            return $artwork;
        }
        
        $artwork->setId(intval($resultRow["a_id"]));
        $artwork->setWidth(intval($resultRow["a_wi"]));
        $artwork->setHeight(intval($resultRow["a_h"]));
        $artwork->setDepth(intval($resultRow["a_d"]));
        $artwork->setWeight(intval($resultRow["a_we"]));
        $artwork->setPrice(intval($resultRow["a_p"]));
        $artwork->setSignatureDate(
                DateTime::createFromFormat('Y-m-d', trim($resultRow["a_sd"])));
        $artwork->setSignatureName(trim($resultRow["a_sn"]));
        
        $artwork->getTitle()->setId(intval($resultRow["a_ti"]));
        $artwork->getTitle()->setCode(trim($resultRow["t_ti_c"]));
        $artwork->getTitle()
            ->getType()
            ->setTypeCode(intval($resultRow["t_ti_t"]));
        $artwork->getTitle()->setTranslation_de(trim($resultRow["t_ti_de"]));
        $artwork->getTitle()->setTranslation_en(trim($resultRow["t_ti_en"]));
        
        $artwork->getExplanation()->setId(intval($resultRow["a_et"]));
        $artwork->getExplanation()->setCode(trim($resultRow["t_et_c"]));
        $artwork->getExplanation()
            ->getType()
            ->setTypeCode(intval($resultRow["t_et_t"]));
        $artwork->getExplanation()->setTranslation_de(
                trim($resultRow["t_et_de"]));
        $artwork->getExplanation()->setTranslation_en(
                trim($resultRow["t_et_en"]));
        
        $artwork->getLocation()->setId(intval($resultRow["a_l"]));
        $artwork->getLocation()->setCode(trim($resultRow["t_lo_c"]));
        $artwork->getLocation()
            ->getType()
            ->setTypeCode(intval($resultRow["t_lo_t"]));
        $artwork->getLocation()->setTranslation_de(trim($resultRow["t_lo_de"]));
        $artwork->getLocation()->setTranslation_en(trim($resultRow["t_lo_en"]));
        
        $artwork->getTechnique()->setId(intval($resultRow["a_te"]));
        $artwork->getTechnique()
            ->getTranslation()
            ->setId(intval($resultRow["t_te_id"]));
        $artwork->getTechnique()
            ->getTranslation()
            ->setCode(trim($resultRow["t_te_c"]));
        $artwork->getTechnique()
            ->getTranslation()
            ->getType()
            ->setTypeCode(intval($resultRow["t_te_t"]));
        $artwork->getTechnique()
            ->getTranslation()
            ->setTranslation_de(trim($resultRow["t_te_de"]));
        $artwork->getTechnique()
            ->getTranslation()
            ->setTranslation_en(trim($resultRow["t_te_en"]));
        
        $artwork->getMaterial()->setId(intval($resultRow["a_m"]));
        $artwork->getMaterial()
            ->getTranslation()
            ->setId(intval($resultRow["t_ma_id"]));
        $artwork->getMaterial()
            ->getTranslation()
            ->setCode(trim($resultRow["t_ma_c"]));
        $artwork->getMaterial()
            ->getTranslation()
            ->getType()
            ->setTypeCode(intval($resultRow["t_ma_t"]));
        $artwork->getMaterial()
            ->getTranslation()
            ->setTranslation_de(trim($resultRow["t_ma_de"]));
        $artwork->getMaterial()
            ->getTranslation()
            ->setTranslation_en(trim($resultRow["t_ma_en"]));
        
        $artwork->getAvailability()->setId(intval($resultRow["a_av"]));
        $artwork->getAvailability()
            ->getTranslation()
            ->setId(intval($resultRow["t_av_id"]));
        $artwork->getAvailability()
            ->getTranslation()
            ->setCode(trim($resultRow["t_av_c"]));
        $artwork->getAvailability()
            ->getTranslation()
            ->getType()
            ->setTypeCode(intval($resultRow["t_av_t"]));
        $artwork->getAvailability()
            ->getTranslation()
            ->setTranslation_de(trim($resultRow["t_av_de"]));
        $artwork->getAvailability()
            ->getTranslation()
            ->setTranslation_en(trim($resultRow["t_av_en"]));
        
        $artwork->getArtist()->setId(intval($resultRow["a_ar"]));
        $artwork->getArtist()->setFirstname(trim($resultRow["ar_fn"]));
        $artwork->getArtist()->setLastname(trim($resultRow["ar_ln"]));
        $artwork->getArtist()->setBirthdate(
                DateTime::createFromFormat('Y-m-d', trim($resultRow["ar_dob"])));
        $artwork->getArtist()->setMail(trim($resultRow["ar_em"]));
        $artwork->getArtist()->setTelephone(trim($resultRow["ar_tel"]));
        $artwork->getArtist()->setCellphone(trim($resultRow["ar_ce"]));
        $artwork->getArtist()->setAddress(trim($resultRow["ar_a"]));
        
        return $artwork;
    }

    private function extractPhotoFromResultSet ($resultRow): Photo
    {
        $photo = new Photo();
        $artwork = $photo->getArtwork();
        
        if (! isset($resultRow["p_id"])) {
            return $photo;
        }
        
        $photo->setId(intval($resultRow["p_id"]));
        $photo->setUploadDate(
                DateTime::createFromFormat('Y-m-d', trim($resultRow["p_ud"])));
        $photo->setFilename(trim($resultRow["p_f"]));
        
        $artwork->setId(intval($resultRow["a_id"]));
        $artwork->setWidth(intval($resultRow["a_wi"]));
        $artwork->setHeight(intval($resultRow["a_h"]));
        $artwork->setDepth(intval($resultRow["a_d"]));
        $artwork->setWeight(intval($resultRow["a_we"]));
        $artwork->setPrice(intval($resultRow["a_p"]));
        $artwork->setSignatureDate(
                DateTime::createFromFormat('Y-m-d', trim($resultRow["a_sd"])));
        $artwork->setSignatureName(trim($resultRow["a_sn"]));
        
        $artwork->getTitle()->setId(intval($resultRow["a_ti"]));
        $artwork->getTitle()->setCode(trim($resultRow["t_ti_c"]));
        $artwork->getTitle()
            ->getType()
            ->setTypeCode(intval($resultRow["t_ti_t"]));
        $artwork->getTitle()->setTranslation_de(trim($resultRow["t_ti_de"]));
        $artwork->getTitle()->setTranslation_en(trim($resultRow["t_ti_en"]));
        
        $artwork->getExplanation()->setId(intval($resultRow["a_et"]));
        $artwork->getExplanation()->setCode(trim($resultRow["t_et_c"]));
        $artwork->getExplanation()
            ->getType()
            ->setTypeCode(intval($resultRow["t_et_t"]));
        $artwork->getExplanation()->setTranslation_de(
                trim($resultRow["t_et_de"]));
        $artwork->getExplanation()->setTranslation_en(
                trim($resultRow["t_et_en"]));
        
        $artwork->getLocation()->setId(intval($resultRow["a_l"]));
        $artwork->getLocation()->setCode(trim($resultRow["t_lo_c"]));
        $artwork->getLocation()
            ->getType()
            ->setTypeCode(intval($resultRow["t_lo_t"]));
        $artwork->getLocation()->setTranslation_de(trim($resultRow["t_lo_de"]));
        $artwork->getLocation()->setTranslation_en(trim($resultRow["t_lo_en"]));
        
        $artwork->getTechnique()->setId(intval($resultRow["a_te"]));
        $artwork->getTechnique()
            ->getTranslation()
            ->setId(intval($resultRow["t_te_id"]));
        $artwork->getTechnique()
            ->getTranslation()
            ->setCode(trim($resultRow["t_te_c"]));
        $artwork->getTechnique()
            ->getTranslation()
            ->getType()
            ->setTypeCode(intval($resultRow["t_te_t"]));
        $artwork->getTechnique()
            ->getTranslation()
            ->setTranslation_de(trim($resultRow["t_te_de"]));
        $artwork->getTechnique()
            ->getTranslation()
            ->setTranslation_en(trim($resultRow["t_te_en"]));
        
        $artwork->getMaterial()->setId(intval($resultRow["a_m"]));
        $artwork->getMaterial()
            ->getTranslation()
            ->setId(intval($resultRow["t_ma_id"]));
        $artwork->getMaterial()
            ->getTranslation()
            ->setCode(trim($resultRow["t_ma_c"]));
        $artwork->getMaterial()
            ->getTranslation()
            ->getType()
            ->setTypeCode(intval($resultRow["t_ma_t"]));
        $artwork->getMaterial()
            ->getTranslation()
            ->setTranslation_de(trim($resultRow["t_ma_de"]));
        $artwork->getMaterial()
            ->getTranslation()
            ->setTranslation_en(trim($resultRow["t_ma_en"]));
        
        $artwork->getAvailability()->setId(intval($resultRow["a_av"]));
        $artwork->getAvailability()
            ->getTranslation()
            ->setId(intval($resultRow["t_av_id"]));
        $artwork->getAvailability()
            ->getTranslation()
            ->setCode(trim($resultRow["t_av_c"]));
        $artwork->getAvailability()
            ->getTranslation()
            ->getType()
            ->setTypeCode(intval($resultRow["t_av_t"]));
        $artwork->getAvailability()
            ->getTranslation()
            ->setTranslation_de(trim($resultRow["t_av_de"]));
        $artwork->getAvailability()
            ->getTranslation()
            ->setTranslation_en(trim($resultRow["t_av_en"]));
        
        $artwork->getArtist()->setId(intval($resultRow["a_ar"]));
        $artwork->getArtist()->setFirstname(trim($resultRow["ar_fn"]));
        $artwork->getArtist()->setLastname(trim($resultRow["ar_ln"]));
        $artwork->getArtist()->setBirthdate(
                DateTime::createFromFormat('Y-m-d', trim($resultRow["ar_dob"])));
        $artwork->getArtist()->setMail(trim($resultRow["ar_em"]));
        $artwork->getArtist()->setTelephone(trim($resultRow["ar_tel"]));
        $artwork->getArtist()->setCellphone(trim($resultRow["ar_ce"]));
        $artwork->getArtist()->setAddress(trim($resultRow["ar_a"]));
        
        return $photo;
    }

    private function extractOtherPhotoFromResultSet ($resultRow): Photo
    {
        $photo = new Photo();
        $artwork = $photo->getArtwork();
        
        if (! isset($resultRow["p_id"])) {
            return $photo;
        }
        
        $photo->setId(intval($resultRow["p_id"]));
        $photo->setUploadDate(
                DateTime::createFromFormat('Y-m-d', trim($resultRow["p_ud"])));
        $photo->setFilename(trim($resultRow["p_f"]));
        
        return $photo;
    }

    private function extractGalleryFromResultSet ($resultRow): Gallery
    {
        $gallery = new Gallery();
        
        if (! isset($resultRow["g_id"])) {
            return $gallery;
        }
        
        $gallery->setId(intval($resultRow["g_id"]));
        
        $gallery->getTitle()->setId(intval($resultRow["g_t"]));
        $gallery->getTitle()->setCode(trim($resultRow["t_t_c"]));
        $gallery->getTitle()
            ->getType()
            ->setTypeCode(intval($resultRow["t_t_t"]));
        $gallery->getTitle()->setTranslation_de(trim($resultRow["t_t_de"]));
        $gallery->getTitle()->setTranslation_en(trim($resultRow["t_t_en"]));
        
        $gallery->getDescription()->setId(intval($resultRow["g_d"]));
        $gallery->getDescription()->setCode(trim($resultRow["t_g_c"]));
        $gallery->getDescription()
            ->getType()
            ->setTypeCode(intval($resultRow["t_g_t"]));
        $gallery->getDescription()->setTranslation_de(
                trim($resultRow["t_g_de"]));
        $gallery->getDescription()->setTranslation_en(
                trim($resultRow["t_g_en"]));
        
        if (! isset($resultRow["gp_id"])) {
            return $gallery;
        }
        
        $galleryPhoto = new GalleryPhoto();
        $galleryPhoto->setId(intval($resultRow["gp_id"]));
        $galleryPhoto->setIndexOfPhoto(intval($resultRow["gp_i"]));
        $galleryPhoto->setGalleryId($gallery->getId());
        
        $gallery->addGalleryPhoto($galleryPhoto,
                $galleryPhoto->getIndexOfPhoto());
        
        $photo = $galleryPhoto->getPhoto();
        $artwork = $photo->getArtwork();
        
        $photo->setId(intval($resultRow["p_id"]));
        $photo->setUploadDate(
                DateTime::createFromFormat('Y-m-d', trim($resultRow["p_ud"])));
        $photo->setFilename(trim($resultRow["p_f"]));
        
        $artwork->setId(intval($resultRow["a_id"]));
        $artwork->setWidth(intval($resultRow["a_wi"]));
        $artwork->setHeight(intval($resultRow["a_h"]));
        $artwork->setDepth(intval($resultRow["a_d"]));
        $artwork->setWeight(intval($resultRow["a_we"]));
        $artwork->setPrice(intval($resultRow["a_p"]));
        $artwork->setSignatureDate(
                DateTime::createFromFormat('Y-m-d', trim($resultRow["a_sd"])));
        $artwork->setSignatureName(trim($resultRow["a_sn"]));
        
        $artwork->getTitle()->setId(intval($resultRow["a_ti"]));
        $artwork->getTitle()->setCode(trim($resultRow["t_ti_c"]));
        $artwork->getTitle()
            ->getType()
            ->setTypeCode(intval($resultRow["t_ti_t"]));
        $artwork->getTitle()->setTranslation_de(trim($resultRow["t_ti_de"]));
        $artwork->getTitle()->setTranslation_en(trim($resultRow["t_ti_en"]));
        
        $artwork->getExplanation()->setId(intval($resultRow["a_et"]));
        $artwork->getExplanation()->setCode(trim($resultRow["t_et_c"]));
        $artwork->getExplanation()
            ->getType()
            ->setTypeCode(intval($resultRow["t_et_t"]));
        $artwork->getExplanation()->setTranslation_de(
                trim($resultRow["t_et_de"]));
        $artwork->getExplanation()->setTranslation_en(
                trim($resultRow["t_et_en"]));
        
        $artwork->getLocation()->setId(intval($resultRow["a_l"]));
        $artwork->getLocation()->setCode(trim($resultRow["t_lo_c"]));
        $artwork->getLocation()
            ->getType()
            ->setTypeCode(intval($resultRow["t_lo_t"]));
        $artwork->getLocation()->setTranslation_de(trim($resultRow["t_lo_de"]));
        $artwork->getLocation()->setTranslation_en(trim($resultRow["t_lo_en"]));
        
        $artwork->getTechnique()->setId(intval($resultRow["a_te"]));
        $artwork->getTechnique()
            ->getTranslation()
            ->setId(intval($resultRow["t_te_id"]));
        $artwork->getTechnique()
            ->getTranslation()
            ->setCode(trim($resultRow["t_te_c"]));
        $artwork->getTechnique()
            ->getTranslation()
            ->getType()
            ->setTypeCode(intval($resultRow["t_te_t"]));
        $artwork->getTechnique()
            ->getTranslation()
            ->setTranslation_de(trim($resultRow["t_te_de"]));
        $artwork->getTechnique()
            ->getTranslation()
            ->setTranslation_en(trim($resultRow["t_te_en"]));
        
        $artwork->getMaterial()->setId(intval($resultRow["a_m"]));
        $artwork->getMaterial()
            ->getTranslation()
            ->setId(intval($resultRow["t_ma_id"]));
        $artwork->getMaterial()
            ->getTranslation()
            ->setCode(trim($resultRow["t_ma_c"]));
        $artwork->getMaterial()
            ->getTranslation()
            ->getType()
            ->setTypeCode(intval($resultRow["t_ma_t"]));
        $artwork->getMaterial()
            ->getTranslation()
            ->setTranslation_de(trim($resultRow["t_ma_de"]));
        $artwork->getMaterial()
            ->getTranslation()
            ->setTranslation_en(trim($resultRow["t_ma_en"]));
        
        $artwork->getAvailability()->setId(intval($resultRow["a_av"]));
        $artwork->getAvailability()
            ->getTranslation()
            ->setId(intval($resultRow["t_av_id"]));
        $artwork->getAvailability()
            ->getTranslation()
            ->setCode(trim($resultRow["t_av_c"]));
        $artwork->getAvailability()
            ->getTranslation()
            ->getType()
            ->setTypeCode(intval($resultRow["t_av_t"]));
        $artwork->getAvailability()
            ->getTranslation()
            ->setTranslation_de(trim($resultRow["t_av_de"]));
        $artwork->getAvailability()
            ->getTranslation()
            ->setTranslation_en(trim($resultRow["t_av_en"]));
        
        $artwork->getArtist()->setId(intval($resultRow["a_ar"]));
        $artwork->getArtist()->setFirstname(trim($resultRow["ar_fn"]));
        $artwork->getArtist()->setLastname(trim($resultRow["ar_ln"]));
        $artwork->getArtist()->setBirthdate(
                DateTime::createFromFormat('Y-m-d', trim($resultRow["ar_dob"])));
        $artwork->getArtist()->setMail(trim($resultRow["ar_em"]));
        $artwork->getArtist()->setTelephone(trim($resultRow["ar_tel"]));
        $artwork->getArtist()->setCellphone(trim($resultRow["ar_ce"]));
        $artwork->getArtist()->setAddress(trim($resultRow["ar_a"]));
        
        return $gallery;
    }

    private function extractGalleryPhotoFromResultSet ($resultRow): GalleryPhoto
    {
        $galleryPhoto = new GalleryPhoto();
        
        if (! isset($resultRow["gp_id"])) {
            return $galleryPhoto;
        }
        
        $galleryPhoto->setGalleryId(intval($resultRow["gp_g"]));
        $galleryPhoto->setId(intval($resultRow["gp_id"]));
        $galleryPhoto->setIndexOfPhoto(intval($resultRow["gp_i"]));
        
        $photo = $galleryPhoto->getPhoto();
        $artwork = $photo->getArtwork();
        
        $photo->setId(intval($resultRow["p_id"]));
        $photo->setUploadDate(
                DateTime::createFromFormat('Y-m-d', trim($resultRow["p_ud"])));
        $photo->setFilename(trim($resultRow["p_f"]));
        
        $artwork->setId(intval($resultRow["a_id"]));
        $artwork->setWidth(intval($resultRow["a_wi"]));
        $artwork->setHeight(intval($resultRow["a_h"]));
        $artwork->setDepth(intval($resultRow["a_d"]));
        $artwork->setWeight(intval($resultRow["a_we"]));
        $artwork->setPrice(intval($resultRow["a_p"]));
        $artwork->setSignatureDate(
                DateTime::createFromFormat('Y-m-d', trim($resultRow["a_sd"])));
        $artwork->setSignatureName(trim($resultRow["a_sn"]));
        
        $artwork->getTitle()->setId(intval($resultRow["a_ti"]));
        $artwork->getTitle()->setCode(trim($resultRow["t_ti_c"]));
        $artwork->getTitle()
            ->getType()
            ->setTypeCode(intval($resultRow["t_ti_t"]));
        $artwork->getTitle()->setTranslation_de(trim($resultRow["t_ti_de"]));
        $artwork->getTitle()->setTranslation_en(trim($resultRow["t_ti_en"]));
        
        $artwork->getExplanation()->setId(intval($resultRow["a_et"]));
        $artwork->getExplanation()->setCode(trim($resultRow["t_et_c"]));
        $artwork->getExplanation()
            ->getType()
            ->setTypeCode(intval($resultRow["t_et_t"]));
        $artwork->getExplanation()->setTranslation_de(
                trim($resultRow["t_et_de"]));
        $artwork->getExplanation()->setTranslation_en(
                trim($resultRow["t_et_en"]));
        
        $artwork->getLocation()->setId(intval($resultRow["a_l"]));
        $artwork->getLocation()->setCode(trim($resultRow["t_lo_c"]));
        $artwork->getLocation()
            ->getType()
            ->setTypeCode(intval($resultRow["t_lo_t"]));
        $artwork->getLocation()->setTranslation_de(trim($resultRow["t_lo_de"]));
        $artwork->getLocation()->setTranslation_en(trim($resultRow["t_lo_en"]));
        
        $artwork->getTechnique()->setId(intval($resultRow["a_te"]));
        $artwork->getTechnique()
            ->getTranslation()
            ->setId(intval($resultRow["t_te_id"]));
        $artwork->getTechnique()
            ->getTranslation()
            ->setCode(trim($resultRow["t_te_c"]));
        $artwork->getTechnique()
            ->getTranslation()
            ->getType()
            ->setTypeCode(intval($resultRow["t_te_t"]));
        $artwork->getTechnique()
            ->getTranslation()
            ->setTranslation_de(trim($resultRow["t_te_de"]));
        $artwork->getTechnique()
            ->getTranslation()
            ->setTranslation_en(trim($resultRow["t_te_en"]));
        
        $artwork->getMaterial()->setId(intval($resultRow["a_m"]));
        $artwork->getMaterial()
            ->getTranslation()
            ->setId(intval($resultRow["t_ma_id"]));
        $artwork->getMaterial()
            ->getTranslation()
            ->setCode(trim($resultRow["t_ma_c"]));
        $artwork->getMaterial()
            ->getTranslation()
            ->getType()
            ->setTypeCode(intval($resultRow["t_ma_t"]));
        $artwork->getMaterial()
            ->getTranslation()
            ->setTranslation_de(trim($resultRow["t_ma_de"]));
        $artwork->getMaterial()
            ->getTranslation()
            ->setTranslation_en(trim($resultRow["t_ma_en"]));
        
        $artwork->getAvailability()->setId(intval($resultRow["a_av"]));
        $artwork->getAvailability()
            ->getTranslation()
            ->setId(intval($resultRow["t_av_id"]));
        $artwork->getAvailability()
            ->getTranslation()
            ->setCode(trim($resultRow["t_av_c"]));
        $artwork->getAvailability()
            ->getTranslation()
            ->getType()
            ->setTypeCode(intval($resultRow["t_av_t"]));
        $artwork->getAvailability()
            ->getTranslation()
            ->setTranslation_de(trim($resultRow["t_av_de"]));
        $artwork->getAvailability()
            ->getTranslation()
            ->setTranslation_en(trim($resultRow["t_av_en"]));
        
        $artwork->getArtist()->setId(intval($resultRow["a_ar"]));
        $artwork->getArtist()->setFirstname(trim($resultRow["ar_fn"]));
        $artwork->getArtist()->setLastname(trim($resultRow["ar_ln"]));
        $artwork->getArtist()->setBirthdate(
                DateTime::createFromFormat('Y-m-d', trim($resultRow["ar_dob"])));
        $artwork->getArtist()->setMail(trim($resultRow["ar_em"]));
        $artwork->getArtist()->setTelephone(trim($resultRow["ar_tel"]));
        $artwork->getArtist()->setCellphone(trim($resultRow["ar_ce"]));
        $artwork->getArtist()->setAddress(trim($resultRow["ar_a"]));
        
        return $galleryPhoto;
    }

    private function consolidateGallerySet (array $gallerySet): array
    {
        $consolidatedGallerySet = [];
        foreach ($gallerySet as $newGallery) {
            if (array_key_exists($newGallery->getId(), $consolidatedGallerySet)) {
                $existingGallery = $consolidatedGallerySet[$newGallery->getId()];
                foreach ($newGallery->getGalleryPhotos() as $galleryPhoto) {
                    $existingGallery->addGalleryPhoto($galleryPhoto);
                }
            } else {
                $consolidatedGallerySet[$newGallery->getId()] = $newGallery;
            }
        }
        
        return $consolidatedGallerySet;
    }
}

?>