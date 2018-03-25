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
require_once ("./controller/artist_controller.php");
require_once ("./controller/material_controller.php");
require_once ("./controller/technique_controller.php");
require_once ("./controller/availability_controller.php");
require_once ("./controller/artwork_controller.php");
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

$artworkId = intval($_GET["id"]);
$artworkController = new ArtworkController();

$artwork = $artworkController->getArtworkById($artworkId);

$artistController = new ArtistController();
$artists = $artistController->getAllArtists();
$artistSelection = $formatter->getArtistsForSelection($artists, "artist",
        $artwork->getArtist()
            ->getId());

$materialController = new MaterialController();
$materials = $materialController->getAllMaterials();
$materialSelection = $formatter->getMaterialsForSelection($materials, "material",
        $artwork->getMaterial()
            ->getId());

$techniqueController = new TechniqueController();
$techniques = $techniqueController->getAllTechniques();
$techniqueSelection = $formatter->getTechniquesForSelection($techniques,
        "technique", $artwork->getTechnique()
            ->getId());

$availabilityController = new AvailabilityController();
$availabilities = $availabilityController->getAllAvailabilities();
$availabilitySelection = $formatter->getAvailabilitiesForSelection(
        $availabilities, "availability",
        $artwork->getAvailability()
            ->getId());

$updateUrl = "artwork_update_confirmation.php?id=" . "$artworkId";
$deleteUrl = "artwork_delete.php?id=" . "$artworkId";
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
			<h1>Änderung Kunstwerk</h1>
			<form action="<?php echo ("$updateUrl");?>" method="post">
				<table>
					<tr>
						<th>Attribut</th>
						<th>Wert</th>
					</tr>
					<tr>
						<td><label for="title_code">Titelkürzel:</label></td>
						<td><input type="text" name="title_code" id="title_code"
							value="<?php echo ($artwork->getTitle()->getCode());?>" required />
					
					</tr>
					<tr>
						<td><label for="title_de">Titel (Deutsch):</label></td>
						<td><input type="text" name="title_de" id="title_de"
							value="<?php echo ($artwork->getTitle()->getDecodedTranslation_de());?>"
							required />
					
					</tr>
					<tr>
						<td><label for="title_en">Titel (Englisch):</label></td>
						<td><input type="text" name="title_en" id="title_en"
							value="<?php echo ($artwork->getTitle()->getDecodedTranslation_en());?>"
							required />
					
					</tr>
					<tr>
						<td><label for="artist">Künstler:</label></td>
						<td><?php echo ("$artistSelection");?></td>
					</tr>
					<tr>
						<td><label for="technique">Technik:</label></td>
						<td><?php echo ("$techniqueSelection");?></td>
					</tr>
					<tr>
						<td><label for="material">Material:</label></td>
						<td><?php echo ("$materialSelection");?></td>
					</tr>
					<tr>
						<td><label for="availability">Verfügbarkeit:</label></td>
						<td><?php echo ("$availabilitySelection");?></td>
					</tr>
					<tr>
						<td><label for="signature_name">Signatur:</label></td>
						<td><input type="text" name="signature_name" id="signature_name"
							value="<?php echo ($artwork->getSignatureName());?>" required></td>
					</tr>
					<tr>
						<td><label for="signature_date">Signaturdatum:</label></td>
						<td><input type="text" name="signature_date" id="signature_date"
							placeholder="TT.MM.JJJJ" required
							pattern="^(31|30|0[1-9]|[12][0-9]|[1-9])\.(0[1-9]|1[012]|[1-9])\.((18|19|20)\d{2}|\d{2})$"
							value="<?php echo ($artwork->getSignatureDate()->format('d.m.Y'));?>" /></td>
					</tr>
					<tr>
						<td><label for="location_code">Lagerortkürzel:</label></td>
						<td><input type="text" name="location_code" id="location_code"
							value="<?php echo ($artwork->getLocation()->getCode());?>"
							required />
					
					</tr>
					<tr>
						<td><label for="location_de">Lagerort (Deutsch):</label></td>
						<td><input type="text" name="location_de" id="location_de"
							value="<?php echo ($artwork->getLocation()->getDecodedTranslation_de());?>"
							required />
					
					</tr>
					<tr>
						<td><label for="location_en">Lagerort (Englisch):</label></td>
						<td><input type="text" name="location_en" id="location_en"
							value="<?php echo ($artwork->getLocation()->getDecodedTranslation_en());?>"
							required />
					
					</tr>
					<tr>
						<td><label for="width">Breite (in mm):</label></td>
						<td><input type="number" name="width" id="width" min="0"
							value="<?php echo ($artwork->getWidth());?>" required></td>
					</tr>
					<tr>
						<td><label for="height">Höhe (in mm):</label></td>
						<td><input type="number" name="height" id="height" min="0"
							value="<?php echo ($artwork->getHeight());?>" required /></td>
					</tr>
					<tr>
						<td><label for="depth">Tiefe (in mm):</label></td>
						<td><input type="number" name="depth" id="depth" min="0"
							value="<?php echo ($artwork->getDepth());?>" required></td>
					</tr>
					<tr>
						<td><label for="weight">Gewicht (in g):</label></td>
						<td><input type="number" name="weight" id="weight" min="0"
							value="<?php echo ($artwork->getWeight());?>" required></td>
					</tr>
					<tr>
						<td><label for="price">Preis (in €):</label></td>
						<td><input type="number" name="price" id="price" min="-1"
							value="<?php echo ($artwork->getPrice());?>" required></td>
					</tr>
				</table>
				<br /> 
				<textarea name="explanation_de" rows="20" cols="100"
					maxlength="10000" required><?php echo ($artwork->getExplanation()->getDecodedTranslation_de()); ?></textarea>
				<br /> <br />
				<textarea name="explanation_en" rows="20" cols="100"
					maxlength="10000" required><?php echo ($artwork->getExplanation()->getDecodedTranslation_en()); ?></textarea>
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