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

class TranslationDefaults
{

    private $systemTranslations;

    private $userTranslations;

    public function __construct ()
    {
        $this->systemTranslations = [];
        $this->userTranslations = [];
        
        // GUI translations (part of system translations))
        $this->addSystemTranslation("about", "Impressum", "About");
        $this->addSystemTranslation("terms", "Nutzungsbedingungen",
                "Terms of use");
        $this->addSystemTranslation("bio", "Biographie", "Biography");
        $this->addSystemTranslation("exhibition", "Ausstellungen", "Exhibitions");
        $this->addSystemTranslation("gallery", "Bildergalerie",
                "Picture gallery");
        $this->addSystemTranslation("help", "Hilfe", "Help");
        $this->addSystemTranslation("news", "Aktuelles", "News");
        $this->addSystemTranslation("startpage", "Startseite", "Home");
        $this->addSystemTranslation("art", "Kunst", "Art");
        $this->addSystemTranslation("title", "CATHRIN SCHULZ-MIRBACH",
                "CATHRIN SCHULZ-MIRBACH");
        $this->addSystemTranslation("aw-headline",
                "Detailinformationen zum Kunstwerk",
                "Particulars for the artwork");
        $this->addSystemTranslation("ga-headline", "Bildergalerie ",
                "Image gallery ");
        $this->addSystemTranslation("ga-play-hint",
                "Klicken auf ein Bild startet das Abspielen der Bildergalerie. Drücken von ESC beendet das Abspielen. ",
                "Clicking on an image starts the display of the image gallery. Pressing ESC ends it. ");
        $this->addSystemTranslation("art-headline", "Übersicht aller Kunstwerke",
                "Overview all artworks");
        $this->addSystemTranslation("art-galleries-headline", "Bildergalerien",
                "Artworks galleries");
        $this->addSystemTranslation("price-zero", "Auf Anfrage", "On request");
        $this->addSystemTranslation("price-minus-one", "Unverkäuflich",
                "Not for sale");
        
        // System messages (part of system translations))
        $this->addSystemTranslation("language-switch",
                "Die Sprache der Benutzeroberfläche wurde auf Deutsch umgestellt. ",
                "The language of the graphical user interface was changed to English.");
        
        // Object attribute translations (part of system translations)
        // Object Artwork
        $this->addSystemTranslation("aw-id", "Id", "Id");
        $this->addSystemTranslation("aw-title", "Titel", "Title");
        $this->addSystemTranslation("aw-artist", "Künstler", "Artist");
        $this->addSystemTranslation("aw-expl", "Erläuterung", "Elucidation");
        $this->addSystemTranslation("aw-signature", "Signatur", "Signature");
        $this->addSystemTranslation("aw-date", "Datum", "Date");
        $this->addSystemTranslation("aw-mat", "Material", "Material");
        $this->addSystemTranslation("aw-tech", "Technik", "Technique");
        $this->addSystemTranslation("aw-avail", "Verfügbarkeit", "Availability");
        $this->addSystemTranslation("aw-loc", "Ort", "Location");
        $this->addSystemTranslation("aw-width", "Breite", "Width");
        $this->addSystemTranslation("aw-height", "Höhe", "Height");
        $this->addSystemTranslation("aw-depth", "Tiefe", "Depth");
        $this->addSystemTranslation("aw-weight", "Gewicht", "Weight");
        $this->addSystemTranslation("aw-price", "Preis", "Price");
        
        // Object Exhibition
        $this->addSystemTranslation("ex-code", "Kürzel", "Code");
        $this->addSystemTranslation("ex-title", "Titel", "Title");
        $this->addSystemTranslation("ex-desc", "Beschreibung", "Description");
        $this->addSystemTranslation("ex-start", "Eröffnungsdatum",
                "Opening date");
        $this->addSystemTranslation("ex-end", "Abschlußdatum", "Closing date");
        $this->addSystemTranslation("ex-oh", "Öffungszeiten", "Opening hours");
        $this->addSystemTranslation("ex-addr", "Anschrift", "Address");
        
        // Object Report
        $this->addSystemTranslation("re-code", "Kürzel", "Code");
        $this->addSystemTranslation("re-title", "Titel", "Title");
        $this->addSystemTranslation("re-text", "Bericht", "Report");
        $this->addSystemTranslation("re-aut", "Autor", "Author");
        $this->addSystemTranslation("re-pub", "Publikationsdatum",
                "Publication date");
        $this->addSystemTranslation("ex-obs", "Ablaufdatum", "Obsolescence date");
        
        // User translations
        $this->addUserTranslation("zzz-art",
                "Text/html für art.php Teil Kunstwerke (Deutsch)",
                "Text/html for art.php Part artworks (English)");
        $this->addUserTranslation("zzz-art-gallery",
                "Text/html für art.php Teil Bildergalerien (Deutsch)",
                "Text/html for art.php Part image galleries (English)");
        
        $bio_de = '<h1>Biographie von Cathrin Schulz-Mirbach</h1>
        <ul>
        <li>In Lugano(Schweiz)1964 geboren als Kind eines Physikers und einer Linguistin</li>
        <li>Vorschuljahre in Ispra und Varese (Italien), Mol (Belgien), Patras (Griechenland)</li>
        <li>Grundschulbesuch in Brüssel, Gymnasialabschluss in Athen (Abitur), während dieser Zeit autodidaktisch künstlerisch tätig in der Malerei und Plastik</li>
        <li>Physik-Studium an der Friedrich-Alexander-Universität Erlangen-Nürnberg (Vordiplom)</li>
        <li>Hauptstudium der Physik an der Universität Hamburg (Diplom)</li>
        <li>Heirat mit Dr.-Ing. Hanns Schulz-Mirbach</li>
        <li>Wissenschaftliche Mitarbeiterin an der Technischen Universität Hamburg-Harburg am Institut für Hochfrequenztechnik (Dissertation)</li>
        <li>Tätigkeit in einem Hamburger Software Unternehmen (1996 bis 2003)</li>
        <li>Wohnortwechsel nach Baden (Schweiz)von 1997 bis 2000, bedingt durch den Beruf des Ehemannes</li>
        <li>Zwei Kinder geboren in 1998 und 2003, nach der Geburt des zweiten Kindes ausschließlich künstlerisch tätig</li>
        <li>Seit 2017 Schülerin von Alexander Ignatkov in Lübeck im Fach Ölmalerei</li>
        </ul>';
        
        $bio_en = '<h1>Biography of Cathrin Schulz-Mirbach</h1>
        <ul>
        <li>Born in 1964 in Lugano (Switzerland), her father is a physicist, her mother studied linguistics</li>
        <li>Pre-school childhood in Ispra an Varese (Italy), Mol (Belgium), Patras (Greece)</li>
        <li>Primary school in Brussels, grammar school finished in Athens with the diploma from German secondary school qualifying for university admission, during this time self-taught creation of paintings and sculpture in clay </li>
        <li>University studies of physics at Friedrich-Alexander-University Erlangen-Nüernberg (intermediate diploma)</li>
        <li>University studies of physics at Hamburg University, degreed physicist</li>
        <li>Marriage to Dr.-Ing. Hanns Schulz-Mirbach</li>
        <li>Research assistent at the Institute of High-Frequency Technology at  Technical University Hamburg-Harburg (PhD in Electrical Engineering)</li>
        <li>Employee in a software company in Hamburg from 1996 to 2003</li>
        <li>Relocation to Baden (Switzerland) due to her husbands work from 1997 to 2000</li>
        <li>Birth of two children in 1998 and 2003, work as an artist</li>
        <li>Since 2017 learning oil painting technique with artist Alexander Ignatkov</li>
        </ul>';
        
        $this->addUserTranslation("zzz-bio", $bio_de, $bio_en);
        $this->addUserTranslation("zzz-exhibition",
                "Text/html für art.exhibition (Deutsch)",
                "Text/html for art.exhibition (English)");
        $this->addUserTranslation("zzz-index",
                "Text/html für art.index (Deutsch)",
                "Text/html for art.index (English)");
        $this->addUserTranslation("zzz-news", "Text/html für news.php (Deutsch)",
                "Text/html for news.php (English)");
    }

    public function getDefaultSystemTranslations (): array
    {
        return $this->systemTranslations;
    }

    public function getDefaultUserTranslations (): array
    {
        return $this->userTranslations;
    }

    public function getDefaultSystemTranslation (string $code): Translation
    {
        if (isset($this->systemTranslations[$code])) {
            $defaultTranslation = $this->systemTranslations[$code];
        } else {
            $defaultTranslation = new Translation();
        }
        
        return $defaultTranslation;
    }

    public function getDefaultUserTranslation (string $code): Translation
    {
        if (isset($this->userTranslations[$code])) {
            $defaultTranslation = $this->userTranslations[$code];
        } else {
            $defaultTranslation = new Translation();
        }
        
        return $defaultTranslation;
    }

    private function addSystemTranslation (string $key, string $text_de,
            string $text_en): void
    {
        $translation = new Translation();
        $translation->getType()->setIsSystemText();
        $translation->setCode($key);
        $translation->setTranslation_de($text_de);
        $translation->setTranslation_en($text_en);
        
        ($this->systemTranslations)[] = $translation;
    }

    private function addUserTranslation (string $key, string $text_de,
            string $text_en): void
    {
        $translation = new Translation();
        $translation->getType()->setIsEndUserText();
        $translation->setCode($key);
        $translation->setTranslation_de($text_de);
        $translation->setTranslation_en($text_en);
        
        ($this->userTranslations)[] = $translation;
    }
}

?>