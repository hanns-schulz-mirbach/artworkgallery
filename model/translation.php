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

require_once ("./util/translation_type.php");
require_once ("./util/language.php");

class Translation
{

    private $id;

    private $code;

    private $type;

    private $language;

    private $translation_de;

    private $translation_en;

    function __construct ()
    {
        // default id. The final id is generated later automatically by the
        // database
        $this->id = - 1;
        
        $this->item = '';
        $this->type = new TranslationType();
        $this->language = new Language();
        $this->translation_de = '';
        $this->translation_en = '';
    }

    public function __toString (): string
    {
        $translationAsHTMLTable = '<table><tr><th>Attribut</th><th>Wert</th></tr><tr><td>Id:</td><td>' .
                 $this->id . '</td></tr><tr><td>Kennung:</td><td>' . $this->code .
                 '</td></tr><tr><td>Typ:</td><td>' . $this->type .
                 '</td></tr><tr><td>Text Deutsch:</td><td>' .
                 $this->getDecodedTranslation_de() .
                 '</td></tr><tr><td>Text Englisch:</td><td>' .
                 $this->getDecodedTranslation_en() . '</td></tr></table>';
        
        return $translationAsHTMLTable;
    }

    public function isTranslationValid (): bool
    {
        return ($this->idIsValid() && $this->codeIsValid() &&
                 $this->typeIsValid() && $this->translationDeIsValid() &&
                 $this->translationEnIsValid());
    }

    public function getId (): int
    {
        return $this->id;
    }

    public function getCode (): string
    {
        return $this->code;
    }

    public function getType (): TranslationType
    {
        return $this->type;
    }

    public function getLanguage (): Language
    {
        return $this->language;
    }

    public function getTextForLanguage (): string
    {
        if ($this->language->isGerman()) {
            $text = $this->getDecodedTranslation_de();
        } elseif ($this->language->isEnglish()) {
            $text = $this->getDecodedTranslation_en();
        } else {
            $text = $this->getDecodedTranslation_de();
        }
        
        return html_entity_decode($text, ENT_QUOTES | ENT_XML1, "UTF-8");
    }

    public function getEncodedTranslation_de (): string
    {
        return $this->translation_de;
    }

    public function getDecodedTranslation_de (): string
    {
        $text_de = $this->translation_de;
        $textDecoded = html_entity_decode($text_de, ENT_QUOTES | ENT_XML1,
                "UTF-8");
        return html_entity_decode($textDecoded, ENT_QUOTES | ENT_XML1, "UTF-8");
    }

    public function getEncodedTranslation_en (): string
    {
        return $this->translation_en;
    }

    public function getDecodedTranslation_en (): string
    {
        $text_de = $this->translation_en;
        $textDecoded = html_entity_decode($text_de, ENT_QUOTES | ENT_XML1,
                "UTF-8");
        return html_entity_decode($textDecoded, ENT_QUOTES | ENT_XML1, "UTF-8");
    }

    public function setId (int $id): void
    {
        $this->id = $id;
    }

    public function setCode (string $code): void
    {
        $this->code = $code;
    }

    public function setType (TranslationType $type): void
    {
        $this->type = $type;
    }

    public function setLanguage (Language $language): void
    {
        $this->language = $language;
    }

    public function setTranslation_de (string $translation_de): void
    {
        $this->translation_de = htmlentities($translation_de, ENT_QUOTES,
                "UTF-8");
    }

    public function setTranslation_en (string $translation_en): void
    {
        $this->translation_en = htmlentities($translation_en, ENT_QUOTES,
                "UTF-8");
    }

    public function setRawTranslation_de (string $translation_de): void
    {
        $this->translation_de = $translation_de;
    }

    public function setRawTranslation_en (string $translation_en): void
    {
        $this->translation_en = $translation_en;
    }

    private function idIsValid (): bool
    {
        return (isset($this->id) && is_int($this->id));
    }

    private function codeIsValid (): bool
    {
        return (isset($this->code) && is_string($this->code));
    }

    private function typeIsValid (): bool
    {
        return $this->type->isTranslationTypeValid();
    }

    private function translationDeIsValid (): bool
    {
        return (isset($this->translation_de) && is_string($this->translation_de));
    }

    private function translationEnIsValid (): bool
    {
        return (isset($this->translation_en) && is_string($this->translation_en));
    }
}
?>
