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
require_once ("./util/translation_type.php");
require_once ("./controller/translation_controller.php");
require_once ("./controller/gallery_controller.php");
require_once ("./controller/access_controller.php");
require_once ("./model/gallery.php");
require_once ("./model/gallery_photo.php");

session_start();
$sessionManager = new SessionManager();

$accessController = new AccessController($sessionManager->getUserRole());

if (! $accessController->access_GalleryMasterData()) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
    exit();
}

$language = $sessionManager->getUserLanguage();
$tc = new TranslationController($language);
$tc->setUserRole($sessionManager->getUserRole());

$formatter = new Formatter();

$code = trim($_POST["code"]);
$title_de = trim($_POST["title_de"]);
$title_en = trim($_POST["title_en"]);
$description_de = trim($_POST["description_de"]);
$description_en = trim($_POST["description_en"]);

$galleryController = new GalleryController();
$gallery = $galleryController->getGallery();

$gallery->getTitle()->setCode($code);
$gallery->getTitle()
    ->getType()
    ->setIsEndUserText();
$gallery->getTitle()->setTranslation_de($title_de);
$gallery->getTitle()->setTranslation_en($title_en);

$gallery->getDescription()->setCode($code . "_description");
$gallery->getDescription()
    ->getType()
    ->setIsEndUserText();
$gallery->getDescription()->setTranslation_de($description_de);
$gallery->getDescription()->setTranslation_en($description_en);

$numOfPhotos = 0;
for ($i = 0; $i < 20; $i ++) {
    $name = "photo" . ($i + 1);
    $index = "index" . ($i + 1);
    
    $photoId = intval($_POST[$name]);
    $indexValue = intval($_POST[$index]);
    
    if ($photoId != - 1) {
        $galleryPhoto = new GalleryPhoto();
        $photo = new Photo();
        $photo->setId($photoId);
        $galleryPhoto->setPhoto($photo);
        $galleryPhoto->setIndexOfPhoto($indexValue);
        $gallery->addGalleryPhoto($galleryPhoto);
        $numOfPhotos += 1;
    }
}

if ($numOfPhotos > 0) {
    $affectedRows = $galleryController->insertGalleryAndPhotos();
} else {
    $affectedRows = 0;
}

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
			<h1>Ergebnis Neuanlage Galerie</h1>
<?php
if ($affectedRows >= 1) {
    echo ('Die Neuanlage der Galerie war erfolgreich. Es wurden die folgenden Daten übernommen: ');
    echo ("<br />");
    echo ("<br />");
    echo ("$gallery");
}elseif ($numOfPhotos == 0){
    echo ('Der Galerie sind keine Bilder zugeordnet. Deswegen wurden die Daten nicht übernommen. Bitte überprüfen Sie die Daten und wiederholen Sie ggf. die ');
    echo ('<a href="gallery_create.php">Neuanlage der Galerie</a>');
}
else {
    echo ('Die Neuanlage der Galerie hat nicht funktioniert. Bitte überprüfen Sie die Daten und wiederholen Sie ggf. die   ');
    echo ('<a href="gallery_create.php">Neuanlage der Galerie</a>');
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