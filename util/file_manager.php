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
class FileManager
{

    private $fileHandle;

    private $fileDir;

    public function __construct (string $fileHandle)
    {
        $this->fileDir = "./image/";
        $this->fileHandle = $fileHandle;
        
        if (! empty($_FILES)) {
            if ($_FILES["$fileHandle"]['error'] === UPLOAD_ERR_OK) {
                $this->fileHandle = $fileHandle;
            } else {
                $this->fileHandle = "none";
            }
        } else {
            $this->fileHandle = "none";
        }
    }

    public function getFileHandle (): string
    {
        return $this->fileHandle;
    }

    public function setFileHandle (string $fileHandle)
    {
        $this->fileHandle = $fileHandle;
    }

    public function getFileDir (): string
    {
        return $this->fileDir;
    }

    public function setFileDir (string $fileDir)
    {
        $this->fileDir = $fileDir;
    }

    public function moveToImageDir (string $filename): bool
    {
        if ($this->fileIsImage()) {
            $destination = $this->fileDir . $filename;
            
            return move_uploaded_file($_FILES["$this->fileHandle"]['tmp_name'],
                    $destination);
        } else {
            return false;
        }
    }

    public function deleteFile ($filename): bool
    {
        $deletePath = $this->fileDir . $filename;
        return unlink($deletePath);
    }

    public function fileIsImage (): bool
    {
        if (! $this->fileHandleIsValid()) {
            return false;
        }
        
        $imagesize = getimagesize($_FILES["$this->fileHandle"]['tmp_name']);
        if ($imagesize) {
            return true;
        } else {
            return false;
        }
    }

    private function fileHandleIsValid (): bool
    {
        return ($this->fileHandle != "none");
    }
}

?>