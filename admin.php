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
require_once ("./controller/translation_controller.php");
require_once ("./controller/access_controller.php");
require_once ("./controller/report_controller.php");
require_once ("./model/translation.php");

session_start();
$sessionManager = new SessionManager();

$accessController = new AccessController($sessionManager->getUserRole());

$language = $sessionManager->getUserLanguage();
$tc = new TranslationController($language);
$tc->setUserRole($sessionManager->getUserRole());

$reportController = new ReportController();
$reportController->deleteObsoleteReports();

$zzzArt = $tc->getTranslationByCode("zzz-art");
$zzzGallery = $tc->getTranslationByCode("zzz-art-gallery");
$zzzBio = $tc->getTranslationByCode("zzz-bio");
$zzzExhibition = $tc->getTranslationByCode("zzz-exhibition");
$zzzIndex = $tc->getTranslationByCode("zzz-index");
$zzzNews = $tc->getTranslationByCode("zzz-news");

$zzzArtUrl = "translation_update.php?id=" . $zzzArt->getId();
$zzzGalleryUrl = "translation_update.php?id=" . $zzzGallery->getId();
$zzzBioUrl = "translation_update.php?id=" . $zzzBio->getId();
$zzzExhibitionUrl = "translation_update.php?id=" . $zzzExhibition->getId();
$zzzIndexUrl = "translation_update.php?id=" . $zzzIndex->getId();
$zzzNewsUrl = "translation_update.php?id=" . $zzzNews->getId();

?>
<!doctype html>
<html lang="de">
<head>
<?php

require_once ("./template/head.php");
if (! $accessController->access_AdminFunctions()) {
    echo ('<meta http-equiv="Refresh" content="0; url=login.php">');
}

?>

</head>
<body>
	<?php require_once ("./template/header.php"); ?>
	<?php require_once ("./template/navigation.php"); ?>
	<div id="workarea">
		<main class="central-display-area">
		<article>
			<h1>Administrationsfunktionen</h1>

			Stammdaten:
			<ul>
				<li><a href="technique_overview.php">Arbeitstechniken</a></li>
				<li><a href="exhibition_overview.php">Ausstellungen</a></li>
				<li><a href="report_overview.php">Berichte</a></li>
				<li><a href="gallery_overview.php">Bildergalerien</a></li>
				<li><a href="artist_overview.php">Künstler</a></li>
				<li><a href="artwork_overview.php">Kunstwerke</a></li>
				<li><a href="material_overview.php">Materialien der Kunstwerke</a></li>
				<li><a href="photo_overview.php">Fotos der Kunstwerke</a></li>
				<li><a href="photo_other_overview.php">Fotos ohne Bezug zu einem
						Kunstwerk</a></li>
				<li><a href="availability_overview.php">Verfügbarkeiten der
						Kunstwerke</a></li>
			</ul>

			Übersetzungen:
			<ul>
				<li><a href="translation_overview.php?type=2">Übersetzung
						Endanwendertexte</a></li>
				<li><a href="translation_overview.php?type=1">Übersetzungstexte für
						Systemfunktionen</a></li>
			</ul>

			Übersetzungen neu erzeugen (Achtung: dies löscht die vorhandenen
			Datenbankeinträge):
			<ul>
				<li><a href="translation_generate.php?type=2">Endbenutzer
						Übersetzungstexte neu erzeugen</a></li>
				<li><a href="translation_generate.php?type=1">System
						Übersetzungstexte neu erzeugen</a></li>
			</ul>

			Benutzeroberflächen:
			<ul>
				<li><a href="<?php echo ($zzzIndexUrl);?>">Startseite</a></li>
				<li><a href="<?php echo ($zzzArtUrl);?>">Kunst</a></li>
				<li><a href="<?php echo ($zzzGalleryUrl);?>">Bildergalerien</a></li>
				<li><a href="<?php echo ($zzzExhibitionUrl);?>">Ausstellungen</a></li>
				<li><a href="<?php echo ($zzzBioUrl);?>">Biographie</a></li>
				<li><a href="<?php echo ($zzzNewsUrl);?>">Aktuelles</a></li>
			</ul>

			Systeminformation:
			<ul>
				<li><a href="php_info.php">PHP Info</a></li>
			</ul>
		</article>

		</main>
	</div>
	<?php include ("./template/footer.php"); ?>
	
	<script type="text/javascript" src="script/index.js"></script>
</body>
</html>