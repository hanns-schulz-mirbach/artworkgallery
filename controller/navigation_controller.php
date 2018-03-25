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

require_once ("./util/language.php");
require_once ("./db/database.php");

class NavigationController
{

    private $language;

    private $navigationDictionary;

    private $database;

    public function __construct ()
    {
        $this->language = new Language();
        $this->database = new Database();
        
        $this->navigationDictionary = array(
                "startpage" => "Startseite",
                "about" => "Impressum",
                "help" => "Hilfe"
        );
    }

    public function __destruct ()
    {
        $this->database = null;
    }

    public function getTargetUrl (string $target): string
    {
        return $this->navigationDictionary[$target];
    }

    public function getLanguage (): Language
    {
        return $this->language;
    }

    public function setLanguage (Language $language)
    {
        $this->language = $language;
    }
}

?>