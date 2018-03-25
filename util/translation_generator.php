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
require_once ("./controller/translation_controller.php");
require_once ("./util/language.php");
require_once ("./util/translation_defaults.php");

class TranslationGenerator
{

    private $translationController;

    private $systemTranslations;

    private $userTranslations;

    public function __construct ()
    {
        $language = new Language();
        $this->translationController = new TranslationController($language);
        
        $translationDefaults = new TranslationDefaults();
        
        $this->systemTranslations = $translationDefaults->getDefaultSystemTranslations();
        $this->userTranslations = $translationDefaults->getDefaultUserTranslations();
    }

    public function generateAllSystemTranslations (): int
    {
        $numOfDeletedRows = $this->translationController->deleteAllSystemTranslations();
        
        $numOfInsertedRows = 0;
        
        foreach ($this->systemTranslations as $translation) {
            $this->translationController->setTranslation($translation);
            $numOfInsertedRows += $this->translationController->insertTranslation();
        }
        
        return $numOfInsertedRows;
    }

    public function generateAllUserTranslations (): int
    {
        $numOfDeletedRows = 0;
        $numOfInsertedRows = 0;
        
        foreach ($this->userTranslations as $translation) {
            $this->translationController->setTranslation($translation);
            $numOfDeletedRows += $this->translationController->deleteUserTranslationsByCode(
                    $translation->getCode());
            $numOfInsertedRows += $this->translationController->insertTranslation();
        }
        
        return $numOfInsertedRows;
    }
}

?>