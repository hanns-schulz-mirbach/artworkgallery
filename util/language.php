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

class Language
{

    // 0=German, 1=English
    private $languageId;

    private $languageCodeDictionary;

    private $knownLanguages;

    public function __construct ()
    {
        $this->languageId = 0; // German
        
        $this->languageCodeDictionary = array(
                "de" => 0,
                "en" => 1
        );
        
        $this->knownLanguages = array(
                "de",
                "en"
        );
    }

    public function __toString (): string
    {
        if (isset($this->knownLanguages[$this->languageId])) {
            return $this->knownLanguages[$this->languageId];
        } else {
            return "Unknown";
        }
    }

    public function isGerman (): bool
    {
        return (($this->languageId == 0));
    }

    public function isEnglish (): bool
    {
        return (($this->languageId == 1));
    }

    public function getLanguageId (): int
    {
        return $this->languageId;
    }

    public function setLanguageId (int $languageId): void
    {
        $this->languageId = $languageId;
    }

    public function setIsGerman ()
    {
        $this->languageId = 0;
    }

    public function setIsEnglish ()
    {
        $this->languageId = 1;
    }

    public function isLanguageValid (): bool
    {
        return (($this->languageId == 0) || ($this->languageId == 1));
    }

    public function setLanguageFromBrowserSettings ()
    {
        $this->languageId = $this->extractLanguageFromHttpRequest();
    }

    public static function getNumberOfLanguages ()
    {
        // There are two result values:
        // 0=German
        // 1=English
        return 2;
    }

    public static function getLowestLanguageValue ()
    {
        return 0;
    }

    private function extractLanguageFromHttpRequest (): int
    {
        $browserLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        
        foreach ($browserLanguages as $language) {
            $languageCode = substr($language, 0, 2);
            if (in_array($languageCode, $this->knownLanguages)) {
                return ($this->languageCodeDictionary[$languageCode]);
            }
        }
        
        // no match with known languages found. Return default 0 (German)
        return 0;
    }
}

?>