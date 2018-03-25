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

if (! $accessController->access_ArtistMasterData()) {
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
			<h1>Neuanlage Künstler</h1>
			<form action="artist_create_confirmation.php" method="post">
				<table>
					<tr>
						<th>Attribut</th>
						<th>Wert</th>
					</tr>
					<tr>
						<td><label for="firstname">Vorname:</label></td>
						<td><input type="text" class="firstname" name="firstname"
							id="firstname" required />
					
					</tr>
					<tr>
						<td><label for="lastname">Nachname:</label></td>
						<td><input type="text" class="lastname" name="lastname"
							id="lastname" required /></td>
					</tr>
					<tr>
						<td><label for="birthdate">Geburtsdatum:</label></td>
						<td><input type="text" name="birthdate" id="birthdate"
							placeholder="TT.MM.JJJJ" required
							pattern="^(31|30|0[1-9]|[12][0-9]|[1-9])\.(0[1-9]|1[012]|[1-9])\.((18|19|20)\d{2}|\d{2})$"
							title="Datumseingabe im Format TT.MM.JJJJ" /></td>
					</tr>
					<tr>
						<td><label for="mail">E-Mail:</label></td>
						<td><input type="email" name="mail" id="mail" required></td>
					</tr>
					<tr>
						<td><label for="phone">Festnetznummer:</label></td>
						<td><input type="text" name="phone" id="phone"></td>
					</tr>
					<tr>
						<td><label for="mobile">Mobilnummer:</label></td>
						<td><input type="text" name="mobile" id="mobile"></td>
					</tr>
					<tr>
						<td><label for="address">Adresse:</label></td>
						<td><input type="text" class="address" name="address" id="address"
							required /></td>
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