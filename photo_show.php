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
require_once ("./model/photo.php");
require_once ("./model/artwork.php");
require_once ("./model/artist.php");

session_start();
$sessionManager = new SessionManager();

$language = $sessionManager->getUserLanguage();
$tc = new TranslationController($language);
$tc->setUserRole($sessionManager->getUserRole());

$formatter = new Formatter();

$photoId = intval($_GET["id"]);
$photoController = new PhotoController();

$photo = $photoController->getPhotoById($photoId);

$caption = $tc->getCaptionForPhoto($photo);

$pathToExistingFile = "./image/" . $photo->getFilename();

$photoGui = $formatter->getPhotoGui($photo, $caption);

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
<?php echo ($photoGui);?>
		</article>
		</main>
	</div>
	<?php include ("./template/footer.php"); ?>
	
</body>
</html>