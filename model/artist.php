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
class Artist
{

    private $id;

    private $firstname;

    private $lastname;

    private $birthdate;

    private $mail;

    private $telephone;

    private $cellphone;

    private $address;

    function __construct ()
    {
        $this->id = - 1;
        $this->firstname = "";
        $this->lastname = "";
        $this->birthdate = new DateTime();
        $this->mail = "";
        $this->telephone = "";
        $this->cellphone = "";
        $this->address = "";
    }

    public function __toString (): string
    {
        $birthdate = $this->birthdate->format("m.d.Y");
        $artistDataAsHTMLTable = '<table><tr><th>Attribut</th><th>Wert</th></tr><tr><td>Id:</td><td>' .
                 "$this->id" . '</td></tr><tr><td>Vorname:</td><td>' .
                 "$this->firstname" . '</td></tr><tr><td>Nachname:</td><td>' .
                 "$this->lastname" . '</td></tr><tr><td>Geburtsdatum:</td><td>' .
                 "$birthdate" . '</td></tr><tr><td>E-mail:</td><td>' .
                 "$this->mail" . '</td></tr><tr><td>Festnetznummer:</td><td>' .
                 "$this->telephone" . '</td></tr><tr><td>Mobilnummer:</td><td>' .
                 "$this->cellphone" . '</td></tr><tr><td>Adresse:</td><td>' .
                 "$this->address" . '</td></tr></table>';
        
        return $artistDataAsHTMLTable;
    }

    public function isArtistValid (): bool
    {
        return ($this->lastnameIsValid() && $this->mailIsValid() &&
                 $this->firstnameIsValid() && $this->birthdateIsValid() &&
                 $this->telephoneIsValid() && $this->cellphoneIsValid() &&
                 $this->addressIsValid());
    }

    public function getId (): int
    {
        return $this->id;
    }

    public function getFirstname (): string
    {
        return $this->firstname;
    }

    public function getLastname (): string
    {
        return $this->lastname;
    }

    public function getBirthdate (): DateTime
    {
        return $this->birthdate;
    }

    public function getMail (): string
    {
        return $this->mail;
    }

    public function getTelephone (): string
    {
        return $this->telephone;
    }

    public function getCellphone (): string
    {
        return $this->cellphone;
    }

    public function getAddress (): string
    {
        return $this->address;
    }

    public function setId (int $id): void
    {
        $this->id = $id;
    }

    public function setFirstname (string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function setLastname (string $lastname): void
    {
        $this->lastname = $lastname;
    }

    public function setBirthdate (DateTime $birthdate): void
    {
        $this->birthdate = $birthdate;
    }

    public function setMail (string $mail): void
    {
        $this->mail = $mail;
    }

    public function setTelephone (string $telephone): void
    {
        $this->telephone = $telephone;
    }

    public function setCellphone (string $cellphone): void
    {
        $this->cellphone = $cellphone;
    }

    public function setAddress (string $address): void
    {
        $this->address = $address;
    }

    private function firstnameIsValid (): bool
    {
        return (isset($this->firstname) && is_string($this->firstname));
    }

    private function lastnameIsValid (): bool
    {
        return (isset($this->lastname) && is_string($this->lastname));
    }

    private function birthdateIsValid (): bool
    {
        return (isset($this->birthdate) && is_object($this->birthdate));
    }

    private function mailIsValid (): bool
    {
        return (isset($this->mail) && is_string($this->mail));
    }

    private function telephoneIsValid (): bool
    {
        return (isset($this->telephone) && is_string($this->telephone));
    }

    private function cellphoneIsValid (): bool
    {
        return (isset($this->cellphone) && is_string($this->cellphone));
    }

    private function addressIsValid (): bool
    {
        return (isset($this->address) && is_string($this->address));
    }
}
?>
