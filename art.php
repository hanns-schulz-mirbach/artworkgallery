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
require_once ("./util/formatter.php");
require_once ("./controller/translation_controller.php");
require_once ("./controller/photo_controller.php");
require_once ("./controller/gallery_controller.php");
require_once ("./controller/artwork_controller.php");

session_start();
$sessionManager = new SessionManager();
$language = $sessionManager->getUserLanguage();
$tc = new TranslationController($language);
$tc->setUserRole($sessionManager->getUserRole());

$formatter = new Formatter();

$photoController = new PhotoController();
$threePhotoSetGui = $photoController->getGuiForThreeDifferentPhotos($tc,
        $formatter);

$galleryController = new GalleryController();
$allGalleriesGui = $galleryController->getAllGalleriesGui($tc, $formatter);

$artworkController = new ArtworkController(); 
$allArtworksGui = $artworkController->getAllArtworksGui($tc, $formatter);


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
			<?php
echo ("<h1>" . $tc->getText("art-headline") . "</h1>");
echo ($tc->getEndUserTextforCurrentLanguageByCode("zzz-art") . "<br/><br/>");
echo ('<div class="image-set">');
echo ($allArtworksGui);
echo ('</div> <br/><br/>');
echo ("<h1>" . $tc->getText("art-galleries-headline") . "</h1>");
echo ($tc->getEndUserTextforCurrentLanguageByCode("zzz-art-gallery") . "<br/><br/>");
echo ($allGalleriesGui);
?>
						 <div class="image-set">
					<?php echo ($threePhotoSetGui);?>
					</div>
		</article>
		</main>
	</div>
	<?php include ("./template/footer.php"); ?>
	
	<script type="text/javascript" src="script/index.js"></script>
</body>
</html>