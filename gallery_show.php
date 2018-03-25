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
require_once ("./controller/access_controller.php");
require_once ("./controller/translation_controller.php");
require_once ("./controller/photo_controller.php");
require_once ("./controller/gallery_controller.php");
require_once ("./model/photo.php");
require_once ("./model/gallery.php");

session_start();
$sessionManager = new SessionManager();

$accessController = new AccessController($sessionManager->getUserRole());

$language = $sessionManager->getUserLanguage();
$tc = new TranslationController($language);
$tc->setUserRole($sessionManager->getUserRole());

$formatter = new Formatter();

$galleryId = intval($_GET["id"]);
$galleryController = new GalleryController();
$gallery = $galleryController->getGalleryById($galleryId);

$gallery->setLanguage($tc->getLanguage());
$galleryController->setGallery($gallery);
$galleryGui = $galleryController->getGalleryGui($tc, $formatter);

$headline = $tc->getText("ga-headline") . " " . $gallery->getTitle()->getTextForLanguage();

?>
<!doctype html>
<html lang="de">
<head>
<link href="./style/lightbox.css" rel="stylesheet">
<?php require_once ("./template/head.php"); ?>

</head>
<body>
	<?php require_once ("./template/header.php"); ?>
	<?php require_once ("./template/navigation.php"); ?>
<div id="workarea">
		<main class="central-display-area">
		<article>

			<h1><?php echo ($headline);?></h1>
		<?php echo ($gallery->getDescription()->getTextForLanguage() . "<br /><br />" . $tc->getText("ga-play-hint") . "<br /><br />");?>
			<?php echo ($galleryGui); ?>
		</article>
		</main>
	</div>
	<?php include ("./template/footer.php"); ?>
	<script type="text/javascript" src="script/lightbox-plus-jquery.js"></script>
	<script type="text/javascript" src="script/lightbox-options.js"></script>
</body>
</html>