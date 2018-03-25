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

$photoController = new PhotoController();
$photo = $photoController->getOtherPhotoById(intval($_GET["id"]));
$photoController->setPhoto($photo);

$fileManager = new FileManager("none");
$fileDeleteSuccessful = $fileManager->deleteFile($photo->getFilename());

$affectedRows = $photoController->deletePhoto();
$updateUrl = "photo_other_update.php?id=" . $photoController->getPhoto()->getId();

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
			<h1>Ergebnis Löschung Foto ohne Bezug zu einem Kunstwerk</h1>
<?php
if (($affectedRows == 1) && $fileDeleteSuccessful) {
    echo ('Die Löschung des Fotos war erfolgreich.');
} else {
    echo ('Die Löschung des Fotos hat nicht funktioniert. Bitte überprüfen Sie die Daten und wiederholen Sie ggf. die   ');
    echo ('<a href="' . $updateUrl . '">Löschung des Fotos</a>');
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