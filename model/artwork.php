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
require_once ("./model/availability.php");
require_once ("./model/artist.php");
require_once ("./model/technique.php");
require_once ("./model/material.php");
require_once ("./util/language.php");

class Artwork
{

    private $id;

    private $artist;

    private $title;

    private $explanation;

    private $signatureDate;

    private $signatureName;

    private $technique;

    private $material;

    private $availability;

    private $location;

    // internal unit: millimeter
    private $width;

    // internal unit: millimeter
    private $height;

    // internal unit: millimeter
    private $depth;

    // internal unit: gram
    private $weight;

    // internal unit: euro
    private $price;

    public function getId (): int
    {
        return $this->id;
    }

    public function getArtist (): Artist
    {
        return $this->artist;
    }

    public function getWidth (): int
    {
        return $this->width;
    }

    public function getWidthInCm (): int
    {
        $widthInCm = intval(ceil($this->width / 100));
        return $widthInCm;
    }

    public function getHeight (): int
    {
        return $this->height;
    }

    public function getHeightInCm (): int
    {
        $heightInCm = intval(ceil($this->height / 100));
        return $heightInCm;
    }

    public function getDepth (): int
    {
        return $this->depth;
    }

    public function getDepthInCm (): int
    {
        $depthInCm = intval(ceil($this->depth / 100));
        return $depthInCm;
    }

    public function getWeight (): int
    {
        return $this->weight;
    }

    public function getWeightInKg (): int
    {
        $weightInKg = intval(ceil($this->weight / 1000));
        return $weightInKg;
    }

    public function getTitle (): Translation
    {
        return $this->title;
    }

    public function getExplanation (): Translation
    {
        return $this->explanation;
    }

    public function getSignatureDate (): DateTime
    {
        return $this->signatureDate;
    }

    public function getSignatureName (): string
    {
        return $this->signatureName;
    }

    public function getTechnique (): Technique
    {
        return $this->technique;
    }

    public function getMaterial (): Material
    {
        return $this->material;
    }

    public function getAvailability (): Availability
    {
        return $this->availability;
    }

    public function getPrice (): int
    {
        return $this->price;
    }

    public function getLocation (): Translation
    {
        return $this->location;
    }

    public function setId (int $id): void
    {
        $this->id = $id;
    }

    public function setArtist (Artist $artist): void
    {
        $this->artist = $artist;
    }

    public function setWidth (int $width): void
    {
        $this->width = $width;
    }

    public function setHeight (int $height): void
    {
        $this->height = $height;
    }

    public function setDepth (int $depth): void
    {
        $this->depth = $depth;
    }

    public function setWeight (int $weight): void
    {
        $this->weight = $weight;
    }

    public function setTitle (Translation $title): void
    {
        $this->title = $title;
    }

    public function setExplanation (Translation $explanation): void
    {
        $this->explanation = $explanation;
    }

    public function setSignatureDate (DateTime $signatureDate): void
    {
        $this->signatureDate = $signatureDate;
    }

    public function setSignatureName (string $signatureName): void
    {
        $this->signatureName = $signatureName;
    }

    public function setTechnique (Technique $technique): void
    {
        $this->technique = $technique;
    }

    public function setMaterial (Material $material): void
    {
        $this->material = $material;
    }

    public function setAvailability (Availability $availability): void
    {
        $this->availability = $availability;
    }

    public function setPrice (int $price): void
    {
        $this->price = $price;
    }

    public function setLocation (Translation $location): void
    {
        $this->location = $location;
    }

    public function setLanguage (Language $language): void
    {
        $this->getTitle()->setLanguage($language);
        $this->getExplanation()->setLanguage($language);
        $this->getTechnique()->setLanguage($language);
        $this->getMaterial()->setLanguage($language);
        $this->getAvailability()->setLanguage($language);
        $this->getLocation()->setLanguage($language);
    }

    function __construct ()
    {
        // default id. The final id is generated later automatically by the
        // database
        $this->id = - 1;
        
        $this->artist = new Artist();
        $this->availability = new Availability();
        $this->depth = 0;
        $this->height = 0;
        $this->location = new Translation();
        $this->material = new Material();
        $this->price = 0;
        $this->signatureDate = new DateTime();
        $this->signatureName = "";
        $this->technique = new Technique();
        $this->title = new Translation();
        $this->explanation = new Translation();
        $this->weight = 0;
        $this->width = 0;
    }

    public function __toString (): string
    {
        $availabilityAsHTMLTable = '<table><tr><th>Attribut</th><th>Wert</th></tr><tr><td>Id:</td><td>' .
                 $this->id . '</td></tr><tr><td>Künstler Id</td><td>' .
                 $this->artist->getId() . '</td></tr>' . '<tr><td>Titel</td><td>' .
                 $this->getTitle() . '</td></tr>' .
                 '<tr><td>Erläuterung</td><td>' . $this->getExplanation() .
                 '</td></tr>' . '<tr><td>Signatur</td><td>' .
                 $this->getSignatureName() . '</td></tr>' .
                 '<tr><td>Signaturdatum</td><td>' .
                 $this->getSignatureDate()->format("d.m.Y") . '</td></tr>' .
                 '<tr><td>Breite</td><td>' . $this->getWidth() . '</td></tr>' .
                 '<tr><td>Höhe</td><td>' . $this->getHeight() . '</td></tr>' .
                 '<tr><td>Tiefe</td><td>' . $this->getDepth() . '</td></tr>' .
                 '<tr><td>Gewicht</td><td>' . $this->getWeight() . '</td></tr>' .
                 '<tr><td>Technik Id</td><td>' . $this->getTechnique()->getId() .
                 '</td></tr>' . '<tr><td>Material Id</td><td>' .
                 $this->getMaterial()->getId() . '</td></tr>' .
                 '<tr><td>Verfügbarkeit Id</td><td>' .
                 $this->getAvailability()->getId() . '</td></tr>' .
                 '<tr><td>Ort</td><td>' . $this->getLocation() . '</td></tr>' .
                 '<tr><td>Preis</td><td>' . $this->getPrice() .
                 '</td></tr></td></tr></table>';
        
        return $availabilityAsHTMLTable;
    }

    public function isArtworkValid (): bool
    {
        return ($this->idIsValid() && $this->artist->isArtistValid() &&
                 $this->availability->isAvailabilityValid() &&
                 $this->technique->isTechniqueValid() &&
                 $this->material->isMaterialValid());
    }

    private function idIsValid (): bool
    {
        return (isset($this->id) && is_int($this->id));
    }
}
?>
