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
require_once ("./model/exhibition.php");
require_once ("./util/language.php");

class Report
{

    private $id;

    private $author;

    private $titleTranslation;

    private $textTranslation;

    private $publicationDate;

    private $obsolescenceDate;

    private $exhibition;

    function __construct ()
    {
        // default id. The final id is generated later automatically by the
        // database
        $this->id = - 1;
        
        $this->author = "";
        $this->titleTranslation = new Translation();
        $this->textTranslation = new Translation();
        $this->publicationDate = new DateTime();
        $this->obsolescenceDate = new DateTime();
        $this->exhibition = new Exhibition();
    }

    public function __toString (): string
    {
        $reportAsHTMLTable = '<table><tr><th>Attribut</th><th>Wert</th></tr><tr><td>Id:</td><td>' .
                 $this->id . '</td></tr><tr><td>Autor</td><td>' . $this->author .
                 '</td></tr><tr><td>Publikationsdatum</td><td>' .
                 $this->publicationDate->format('d.m.Y') .
                 '</td></tr><tr><td>Verfallsdatum</td><td>' .
                 $this->obsolescenceDate->format('d.m.Y') .
                 '</td></tr><tr><td>AusstellungsId</td><td>' . $this->exhibition->getId() .
                 '</td></tr><tr><td>Titel</td><td>' . $this->titleTranslation .
                 '</td></tr><tr><td>Text</td><td>' . $this->textTranslation .
                 '</td></tr></table>';
        
        return $reportAsHTMLTable;
    }

    public function isReportValid (): bool
    {
        return ($this->idIsValid() &&
                 $this->titleTranslation->isTranslationValid() &&
                 $this->textTranslation->isTranslationValid() &&
                 $this->datesAreValid() && $this->exhibition->isExhibitionValid());
    }

    public function getId (): int
    {
        return $this->id;
    }

    public function setId (int $id): void
    {
        $this->id = $id;
    }

    public function getAuthor (): string
    {
        return $this->author;
    }

    public function getTitleTranslation (): Translation
    {
        return $this->titleTranslation;
    }

    public function getTextTranslation (): Translation
    {
        return $this->textTranslation;
    }

    public function getPublicationDate (): DateTime
    {
        return $this->publicationDate;
    }

    public function getObsolescenceDate (): DateTime
    {
        return $this->obsolescenceDate;
    }

    public function getExhibition (): Exhibition
    {
        return $this->exhibition;
    }

    public function setAuthor (string $author): void
    {
        $this->author = $author;
    }

    public function setTitleTranslation (Translation $titleTranslation): void
    {
        $this->titleTranslation = $titleTranslation;
    }

    public function setTextTranslation (Translation $textTranslation): void
    {
        $this->textTranslation = $textTranslation;
    }

    public function setPublicationDate (DateTime $publicationDate): void
    {
        $this->publicationDate = $publicationDate;
    }

    public function setObsolescenceDate (DateTime $obsolescenceDate): void
    {
        $this->obsolescenceDate = $obsolescenceDate;
    }

    public function setExhibition (Exhibition $exhibition): void
    {
        $this->exhibition = $exhibition;
    }

    public function setLanguage (Language $language): void
    {
        $this->getTitleTranslation()->setLanguage($language);
        $this->getTextTranslation()->setLanguage($language);
        $this->exhibition->setLanguage($language);
    }

    private function idIsValid (): bool
    {
        return (isset($this->id) && is_int($this->id));
    }

    private function datesAreValid (): bool
    {
        $datesAreSet = (isset($this->publicationDate) &&
                 isset($this->obsolescenceDate));
        $dateOrderIsCorrect = ($this->publicationDate <= $this->obsolescenceDate);
        
        return ($datesAreSet && $dateOrderIsCorrect);
    }
}
?>
