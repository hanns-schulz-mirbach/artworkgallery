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
require_once ("./controller/exhibition_controller.php");
require_once ("./model/exhibition.php");

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

$exhibitionId = intval($_GET["id"]);

$exhibitionController = new ExhibitionController();

$exhibition = $exhibitionController->getExhibitionById($exhibitionId);

$updateUrl = "exhibition_update_confirmation.php?id=" . $exhibition->getId();
$deleteUrl = "exhibition_delete.php?id=" . $exhibition->getId();

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
			<h1>Änderung Ausstellung</h1>
			<form action='<?php echo ("$updateUrl")?>' method="post">
				<table>
					<tr>
						<th>Attribut</th>
						<th>Wert</th>
					</tr>
					<tr>
						<td><label for="code">Kurzbezeichnung Ausstellung:</label></td>
						<td><input type="text" class="code" name="code" id="code"
							value="<?php echo ($exhibition->getTitle()->getCode());?>"
							required />
					
					</tr>
					<tr>
						<td><label for="title_de">Ausstellungstitel Deutsch:</label></td>
						<td><input type="text" class="title_de" name="title_de"
							id="title_de"
							value="<?php echo ($exhibition->getTitle()->getDecodedTranslation_de());?>"
							required />
					
					</tr>
					<tr>
						<td><label for="title_en">Ausstellungstitel Englisch:</label></td>
						<td><input type="text" class="title_en" name="title_en"
							id="title_en"
							value="<?php echo ($exhibition->getTitle()->getDecodedTranslation_en());?>"
							required /></td>
					</tr>
					<tr>
						<td><label for="description_de">Beschreibung Ausstellung
								(Deutsch):</label></td>
						<td><textarea name="description_de" rows="5" cols="31"
								maxlength="1000" required><?php echo ($exhibition->getDescription()->getDecodedTranslation_de());?></textarea>
						</td>

					</tr>
					<tr>
						<td><label for="description_en">Beschreibung Ausstellung
								(Englisch):</label></td>
						<td><textarea name="description_en" rows="5" cols="31"
								maxlength="1000" required><?php echo ($exhibition->getDescription()->getDecodedTranslation_en());?></textarea>
						</td>
					</tr>
					<tr>
						<td><label for="startdate">Startdatum:</label></td>
						<td><input type="text" name="startdate" id="startdate"
							placeholder="TT.MM.JJJJ" required
							pattern="^(31|30|0[1-9]|[12][0-9]|[1-9])\.(0[1-9]|1[012]|[1-9])\.((18|19|20)\d{2}|\d{2})$"
							value="<?php echo ($exhibition->getStart()->format('d.m.Y'));?>" /></td>
					</tr>
					<tr>
						<td><label for="enddate">Abschlussdatum:</label></td>
						<td><input type="text" name="enddate" id="enddate"
							placeholder="TT.MM.JJJJ" required
							pattern="^(31|30|0[1-9]|[12][0-9]|[1-9])\.(0[1-9]|1[012]|[1-9])\.((18|19|20)\d{2}|\d{2})$"
							value="<?php echo ($exhibition->getEnd()->format('d.m.Y'));?>" /></td>
					</tr>
					<tr>
						<td><label for="openinghours">Öffnungszeiten:</label></td>
						<td><input type="text" name="openinghours" id="openinghours"
							value="<?php echo ($exhibition->getOpeningHours());?>" required></td>
					</tr>
					<tr>
						<td><label for="address">Anschrift:</label></td>
						<td><input type="text" name="address" id="address"
							value="<?php echo ($exhibition->getAddress());?>" required></td>
					</tr>
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