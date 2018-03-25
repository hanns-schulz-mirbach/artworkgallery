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
class TranslationType
{

    // 1 = sytem translation, 2 = end user translation
    private $typeCode;

    public function __construct ()
    {
        $this->typeCode = 2; // end user translation
    }

    public function __toString (): string
    {
        switch ($this->typeCode) {
            case 1:
                $typeDescription = "System Text";
                break;
            
            case 2:
                $typeDescription = "Endbenutzer Text";
                break;
            
            default:
                $typetDescription = "Endbenutzer Text";
                break;
        }
        
        return $typeDescription;
    }

    public function getTypeCode (): int
    {
        return $this->typeCode;
    }

    public function setTypeCode (int $typeCode)
    {
        $this->typeCode = $typeCode;
    }

    public function isSystemText (): bool
    {
        return (($this->typeCode == 1));
    }

    public function isEndUserText (): bool
    {
        return (($this->typeCode == 2));
    }

    public function setIsSystemText ()
    {
        $this->typeCode = 1;
    }

    public function setIsEndUserText ()
    {
        $this->typeCode = 2;
    }

    public function isTranslationTypeValid (): bool
    {
        return (($this->typeCode == 1) || ($this->typeCode == 2));
    }

    public static function getNumberOfTranslationTypeValues ()
    {
        // There are two translation type values:
        // 1=system text
        // 2=end user text
        return 2;
    }

    public static function getLowestTranslationTypeValue ()
    {
        return 1;
    }
}

?>