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

require_once ("./util/session_manager.php");
require_once ("./util/language.php");
require_once ("./util/user_role.php");
require_once ("./util/formatter.php");
require_once ("./util/file_manager.php");
require_once ("./controller/translation_controller.php");
require_once ("./controller/photo_controller.php");
require_once ("./controller/access_controller.php");
require_once ("./model/photo.php");

session_start();
$sessionManager = new SessionManager();

$accessController = new AccessController($sessionManager->getUserRole());

if (! $accessController->access_PhotoMasterData()) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
    exit();
}

$language = $sessionManager->getUserLanguage();
$tc = new TranslationController($language);
$tc->setUserRole($sessionManager->getUserRole());

$formatter = new Formatter();

$uploadDate = DateTime::createFromFormat('d.m.Y', trim($_POST["uploaddate"]));
$filename = trim($_POST["filename"]);
$artworkId = intval($_POST["artwork"]);
$photoId = intval($_GET["id"]);

$photoController = new PhotoController();
$photo = $photoController->getPhotoById(intval($_GET["id"]));
$photoController->setPhoto($photo);

$fileManager = new FileManager("fileforupload");

if ($fileManager->fileIsImage()) {
    $fileManager->deleteFile($photo->getFilename());
    $fileMoveSuccessful = $fileManager->moveToImageDir($filename);
} else {
    $fileMoveSuccessful = true;
}

$photo->getArtwork()->setId($artworkId);
$photo->setFilename($filename);
$photo->setUploadDate($uploadDate);

$affectedRows = $photoController->updatePhoto();

$updateUrl = "photo_update.php?id=" . $photo->getId();

?>
<!doctype html>
<html lang="de">
<head>
<?php require_once ("./template/head.php"); ?>

</head>
<body>
	<?php require_once ("./template/header.php"); ?>
	<?php require_once ("./template/navigation.php"); ?>
	<div id="workarea">
		<main class="central-display-area">
		<article>
			<h1>Ergebnis Änderung Foto</h1>
<?php
if ((($affectedRows == 0) || ($affectedRows == 1)) && $fileMoveSuccessful) {
    echo ('Die Änderung des Fotos war erfolgreich. Es wurden die folgenden Daten übernommen: ');
    echo ("<br />");
    echo ("<br />");
    echo ("$photo");
} else {
    echo ('Die Änderung des Fotos hat nicht funktioniert. Bitte überprüfen Sie die Daten und wiederholen Sie ggf. die   ');
    echo ('<a href="' . $updateUrl . '">Änderung des Fotos</a>');
}
echo ("<br /> <br />");
echo ('<a href="admin.php">Administrationsfunktionen</a>');
?>
		</article>
		</main>
	</div>
	<?php include ("./template/footer.php"); ?>
	
</body>
</html>