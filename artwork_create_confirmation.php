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
require_once ("./controller/artwork_controller.php");
require_once ("./controller/access_controller.php");
require_once ("./model/artwork.php");

session_start();
$sessionManager = new SessionManager();

$accessController = new AccessController($sessionManager->getUserRole());

if (! $accessController->access_ArtworkMasterData()) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
    exit();
}

$language = $sessionManager->getUserLanguage();
$tc = new TranslationController($language);
$tc->setUserRole($sessionManager->getUserRole());

$formatter = new Formatter();

$titleCode = trim($_POST["title_code"]);
$title_de = trim($_POST["title_de"]);
$title_en = trim($_POST["title_en"]);
$artistId = intval($_POST["artist"]);
$techniqueId = intval($_POST["technique"]);
$materialId = intval($_POST["material"]);
$avilabilityId = intval($_POST["availability"]);
$signatureName = trim($_POST["signature_name"]);
$signatureDate = DateTime::createFromFormat('d.m.Y',
        trim($_POST["signature_date"]));
$locationCode = trim($_POST["location_code"]);
$location_de = trim($_POST["location_de"]);
$location_en = trim($_POST["location_en"]);
$width = intval($_POST["width"]);
$height = intval($_POST["height"]);
$depth = intval($_POST["depth"]);
$weight = intval($_POST["weight"]);
$price = intval($_POST["price"]);
$explanation_de = trim($_POST["explanation_de"]);
$explanation_en = trim($_POST["explanation_en"]);
$explanationCode = $titleCode . "_explanation_code";

$artworkController = new ArtworkController();
$artwork = $artworkController->getArtwork();

$artwork->getTitle()->setCode($titleCode);
$artwork->getTitle()
    ->getType()
    ->setIsEndUserText();
$artwork->getTitle()->setTranslation_de($title_de);
$artwork->getTitle()->setTranslation_en($title_en);

$artwork->getExplanation()->setCode($explanationCode);
$artwork->getExplanation()
->getType()
->setIsEndUserText();
$artwork->getExplanation()->setTranslation_de($explanation_de);
$artwork->getExplanation()->setTranslation_en($explanation_en);

$artwork->getArtist()->setId($artistId);
$artwork->getTechnique()->setId($techniqueId);
$artwork->getMaterial()->setId($materialId);
$artwork->getAvailability()->setId($avilabilityId);

$artwork->setSignatureName($signatureName);
$artwork->setSignatureDate($signatureDate);

$artwork->getLocation()->setCode($locationCode);
$artwork->getLocation()
    ->getType()
    ->setIsEndUserText();
$artwork->getLocation()->setTranslation_de($location_de);
$artwork->getLocation()->setTranslation_en($location_en);

$artwork->setWidth($width);
$artwork->setHeight($height);
$artwork->setDepth($depth);
$artwork->setWeight($weight);
$artwork->setPrice($price);

$affectedRows = $artworkController->insertArtwork();

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
			<h1>Ergebnis Neuanlage Kunstwerk</h1>
<?php
if ($affectedRows >= 1) {
    echo ('Die Neuanlage des Kunstwerks war erfolgreich. Es wurden die folgenden Daten übernommen: ');
    echo ("<br />");
    echo ("<br />");
    echo ($artwork);
} else {
    echo ('Die Neuanlage des Kunstwerks hat nicht funktioniert. Bitte überprüfen Sie die Daten und wiederholen Sie ggf. die   ');
    echo ('<a href="artwork_create.php">Neuanlage des Kunstwerks</a>');
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