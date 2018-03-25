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
require_once ("./util/language.php");

class Exhibition
{

    private $id;

    private $title;

    private $description;

    private $start;

    private $end;

    private $openingHours;

    private $address;

    function __construct ()
    {
        // default id. The final id is generated later automatically by the
        // database
        $this->id = - 1;
        
        $this->title = new Translation();
        $this->description = new Translation();
        $this->start = new DateTime();
        $this->end = new DateTime();
        $this->openingHours = "";
        $this->address = "";
    }

    public function __toString (): string
    {
        $exhibitionAsHTMLTable = '<table><tr><th>Attribut</th><th>Wert</th></tr><tr><td>Id:</td><td>' .
                 $this->id . '</td></tr><tr><td>Ausstellungstitel</td><td>' .
                 $this->title . '</td></tr><tr><td>Beschreibung</td><td>' .
                 $this->description . '</td></tr><tr><td>Start</td><td>' .
                 $this->start->format("d.m.Y") .
                 '</td></tr><tr><td>Ende</td><td>' . $this->end->format("d.m.Y") .
                 '</td></tr><tr><td>Öffnungszeiten</td><td>' .
                 $this->openingHours . '</td></tr><tr><td>Adresse</td><td>' .
                 $this->address . '</td></tr></table>';
        
        return $exhibitionAsHTMLTable;
    }

    public function isExhibitionValid (): bool
    {
        return ($this->idIsValid() && $this->title->isTranslationValid() &&
                 $this->description->isTranslationValid() &&
                 $this->datesAreValid() && $this->openingHoursIsValid() &&
                 $this->addressIsValid());
    }

    public function getId (): int
    {
        return $this->id;
    }

    public function getTitle (): Translation
    {
        return $this->title;
    }

    public function getDescription (): Translation
    {
        return $this->description;
    }

    public function setId (int $id): void
    {
        $this->id = $id;
    }

    public function setTitle (Translation $title): void
    {
        $this->title = $title;
    }

    public function setDescription (Translation $description): void
    {
        $this->description = $description;
    }

    public function getStart (): DateTime
    {
        return $this->start;
    }

    public function getEnd (): DateTime
    {
        return $this->end;
    }

    public function getOpeningHours (): string
    {
        return $this->openingHours;
    }

    public function getAddress (): string
    {
        return $this->address;
    }

    public function setStart (DateTime $start): void
    {
        $this->start = $start;
    }

    public function setEnd (DateTime $end): void
    {
        $this->end = $end;
    }

    public function setOpeningHours (string $openingHours): void
    {
        $this->openingHours = $openingHours;
    }

    public function setAddress (string $address): void
    {
        $this->address = $address;
    }

    public function setLanguage (Language $language): void
    {
        $this->getTitle()->setLanguage($language);
        $this->getDescription()->setLanguage($language);
    }

    private function idIsValid (): bool
    {
        return (isset($this->id) && is_int($this->id));
    }

    private function datesAreValid (): bool
    {
        $startIsSet = (isset($this->start) && ($this->start instanceof DateTime));
        $endIsSet = (isset($this->end) && ($this->end instanceof DateTime));
        $startBeforeEnd = ($this->start < $this->end);
        
        return ($startIsSet && $endIsSet && $startBeforeEnd);
    }

    private function openingHoursIsValid (): bool
    {
        return (isset($this->openingHours));
    }

    private function addressIsValid (): bool
    {
        return (isset($this->address));
    }
}
?>
