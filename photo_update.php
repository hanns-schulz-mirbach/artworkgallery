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
require_once ("./controller/artwork_controller.php");
require_once ("./controller/photo_controller.php");
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

$photoId = intval($_GET["id"]);
$photoController = new PhotoController();

$photo = $photoController->getPhotoById($photoId);

$artworkController = new ArtworkController();
$artworks = $artworkController->getAllArtworks();
$artworkSelection = $formatter->getArtworksForSelection($artworks, "artwork",
        $photo->getArtwork()
            ->getId());

$uploadDate = $photo->getUploadDate()->format("d.m.Y");

$pathToExistingFile = "./image/" . $photo->getFilename();

$updateUrl = "photo_update_confirmation.php?id=" . "$photoId";
$deleteUrl = "photo_delete.php?id=" . "$photoId";

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
			<h1>Änderung Foto</h1>
			<form action="<?php echo ($updateUrl); ?>" method="post"
				enctype="multipart/form-data">
				<table>
					<tr>
						<th>Attribut</th>
						<th>Wert</th>
					</tr>
					<tr>
						<td><label for="filename">Filename:</label></td>
						<td><input type="text" name="filename" id="filename"
							value="<?php echo ($photo->getFilename()); ?>" required></td>
					</tr>
					<tr>
						<td><label for="uploaddate">Speicherdatum:</label></td>
						<td><input type="text" name="uploaddate" id="uploaddate"
							placeholder="TT.MM.JJJJ" required
							pattern="^(31|30|0[1-9]|[12][0-9]|[1-9])\.(0[1-9]|1[012]|[1-9])\.((18|19|20)\d{2}|\d{2})$"
							value="<?php echo ($uploadDate); ?>" /></td>
					</tr>
					<tr>
						<td><label for="artwork">Kunstwerk:</label></td>
						<td><?php echo ("$artworkSelection");?></td>
					</tr>
				</table>
				<br /> <br />
				<figure>
					<img alt="Vorhandenes Foto"
						src="<?php echo ($pathToExistingFile . "?hash=" . filemtime($pathToExistingFile));?>">
					<figcaption>Vorhandenes Foto</figcaption>
				</figure>
				<br /> <br /> <label for="fileforupload">Fotodatei ersetzen durch:</label><br />
				<input type="file" name="fileforupload">
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