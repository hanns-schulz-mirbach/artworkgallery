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
			<h1>Neuanlage Ausstellung</h1>
			<form action="exhibition_create_confirmation.php" method="post">
				<table>
					<tr>
						<th>Attribut</th>
						<th>Wert</th>
					</tr>
					<tr>
						<td><label for="code">Kurzbezeichnung Ausstellung:</label></td>
						<td><input type="text" class="code" name="code" id="code" required />
					
					</tr>
					<tr>
						<td><label for="title_de">Ausstellungstitel Deutsch:</label></td>
						<td><input type="text" class="title_de" name="title_de"
							id="title_de" required />
					
					</tr>
					<tr>
						<td><label for="title_en">Ausstellungstitel Englisch:</label></td>
						<td><input type="text" class="title_en" name="title_en"
							id="title_en" required /></td>
					</tr>
					<tr>
						<td><label for="description_de">Beschreibung Ausstellung
								(Deutsch):</label></td>
						<td><textarea name="description_de" rows="10" cols="20"
								maxlength="1000" required>Beschreibung Ausstellung (Deutsch)</textarea>
						</td>

					</tr>
					<tr>
						<td><label for="description_en">Beschreibung Ausstellung
								(Englisch):</label></td>
						<td><textarea name="description_en" rows="10" cols="20"
								maxlength="1000" required>Beschreibung Galerie (Englisch)</textarea>
						</td>
					</tr>
					<tr>
						<td><label for="startdate">Startdatum:</label></td>
						<td><input type="text" name="startdate" id="startdate"
							placeholder="TT.MM.JJJJ" required
							pattern="^(31|30|0[1-9]|[12][0-9]|[1-9])\.(0[1-9]|1[012]|[1-9])\.((18|19|20)\d{2}|\d{2})$"
							title="Datumseingabe im Format TT.MM.JJJJ" /></td>
					</tr>
					<tr>
						<td><label for="enddate">Abschlussdatum:</label></td>
						<td><input type="text" name="enddate" id="enddate"
							placeholder="TT.MM.JJJJ" required
							pattern="^(31|30|0[1-9]|[12][0-9]|[1-9])\.(0[1-9]|1[012]|[1-9])\.((18|19|20)\d{2}|\d{2})$"
							title="Datumseingabe im Format TT.MM.JJJJ" /></td>
					</tr>
					<tr>
						<td><label for="openinghours">Öffnungszeiten:</label></td>
						<td><input type="text" name="openinghours" id="openinghours"
							required></td>
					</tr>
					<tr>
						<td><label for="address">Anschrift:</label></td>
						<td><input type="text" name="address" id="address" required></td>
					</tr>
				</table>

			<?php
echo ($formatter->getSubmitResetControl());
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