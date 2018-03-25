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
require_once ("./model/artist.php");
require_once ("./model/availability.php");
require_once ("./model/report.php");
require_once ("./model/technique.php");
require_once ("./model/material.php");
require_once ("./model/artwork.php");
require_once ("./model/photo.php");
require_once ("./model/exhibition.php");

class Formatter
{

    public function getSubmitResetControl (): string
    {
        $submitSelectControl = '<div class="submit-reset"><input name="absenden" type="submit" value="Speichern"> <input name="reset" type="reset" value="Zurücksetzen"></div>';
        
        return $submitSelectControl;
    }

    public function getSubmitResetDeleteControl (string $deleteURL): string
    {
        $submitSelectDeleteControl = '<div class="submit-reset"><input name="absenden" type="submit" value="Speichern"> <input name="reset" type="reset" value="Zurücksetzen"> ' .
                 "$deleteURL" . '</div>';
        
        return $submitSelectDeleteControl;
    }

    public function getDataTableHeader (): string
    {
        $dataTableHeader = '<tr><th>Attribut</th><th>Wert</th></tr>';
        
        return $dataTableHeader;
    }

    public function getPhotoGui (Photo $photo, string $caption): string
    {
        $pathToExistingFile = "./image/" . $photo->getFilename();
        $photoGui = '<figure class="thumbnail"><img alt="Foto File Name ' .
                 $photo->getFilename() . '"
                src="' .
                 $pathToExistingFile . '"?hash="' .
                 filemtime($pathToExistingFile) . '">
                <figcaption>' . $caption .
                 '</figcaption></figure>';
        
        return $photoGui;
    }

    public function getFullscalePhotoGui (Photo $photo, string $caption): string
    {
        $pathToExistingFile = "./image/" . $photo->getFilename();
        $fullscalePhotoGui = '<figure class="fullscalephoto"><img alt="Foto File Name ' .
                 $photo->getFilename() . '"
                src="' .
                 $pathToExistingFile . '"?hash="' .
                 filemtime($pathToExistingFile) . '">
                <figcaption>' . $caption .
                 '</figcaption></figure>';
        
        return $fullscalePhotoGui;
    }

    public function getArtworkGui (Artwork $artwork, TranslationController $tc): string
    {
        $artwork->setLanguage($tc->getLanguage());
        $header = '<table class="artwork-gui">';
        
        $body = '<tr><td>' . $tc->getText("aw-title") . '</td><td>' .
                 $artwork->getTitle()->getTextForLanguage() . '</td></tr>';
        $body .= '<tr><td>' . $tc->getText("aw-artist") . '</td><td>' .
                 $artwork->getArtist()->getFirstname() . " " .
                 $artwork->getArtist()->getLastname() . '</td></tr>';
        $body .= '<tr><td>' . $tc->getText("aw-signature") . '</td><td>' .
                 $artwork->getSignatureName() . '</td></tr>';
        $body .= '<tr><td>' . $tc->getText("aw-date") . '</td><td>' .
                 $artwork->getSignatureDate()->format($tc->getDateFormat()) .
                 '</td></tr>';
        $body .= '<tr><td>' . $tc->getText("aw-mat") . '</td><td>' . $artwork->getMaterial()
            ->getTranslation()
            ->getTextForLanguage() . '</td></tr>';
        $body .= '<tr><td>' . $tc->getText("aw-tech") . '</td><td>' . $artwork->getTechnique()
            ->getTranslation()
            ->getTextForLanguage() . '</td></tr>';
        $body .= '<tr><td>' . $tc->getText("aw-avail") . '</td><td>' . $artwork->getAvailability()
            ->getTranslation()
            ->getTextForLanguage() . '</td></tr>';
        $body .= '<tr><td>' . $tc->getText("aw-loc") . '</td><td>' .
                 $artwork->getLocation()->getTextForLanguage() . '</td></tr>';
        $body .= '<tr><td>' . $tc->getText("aw-width") . ' (in cm) </td><td>' .
                 $artwork->getWidthInCm() . '</td></tr>';
        $body .= '<tr><td>' . $tc->getText("aw-height") . ' (in cm) </td><td>' .
                 $artwork->getHeightInCm() . '</td></tr>';
        $body .= '<tr><td>' . $tc->getText("aw-depth") . ' (in cm) </td><td>' .
                 $artwork->getDepthInCm() . '</td></tr>';
        $body .= '<tr><td>' . $tc->getText("aw-price") . ' (in Euro) </td><td>' .
                 $tc->getPriceDisplayString($artwork) . '</td></tr>';
        
        $footer = '</table>';
        
        $artworkGui = $header . $body . $footer;
        
        return $artworkGui;
    }

    public function getAllTranslationsAsTable (array $allTranslations,
            $targetUrl = "translation_update.php?id"): string
    {
        $tableHeader = "<table><tr><th>Id</th><th>Typ</th><th>Kennung</th><th>Text Deutsch</th><th>Text Englisch</th></tr>";
        
        $tableBody = '';
        
        foreach ($allTranslations as $translation) {
            
            $newTableRow = "<tr><td>" . "<a href='" . "$targetUrl" . "=" .
                     $translation->getId() . "'>" . $translation->getId() .
                     "</a></td><td>" . $translation->getType() . "</td><td>" .
                     $translation->getCode() . "</td><td>" .
                     $translation->getDecodedTranslation_de() . "</td><td>" .
                     $translation->getDecodedTranslation_en() . "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getAllArtistsAsTable (array $allArtists,
            $targetUrl = "artist_update.php?id"): string
    {
        $tableHeader = "<table><tr><th>Id</th><th>Nachname</th><th>Vorname</th><th>Geburtsdatum</th><th>E-Mail</th><th>Telefonnummer</th><th>Mobilnummer</th><th>Adresse</th></tr>";
        
        $tableBody = '';
        
        foreach ($allArtists as $artist) {
            
            $birthdate = $artist->getBirthdate()->format('d.m.Y');
            $newTableRow = "<tr><td>" . "<a href='" . "$targetUrl" . "=" .
                     $artist->getId() . "'>" . $artist->getId() . "</a></td><td>" .
                     $artist->getLastname() . "</td><td>" .
                     $artist->getFirstname() . "</td><td>" . "$birthdate" .
                     "</td><td>" . $artist->getMail() . "</td><td>" .
                     $artist->getTelephone() . "</td><td>" .
                     $artist->getCellphone() . "</td><td>" .
                     $artist->getAddress() . "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getAllArtworksAsTable (array $allArtworks,
            $targetUrl = "artwork_update.php?id"): string
    {
        $tableHeader = "<table><tr><th>Id</th><th>Titel</th><th>Künstler</th><th>Technik</th><th>Material</th><th>Verfügbarkeit</th><th>Signatur</th><th>Signaturdatum</th><th>Lagerort</th><th>Breite</th><th>Höhe (mm)</th><th>Tiefe (mm)</th><th>Gewicht (g)</th><th>Preis (€)</th></tr>";
        
        $tableBody = '';
        
        foreach ($allArtworks as $artwork) {
            
            $signaturedate = $artwork->getSignatureDate()->format('d.m.Y');
            $newTableRow = "<tr><td>" . "<a href='" . "$targetUrl" . "=" .
                     $artwork->getId() . "'>" . $artwork->getId() .
                     "</a></td><td>" .
                     $artwork->getTitle()->getDecodedTranslation_de() .
                     "</td><td>" . $artwork->getArtist()->getLastname() . ", " .
                     $artwork->getArtist()->getFirstname() . "</td><td>" . $artwork->getTechnique()
                        ->getTranslation()
                        ->getDecodedTranslation_de() . "</td><td>" .
                     $artwork->getMaterial()
                        ->getTranslation()
                        ->getDecodedTranslation_de() . "</td><td>" . $artwork->getAvailability()
                        ->getTranslation()
                        ->getDecodedTranslation_de() . "</td><td>" .
                     $artwork->getSignatureName() . "</td><td>" . $signaturedate .
                     "</td><td>" .
                     $artwork->getLocation()->getDecodedTranslation_de() .
                     "</td><td>" . $artwork->getWidth() . "</td><td>" .
                     $artwork->getHeight() . "</td><td>" . $artwork->getDepth() .
                     "</td><td>" . $artwork->getWeight() . "</td><td>" .
                     $artwork->getPrice() . "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getAllArtworksGui (array $allArtworks,
            TranslationController $tc, $targetUrl = "artwork_show.php?id"): string
    {
        $tableHeader = '<table><tr><th>' . $tc->getText("aw-title") . '</th><th>' .
                 $tc->getText("aw-width") . ' (cm)</th><th>' .
                 $tc->getText("aw-height") . ' (cm)</th><th>' .
                 $tc->getText("aw-avail") . '</th><th>' . $tc->getText(
                        "aw-date") . '</th><th>' . $tc->getText("aw-price") .
                 ' (Euro)</th></tr>';
        
        $tableBody = '';
        
        foreach ($allArtworks as $artwork) {
            $artwork->setLanguage($tc->getLanguage());
            $signaturedate = $artwork->getSignatureDate()->format('d.m.Y');
            $newTableRow = "<tr><td>" . "<a href='" . "$targetUrl" . "=" .
                     $artwork->getId() . "'>" .
                     $artwork->getTitle()->getTextForLanguage() . "</a></td><td>" .
                     $artwork->getWidthInCm() . "</td><td>" .
                     $artwork->getHeightInCm() . "</td><td>" .
                     $artwork->getAvailability()
                        ->getTranslation()
                        ->getTextForLanguage() . "</td><td>" . $signaturedate .
                     "</td><td>" . $tc->getPriceDisplayString($artwork) .
                     "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getAllAvailabilitiesAsTable (array $allAvailabilities,
            $targetUrl = "availability_update.php?id"): string
    {
        $tableHeader = "<table><tr><th>Id</th><th>Code</th><th>Text Deutsch</th><th>Text Englisch</th></tr>";
        
        $tableBody = '';
        
        foreach ($allAvailabilities as $availability) {
            
            $newTableRow = "<tr><td>" . "<a href='" . "$targetUrl" . "=" .
                     $availability->getId() . "'>" . $availability->getId() .
                     "</a></td><td>" . $availability->getTranslation()->getCode() .
                     "</td><td>" .
                     $availability->getTranslation()->getDecodedTranslation_de() .
                     "</td><td>" .
                     $availability->getTranslation()->getDecodedTranslation_en() .
                     "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getAllMaterialsAsTable (array $allMaterials,
            $targetUrl = "material_update.php?id"): string
    {
        $tableHeader = "<table><tr><th>Id</th><th>Code</th><th>Text Deutsch</th><th>Text Englisch</th></tr>";
        
        $tableBody = '';
        
        foreach ($allMaterials as $material) {
            
            $newTableRow = "<tr><td>" . "<a href='" . "$targetUrl" . "=" .
                     $material->getId() . "'>" . $material->getId() .
                     "</a></td><td>" . $material->getTranslation()->getCode() .
                     "</td><td>" .
                     $material->getTranslation()->getDecodedTranslation_de() .
                     "</td><td>" .
                     $material->getTranslation()->getDecodedTranslation_en() .
                     "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getAllTechniquesAsTable (array $allTechniques,
            $targetUrl = "technique_update.php?id"): string
    {
        $tableHeader = "<table><tr><th>Id</th><th>Code</th><th>Text Deutsch</th><th>Text Englisch</th></tr>";
        
        $tableBody = '';
        
        foreach ($allTechniques as $technique) {
            
            $newTableRow = "<tr><td>" . "<a href='" . "$targetUrl" . "=" .
                     $technique->getId() . "'>" . $technique->getId() .
                     "</a></td><td>" . $technique->getTranslation()->getCode() .
                     "</td><td>" .
                     $technique->getTranslation()->getDecodedTranslation_de() .
                     "</td><td>" .
                     $technique->getTranslation()->getDecodedTranslation_en() .
                     "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getAllExhibitionsAsTable (array $allExhibitions,
            $targetUrl = "exhibition_update.php?id"): string
    {
        $tableHeader = "<table><tr><th>Id</th><th>Kurzbezeichnung</th><th>Titel Deutsch</th><th>Titel Englisch</th><th>Startdatum</th><th>Abschlußdatum</th><th>Öffnungszeiten</th><th>Anschrift</th></tr>";
        
        $tableBody = '';
        
        foreach ($allExhibitions as $exhibition) {
            
            $newTableRow = "<tr><td>" . "<a href='" . "$targetUrl" . "=" .
                     $exhibition->getId() . "'>" . $exhibition->getId() .
                     "</a></td><td>" . $exhibition->getTitle()->getCode() .
                     "</td><td>" .
                     $exhibition->getTitle()->getDecodedTranslation_de() .
                     "</td><td>" .
                     $exhibition->getTitle()->getDecodedTranslation_en() .
                     "</td><td>" . $exhibition->getStart()->format('d.m.Y') .
                     "</td><td>" . $exhibition->getEnd()->format('d.m.Y') .
                     "</td><td>" . $exhibition->getOpeningHours() . "</td><td>" .
                     $exhibition->getAddress() . "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getAllExhibitionsGui (array $allExhibitions,
            TranslationController $tc, $targetUrl = "exhibition_show.php?id"): string
    {
        $tableHeader = '<table><tr><th>' . $tc->getText("ex-title") . '</th><th>' .
                 $tc->getText("ex-start") . '</th><th>' . $tc->getText("ex-end") .
                 '</th><th>' . $tc->getText("ex-oh") . '</th><th>' . $tc->getText(
                        "ex-addr") . '</th></tr>';
        
        $tableBody = '';
        
        foreach ($allExhibitions as $exhibition) {
            $exhibition->setLanguage($tc->getLanguage());
            $startDate = $exhibition->getStart()->format('d.m.Y');
            $endDate = $exhibition->getEnd()->format('d.m.Y');
            $newTableRow = "<tr><td>" . "<a href='" . "$targetUrl" . "=" .
                     $exhibition->getId() . "'>" .
                     $exhibition->getTitle()->getTextForLanguage() .
                     "</a></td><td>" . $startDate . "</td><td>" . $endDate .
                     "</td><td>" . $exhibition->getOpeningHours() . "</td><td>" .
                     $exhibition->getAddress() . "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getExhibitionGui (Exhibition $exhibition,
            array $exhibitionReports, TranslationController $tc): string
    {
        $exhibition->setLanguage($tc->getLanguage());
        
        $descriptionGui = $exhibition->getDescription()->getTextForLanguage() .
                 "<br/><br/>" . "<table class='centered-table'><tr><td>" .
                 $tc->getText("ex-start") . "</td><td>" .
                 $exhibition->getStart()->format('d.m.Y') . "</td></tr><tr><td>" .
                 $tc->getText("ex-end") . "</td><td>" .
                 $exhibition->getEnd()->format('d.m.Y') . "</td></tr><tr><td>" .
                 $tc->getText("ex-oh") . "</td><td>" .
                 $exhibition->getOpeningHours() . "</td></tr><tr><td>" .
                 $tc->getText("ex-addr") . "</td><td>" .
                 $exhibition->getAddress() . "</td></tr></table>";
        
        $reportGui = "";
        
        foreach ($exhibitionReports as $report) {
            $report->setLanguage($tc->getLanguage());
            $reportHeadline = "<h2>" .
                     $report->getTitleTranslation()->getTextForLanguage() .
                     "</h2>";
            $reportText = $report->getTextTranslation()->getTextForLanguage() .
                     "<br /><br />";
            $reportSignature = $report->getAuthor() . ", " .
                     $report->getPublicationDate()->format("d.m.Y");
            $reportGui .= $reportHeadline . $reportText . $reportSignature;
        }
        
        return ($descriptionGui . $reportGui);
    }

    public function getAllReportsAsTable (array $allReports,
            $targetUrl = "report_update.php?id"): string
    {
        $tableHeader = "<table><tr><th>Id</th><th>Autor</th><th>Publikationsdatum</th><th>Ablaufdatum</th><th>Kurzbezeichnung</th><th>Titel Deutsch</th><th>Titel Englisch</th><th>Text Deutsch</th><th>Text Englisch</th></tr>";
        
        $tableBody = '';
        
        foreach ($allReports as $report) {
            
            $textLength_de = strlen(
                    $report->getTextTranslation()->getDecodedTranslation_de());
            $textLength_en = strlen(
                    $report->getTextTranslation()->getDecodedTranslation_en());
            
            $truncatedReportText_de = substr(
                    $report->getTextTranslation()->getDecodedTranslation_de(), 0,
                    50) . " ... insgesamt " . "$textLength_de" . " Zeichen";
            $truncatedReportText_en = substr(
                    $report->getTextTranslation()->getDecodedTranslation_en(), 0,
                    50) . " ... insgesamt " . "$textLength_en" . " Zeichen";
            
            $newTableRow = "<tr><td>" . "<a href='" . "$targetUrl" . "=" .
                     $report->getId() . "'>" . $report->getId() . "</a></td><td>" .
                     $report->getAuthor() . "</td><td>" .
                     $report->getPublicationDate()->format('d.m.Y') . "</td><td>" .
                     $report->getObsolescenceDate()->format('d.m.Y') .
                     "</td><td>" . $report->getTextTranslation()->getCode() .
                     "</td><td>" .
                     $report->getTitleTranslation()->getDecodedTranslation_de() .
                     "</td><td>" .
                     $report->getTitleTranslation()->getDecodedTranslation_en() .
                     "</td><td>" . $truncatedReportText_de . "</td><td>" .
                     $truncatedReportText_en . "</td></tr>";
            
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getAllReportsGui (array $allReports,
            TranslationController $tc, $targetUrl = "report_show.php?id"): string
    {
        $tableHeader = '<table><tr><th>' . $tc->getText("re-title") . '</th><th>' .
                 $tc->getText("re-aut") . '</th><th>' . $tc->getText("re-pub") .
                 '</th></tr>';
        
        $tableBody = '';
        
        foreach ($allReports as $report) {
            $report->setLanguage($tc->getLanguage());
            $publicationDate = $report->getPublicationDate()->format('d.m.Y');
            $newTableRow = "<tr><td>" . "<a href='" . "$targetUrl" . "=" .
                     $report->getId() . "'>" .
                     $report->getTitleTranslation()->getTextForLanguage() .
                     "</a></td><td>" . $report->getAuthor() . "</td><td>" .
                     $publicationDate . "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getAllPhotosAsTable (array $allPhotos,
            $targetUrl = "photo_update.php?id"): string
    {
        $tableHeader = "<table><tr><th>Id</th><th>Kunstwerk</th><th>Filename</th><th>Speicherdatum</th></tr>";
        
        $tableBody = '';
        
        foreach ($allPhotos as $photo) {
            
            $newTableRow = "<tr><td>" . "<a href='" . "$targetUrl" . "=" .
                     $photo->getId() . "'>" . $photo->getId() . "</a></td><td>" . $photo->getArtwork()
                        ->getTitle()
                        ->getDecodedTranslation_de() . ", " .
                     $photo->getArtwork()
                        ->getArtist()
                        ->getLastname() . "</td><td>" . $photo->getFilename() .
                     "</td><td>" . $photo->getUploadDate()->format('d.m.Y') .
                     "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getAllOtherPhotosAsTable (array $allPhotos,
            $targetUrl = "photo_other_update.php?id"): string
    {
        $tableHeader = "<table><tr><th>Id</th><th>Filename</th><th>Speicherdatum</th></tr>";
        
        $tableBody = '';
        
        foreach ($allPhotos as $photo) {
            
            $newTableRow = "<tr><td>" . "<a href='" . "$targetUrl" . "=" .
                     $photo->getId() . "'>" . $photo->getId() . "</a></td><td>" .
                     $photo->getFilename() . "</td><td>" .
                     $photo->getUploadDate()->format('d.m.Y') . "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getAllGalleriesAsTable (array $allGalleries,
            $targetUrl = "gallery_update.php?id"): string
    {
        $tableHeader = "<table><tr><th>Id</th><th>Kurzbezeichnung</th><th>Titel</th><th>Beschreibung</th><th>Anzahl Bilder</th></tr>";
        
        $tableBody = '';
        
        foreach ($allGalleries as $gallery) {
            
            $newTableRow = "<tr><td>" . "<a href='" . "$targetUrl" . "=" .
                     $gallery->getId() . "'>" . $gallery->getId() .
                     "</a></td><td>" . $gallery->getTitle()->getCode() .
                     "</td><td>" .
                     $gallery->getTitle()->getDecodedTranslation_de() .
                     "</td><td>" .
                     $gallery->getDescription()->getDecodedTranslation_de() .
                     "</td><td>" . $gallery->getNumberOfGalleryPhotos() .
                     "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getArtistsForSelection (array $artists, $selectName,
            int $artistId = -1): string
    {
        $selectHead = "<select name='" . "$selectName" . "'>";
        
        $selectBody = '';
        
        $rowNumber = 0;
        
        foreach ($artists as $artist) {
            if ((($rowNumber == 0) && ($artistId == - 1)) ||
                     ($artist->getId() == $artistId)) {
                $newSelectRow = "<option selected value = '" . $artist->getId() .
                 "' >" . $artist->getLastname() . ", " . $artist->getFirstname() .
                 "</option>";
    } else {
        $newSelectRow = "<option value = '" . $artist->getId() . "' >" .
                 $artist->getLastname() . ", " . $artist->getFirstname() .
                 "</option>";
    }
    
    $selectBody = $selectBody . $newSelectRow;
    $rowNumber ++;
}

$selectFooter = "</select>";

return ($selectHead . $selectBody . $selectFooter);
}

public function getExhibitionsForSelection (array $exhibitions, $selectName,
    int $exhibitionId = -1): string
{
$selectHead = "<select name='" . "$selectName" . "'>";
$selectBody = '';

if ($exhibitionId == - 1) {
    $selectBody = "<option selected value ='-1'>Unbekannt</option>";
}

$rowNumber = 0;

foreach ($exhibitions as $exhibition) {
    if (($exhibition->getId() == $exhibitionId)) {
        $newSelectRow = "<option selected value = '" . $exhibition->getId() .
                 "' >" .
                 $exhibition->getTitle()->getDecodedTranslation_de() . ", " .
                 $exhibition->getStart()->format("d.m.Y") . "</option>";
    } else {
        $newSelectRow = "<option value = '" . $exhibition->getId() . "' >" .
                 $exhibition->getTitle()->getDecodedTranslation_de() . ", " .
                 $exhibition->getStart()->format("d.m.Y") . "</option>";
    }
    
    $selectBody = $selectBody . $newSelectRow;
    $rowNumber ++;
}

if ($exhibitionId != - 1) {
    $selectBody = $selectBody . "<option value ='-1'>Unbekannt</option>";
}

$selectFooter = "</select>";

return ($selectHead . $selectBody . $selectFooter);
}

public function getTechniquesForSelection (array $techniques, $selectName,
    int $techniqueId = -1): string
{
$selectHead = "<select name='" . "$selectName" . "'>";

$selectBody = '';

$rowNumber = 0;

foreach ($techniques as $technique) {
    if ((($rowNumber == 0) && ($techniqueId == - 1)) ||
             ($technique->getId() == $techniqueId)) {
        $newSelectRow = "<option selected value = '" . $technique->getId() .
         "' >" . $technique->getTranslation()->getDecodedTranslation_de() .
         "</option>";
} else {
$newSelectRow = "<option value = '" . $technique->getId() . "' >" .
         $technique->getTranslation()->getDecodedTranslation_de() . "</option>";
}

$selectBody = $selectBody . $newSelectRow;
$rowNumber ++;
}

$selectFooter = "</select>";

return ($selectHead . $selectBody . $selectFooter);
}

public function getMaterialsForSelection (array $materials, $selectName,
int $materialId = -1): string
{
$selectHead = "<select name='" . "$selectName" . "'>";

$selectBody = '';

$rowNumber = 0;

foreach ($materials as $material) {
if ((($rowNumber == 0) && ($materialId == - 1)) ||
     ($material->getId() == $materialId)) {
$newSelectRow = "<option selected value = '" . $material->getId() . "' >" .
 $material->getTranslation()->getDecodedTranslation_de() . "</option>";
} else {
$newSelectRow = "<option value = '" . $material->getId() . "' >" .
 $material->getTranslation()->getDecodedTranslation_de() . "</option>";
}

$selectBody = $selectBody . $newSelectRow;
$rowNumber ++;
}

$selectFooter = "</select>";

return ($selectHead . $selectBody . $selectFooter);
}

public function getAvailabilitiesForSelection (array $availabilities, $selectName,
int $availabilityId = -1): string
{
$selectHead = "<select name='" . "$selectName" . "'>";

$selectBody = '';

$rowNumber = 0;

foreach ($availabilities as $availability) {
if ((($rowNumber == 0) && ($availabilityId == - 1)) ||
 ($availability->getId() == $availabilityId)) {
$newSelectRow = "<option selected value = '" . $availability->getId() . "' >" .
 $availability->getTranslation()->getDecodedTranslation_de() . "</option>";
} else {
$newSelectRow = "<option value = '" . $availability->getId() . "' >" .
 $availability->getTranslation()->getDecodedTranslation_de() . "</option>";
}

$selectBody = $selectBody . $newSelectRow;
$rowNumber ++;
}

$selectFooter = "</select>";

return ($selectHead . $selectBody . $selectFooter);
}

public function getArtworksForSelection (array $artworks, $selectName,
int $artworkId = -1): string
{
$selectHead = "<select name='" . "$selectName" . "'>";

$selectBody = '';

$rowNumber = 0;

foreach ($artworks as $artwork) {
if ((($rowNumber == 0) && ($artworkId == - 1)) ||
 ($artwork->getId() == $artworkId)) {
$newSelectRow = "<option selected value = '" . $artwork->getId() . "' >" .
 $artwork->getTitle()->getDecodedTranslation_de() . ", " .
 $artwork->getArtist()->getLastname() . "</option>";
} else {
$newSelectRow = "<option value = '" . $artwork->getId() . "' >" .
 $artwork->getTitle()->getDecodedTranslation_de() . ", " .
 $artwork->getArtist()->getLastname() . "</option>";
}

$selectBody = $selectBody . $newSelectRow;
$rowNumber ++;
}

$selectFooter = "</select>";

return ($selectHead . $selectBody . $selectFooter);
}

public function getPhotosSelectionBody (array $photos, int $photoId = -1): string
{
$selectBody = '';

if ($photoId == - 1) {
$selectBody = "<option selected value ='-1'>Unbekannt</option>";
}

$rowNumber = 0;

foreach ($photos as $photo) {
if (($photo->getId() == $photoId)) {
$newSelectRow = "<option selected value = '" . $photo->getId() . "' >" . $photo->getArtwork()
->getTitle()
->getDecodedTranslation_de() . ", " . $photo->getFilename() . "</option>";
} else {
$newSelectRow = "<option value = '" . $photo->getId() . "' >" . $photo->getArtwork()
->getTitle()
->getDecodedTranslation_de() . ", " . $photo->getFilename() . "</option>";
}

$selectBody = $selectBody . $newSelectRow;
$rowNumber ++;
}

if ($photoId != - 1) {
$selectBody = $selectBody . "<option value ='-1'>Unbekannt</option>";
}

return $selectBody;
}
}

?>