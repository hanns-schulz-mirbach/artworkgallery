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
require_once ("./model/photo.php");
require_once ("./model/artwork.php");
require_once ("./model/artist.php");
require_once ("./util/language.php");
require_once ("./util/translation_type.php");
require_once ("./util/translation_defaults.php");
require_once ("./util/user_role.php");
require_once ("./db/database.php");

class TranslationController
{

    private $language;

    private $translationDictionary;

    private $translationDefaults;

    private $userRole;

    private $translation;

    private $database;

    public function __construct (Language $language)
    {
        $this->translation = new Translation();
        $this->translation->setLanguage($language);
        $this->language = $language;
        $this->database = new Database();
        $this->translationDefaults = new TranslationDefaults();
        $this->initializeTranslationDictionary();
        $this->userRole = new UserRole();
    }

    public function __destruct ()
    {
        $this->database = null;
    }

    public function getLanguage (): Language
    {
        return $this->language;
    }

    public function getTranslation (): Translation
    {
        return $this->translation;
    }

    public function getUserRole (): UserRole
    {
        return $this->userRole;
    }

    public function getText (string $code): string
    {
        if (! isset($this->translationDictionary[$code])) {
            $text = "Unkown translation code '" . $code . "'";
        } else {
            $text = $this->translationDictionary[$code];
        }
        return $text;
    }

    public function setLanguage (Language $language): void
    {
        $this->language = $language;
    }

    public function setTranslation (Translation $translation): void
    {
        $this->translation = $translation;
    }

    public function setUserRole (UserRole $userRole): void
    {
        $this->userRole = $userRole;
    }

    public function instantiateSkeleton (string $code, int $type, string $text_de,
            string $text_en): void
    {
        $this->translation->setCode($code);
        $this->translation->getType()->setTypeCode($type);
        $this->translation->setTranslation_de($text_de);
        $this->translation->setTranslation_en($text_en);
    }

    public function insertTranslation (): int
    {
        $affectedRows = $this->database->insertTranslation($this->translation);
        return $affectedRows;
    }

    public function updateTranslation (): int
    {
        $affectedRows = $this->database->updateTranslation($this->translation);
        return $affectedRows;
    }

    public function deleteTranslation (): int
    {
        $affectedRows = $this->database->deleteTranslation($this->translation);
        return $affectedRows;
    }

    public function deleteAllSystemTranslations (): int
    {
        $affectedRows = $this->database->deleteAllSystemTranslations();
        return $affectedRows;
    }

    public function deleteUserTranslationsByCode (string $code): int
    {
        $affectedRows = $this->database->deleteUserTranslationsByCode($code);
        return $affectedRows;
    }

    public function getAllTranslationsByType (TranslationType $translationType): array
    {
        $allTranslations = [];
        
        if ($translationType->isTranslationTypeValid()) {
            $allTranslations = $this->database->getAllTranslationsByType(
                    $translationType);
        }
        
        return $allTranslations;
    }

    public function getTranslationById (int $translationId): Translation
    {
        return $this->database->getTranslationById($translationId);
    }

    public function getTranslationByCode (string $code): Translation
    {
        $dbTranslation = $this->database->getTranslationByCode($code);
        $translation = $this->checkForEmptyTranslation($dbTranslation, $code);
        return $translation;
    }

    public function getEndUserTextforCurrentLanguageByCode (string $code): string
    {
        $dbTranslation = $this->database->getEndUserTtranslationByCode($code);
        $translation = $this->checkForEmptyTranslation($dbTranslation, $code);
        return $this->getTextForCurrentLanguage($translation);
    }

    public function translationExistsInDatabase (Translation $translation): bool
    {
        return $this->database->translationExistsInDatabase($translation);
    }

    public function getCaptionForPhoto (Photo $photo): string
    {
        if ($this->language->isGerman()) {
            $caption = "Kunstwerk: " .
                     $photo->getArtwork()
                        ->getTitle()
                        ->getDecodedTranslation_de() . ", Künstler: " . $photo->getArtwork()
                        ->getArtist()
                        ->getLastname() . ", Foto: " . $photo->getFilename();
        } elseif ($this->language->isEnglish()) {
            $caption = "Artwork: " .
                     $photo->getArtwork()
                        ->getTitle()
                        ->getDecodedTranslation_en() . ", Artist: " . $photo->getArtwork()
                        ->getArtist()
                        ->getLastname() . ", Photo: " . $photo->getFilename();
        } else {
            $caption = "Artwork: " .
                     $photo->getArtwork()
                        ->getTitle()
                        ->getDecodedTranslation_en() . ", Artist: " . $photo->getArtwork()
                        ->getArtist()
                        ->getLastname() . ", Photo: " . $photo->getFilename();
        }
        
        return $caption;
    }

    public function getDateFormat (): string
    {
        if ($this->language->isGerman()) {
            $dateFormat = 'd.m.Y';
        } elseif ($this->language->isEnglish()) {
            $dateFormat = 'm-d-Y';
        } else {
            $dateFormat = 'm-d-Y';
        }
        
        return $dateFormat;
    }

    public function getShortCaptionForPhoto (Photo $photo): string
    {
        $openingTag = '<a href="artwork_show.php?id=' .
                 $photo->getArtwork()->getId() . '">';
        $closingTag = '</a>';
        if ($this->language->isGerman()) {
            $caption = $photo->getArtwork()
                ->getTitle()
                ->getDecodedTranslation_de() . ", " . $photo->getArtwork()
                ->getSignatureDate()
                ->format("d.m.Y");
        } elseif ($this->language->isEnglish()) {
            $caption = $photo->getArtwork()
                ->getTitle()
                ->getDecodedTranslation_en() . ", " . $photo->getArtwork()
                ->getSignatureDate()
                ->format("m-d-Y");
        } else {
            $caption = $photo->getArtwork()
                ->getTitle()
                ->getDecodedTranslation_en() . ", " . $photo->getArtwork()
                ->getSignatureDate()
                ->format("m-d-Y");
        }
        
        return $openingTag . $caption . $closingTag;
    }

    public function getLinkForLanguageSwitch (): string
    {
        if ($this->language->isGerman()) {
            $linkForLanguageSwitch = '<a href="language_switch_confirmation.php?id=1">En</a>';
        } elseif ($this->language->isEnglish()) {
            $linkForLanguageSwitch = '<a href="language_switch_confirmation.php?id=0">De</a>';
        } else {
            $linkForLanguageSwitch = '<a href="language_switch_confirmation.php?id=1">En</a>';
        }
        return $linkForLanguageSwitch;
    }

    public function getPriceDisplayString (Artwork $artwork)
    {
        if ($artwork->getPrice() == - 1) {
            $priceDisplayString = $this->getText("price-minus-one");
        } elseif ($artwork->getPrice() == 0) {
            $priceDisplayString = $this->getText("price-zero");
        } else {
            $priceDisplayString = $artwork->getPrice();
        }
        return $priceDisplayString;
    }

    public function getAdminNavigationLink (): string
    {
        if (! $this->userRole->isAdmin()) {
            $adminNavigationLink = "";
        } elseif ($this->userRole->isAdmin()) {
            $adminNavigationLink = ' | <a href="admin.php" target="_blank">Administration</a>';
        } else {
            $adminNavigationLink = "";
            ;
        }
        return $adminNavigationLink;
    }

    private function initializeTranslationDictionary (): void
    {
        $defaultSystemTranslations = $this->translationDefaults->getDefaultSystemTranslations();
        $this->transferSystemTranslationsToDictionary(
                $defaultSystemTranslations);
        
        $systemTranslationType = new TranslationType();
        $systemTranslationType->setIsSystemText();
        
        $allSystemTranslations = $this->getAllTranslationsByType(
                $systemTranslationType);
        
        $this->transferSystemTranslationsToDictionary($allSystemTranslations);
    }

    private function transferSystemTranslationsToDictionary (
            array $allSystemTranslations): void
    {
        if ($this->language->isGerman()) {
            foreach ($allSystemTranslations as $systemTranslation) {
                $this->translationDictionary[$systemTranslation->getCode()] = $systemTranslation->getDecodedTranslation_de();
            }
        } elseif ($this->language->isEnglish()) {
            foreach ($allSystemTranslations as $systemTranslation) {
                $this->translationDictionary[$systemTranslation->getCode()] = $systemTranslation->getDecodedTranslation_en();
            }
        } else {
            foreach ($allSystemTranslations as $systemTranslation) {
                $this->translationDictionary[$systemTranslation->getCode()] = $systemTranslation->getDecodedTranslation_en();
            }
        }
    }

    private function getTextForCurrentLanguage (Translation $translation): string
    {
        if ($this->language->isGerman()) {
            $text = $translation->getDecodedTranslation_de();
        } elseif ($this->language->isEnglish()) {
            $text = $translation->getDecodedTranslation_en();
        } else {
            $text = "Unknown language";
        }
        
        return $text;
    }

    private function checkForEmptyTranslation (Translation $translation,
            string $code): Translation
    {
        if ($translation->getId() != - 1) {
            return $translation;
        } else {
            $defaultSystemTranslation = $this->translationDefaults->getDefaultSystemTranslation(
                    $code);
            if ($defaultSystemTranslation->getId() != - 1) {
                return $defaultSystemTranslation;
            } else {
                $defaultUserTranslation = $this->translationDefaults->getDefaultUserTranslation(
                        $code);
                return $defaultUserTranslation;
            }
        }
    }
}

?>