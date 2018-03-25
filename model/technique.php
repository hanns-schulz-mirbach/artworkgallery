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

class Technique
{

    private $id;

    private $translation;

    function __construct ()
    {
        // default id. The final id is generated later automatically by the
        // database
        $this->id = - 1;
        
        $this->translation = new Translation();
    }

    public function __toString (): string
    {
        $availabilityAsHTMLTable = '<table><tr><th>Attribut</th><th>Wert</th></tr><tr><td>Id:</td><td>' .
                 $this->id . '</td></tr><tr><td>Beschreibung</td><td>' .
                 $this->translation . '</td></tr></table>';
        
        return $availabilityAsHTMLTable;
    }

    public function isTechniqueValid (): bool
    {
        return ($this->idIsValid() && $this->translation->isTranslationValid());
    }

    public function getId (): int
    {
        return $this->id;
    }

    public function getTranslation (): Translation
    {
        return $this->translation;
    }

    public function setId (int $id): void
    {
        $this->id = $id;
    }

    public function setTranslation (Translation $translation): void
    {
        $this->translation = $translation;
    }

    public function setLanguage (Language $language): void
    {
        $this->getTranslation()->setLanguage($language);
    }

    private function idIsValid (): bool
    {
        return (isset($this->id) && is_int($this->id));
    }
}
?>
