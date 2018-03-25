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
require_once ("./controller/access_controller.php");
require_once ("./controller/translation_controller.php");
require_once ("./controller/photo_controller.php");
require_once ("./controller/gallery_controller.php");
require_once ("./model/gallery.php");

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

$galleryId = intval($_GET["id"]);

$galleryController = new GalleryController();
$gallery = $galleryController->getGalleryById($galleryId);
$galleryPhotos = $gallery->getGalleryPhotos();

$photoController = new PhotoController();
$allPhotos = $photoController->getAllPhotos();

$photosSelectionBody = $formatter->getPhotosSelectionBody($allPhotos);

$photoSelectionGUI = "";

$i = 0;

foreach ($galleryPhotos as $galleryPhoto) {
    $photoId = $galleryPhoto->getPhoto()->getId();
    $name = "photo" . ($i + 1);
    $index = "index" . ($i + 1);
    $title = "Foto " . ($i + 1) . ", Index " . ($i + 1);
    $showPhotoUrl = '<a href="photo_show.php?id=' . $photoId . '">' . $title .
             '</a>';
    $photosSelectionBody = $formatter->getPhotosSelectionBody($allPhotos,
            $galleryPhoto->getPhoto()
                ->getId());
    $photoSelectionGUI .= '<tr><td><label for="' . $name . '">' . $showPhotoUrl .
             ':</label></td>' . '<td> <select name="' . $name . '">' .
             $photosSelectionBody . '</select> <input type="number" name="' .
             $index . '" min="0" value="' . $galleryPhoto->getIndexOfPhoto() .
             '"></td></tr>';
    $i ++;
}

$photosSelectionBody = $formatter->getPhotosSelectionBody($allPhotos);

for ($i = $gallery->getNumberOfGalleryPhotos(); $i < 20; $i ++) {
    $name = "photo" . ($i + 1);
    $index = "index" . ($i + 1);
    $title = "Foto " . ($i + 1) . ", Index " . ($i + 1);
    $photoSelectionGUI .= '<tr><td><label for="' . $name . '">' . $title .
             ':</label></td>' . '<td> <select name="' . $name . '">' .
             $photosSelectionBody . '</select> <input type="number" name="' .
             $index . '" min="0"></td></tr>';
}

$updateUrl = "gallery_update_confirmation.php?id=" . $gallery->getId();
$deleteUrl = "gallery_delete.php?id=" . $gallery->getId();

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
			<h1>Änderung Bildergalerie</h1>
			<form action='<?php echo ("$updateUrl")?>' method="post">
				<table>
					<tr>
						<th>Attribut</th>
						<th>Wert</th>
					</tr>
					<tr>
						<td><label for="code">Kurzbezeichnung Galerie:</label></td>
						<td><input type="text" class="code" name="code" id="code"
							value="<?php echo ($gallery->getTitle()->getCode());?>" required />
					
					</tr>
					<tr>
						<td><label for="title_de">Galerietitel Deutsch:</label></td>
						<td><input type="text" class="title_de" name="title_de"
							id="title_de"
							value="<?php echo ($gallery->getTitle()->getDecodedTranslation_de());?>"
							required />
					
					</tr>
					<tr>
						<td><label for="title_en">Galerietitel Englisch:</label></td>
						<td><input type="text" class="title_en" name="title_en"
							id="title_en"
							value="<?php echo ($gallery->getTitle()->getDecodedTranslation_en());?>"
							required /></td>
					</tr>
					<tr>
						<td><label for="description_de">Beschreibung (Deutsch):</label></td>
						<td><textarea name="description_de" rows="5" cols="31"
								maxlength="1000" required><?php echo ($gallery->getDescription()->getDecodedTranslation_de()); ?></textarea>
						</td>

					</tr>
					<tr>
						<td><label for="description_en">Beschreibung (Englisch):</label></td>
						<td><textarea name="description_en" rows="5" cols="31"
								maxlength="1000" required><?php echo ($gallery->getDescription()->getDecodedTranslation_en()); ?></textarea>
						</td>
					</tr>
					<?php echo ($photoSelectionGUI);?>

				</table>

			<?php
echo ($formatter->getSubmitResetControl());
echo ("<br /> <br />");
echo ('<a href="' . $deleteUrl . '">Datensatz löschen</a>');
echo ("<br /> <br />");
echo ('<a href="admin.php">Administrationsfunktionen</a>');
?>
		</form>
		</article>
		</main>
	</div>
	<?php include ("./template/footer.php"); ?>
	
</body>
</html>