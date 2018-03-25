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
require_once ("./controller/exhibition_controller.php");
require_once ("./controller/access_controller.php");
require_once ("./model/exhibition.php");
require_once ("./model/translation.php");

session_start();
$sessionManager = new SessionManager();

$accessController = new AccessController($sessionManager->getUserRole());

if (! $accessController->access_ExhibitionMasterData()) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
    exit();
}

$language = $sessionManager->getUserLanguage();
$tc = new TranslationController($language);
$tc->setUserRole($sessionManager->getUserRole());

$formatter = new Formatter();

$startDate = DateTime::createFromFormat('d.m.Y', trim($_POST["startdate"]));
$endDate = DateTime::createFromFormat('d.m.Y', trim($_POST["enddate"]));
$openingHours = trim($_POST["openinghours"]);
$address = trim($_POST["address"]);
$code = trim($_POST["code"]);
$title_de = trim($_POST["title_de"]);
$title_en = trim($_POST["title_en"]);
$description_de = trim($_POST["description_de"]);
$description_en = trim($_POST["description_en"]);


$exhibitionController = new ExhibitionController();
$exhibition = $exhibitionController->getExhibition();

$exhibition->setStart($startDate);
$exhibition->setEnd($endDate);
$exhibition->setOpeningHours($openingHours);
$exhibition->setAddress($address);

$title = $exhibition->getTitle();
$title->setCode($code);
$title->getType()->setIsEndUserText();
$title->setTranslation_de($title_de);
$title->setTranslation_en($title_en);

$description = $exhibition->getDescription();
$description->setCode($code . "_description");
$description->getType()->setIsEndUserText();
$description->setTranslation_de($description_de);
$description->setTranslation_en($description_en);

$affectedRows = $exhibitionController->insertExhibition();

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
			<h1>Ergebnis Neuanlage Ausstellung</h1>
<?php
if ($affectedRows == 1) {
    echo ('Die Neuanlage der Ausstellung war erfolgreich. Es wurden die folgenden Daten übernommen: ');
    echo ("<br />");
    echo ("<br />");
    echo ("$exhibition");
} else {
    echo ('Die Neuanlage der Ausstellung hat nicht funktioniert. Bitte überprüfen Sie die Daten und wiederholen Sie ggf. die   ');
    echo ('<a href="exhibition_create.php">Neuanlage der Ausstellung</a>');
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