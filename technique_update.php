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
require_once ("./model/technique.php");
require_once ("./controller/translation_controller.php");
require_once ("./controller/technique_controller.php");
require_once ("./controller/access_controller.php");

session_start();
$sessionManager = new SessionManager();

$accessController = new AccessController($sessionManager->getUserRole());

if (! $accessController->access_TechniqueMasterData()) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
    exit();
}

$language = $sessionManager->getUserLanguage();
$tc = new TranslationController($language);
$tc->setUserRole($sessionManager->getUserRole());

$formatter = new Formatter();

$techniqueId = intval($_GET["id"]);

$techniqueController = new TechniqueController();

$technique = $techniqueController->getTechniqueById($techniqueId);

$updateUrl = "technique_update_confirmation.php?id=" . $technique->getId();
$deleteUrl = "technique_delete.php?id=" . $technique->getId();
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
			<h1>Änderung Technik</h1>
			<form action='<?php echo ("$updateUrl")?>' method="post">
				<label for="code">Kennung:</label> <input type="text" class="code"
					name="code" id="code"
					value='<?php echo ($technique->getTranslation()->getCode()); ?>'
					required /> <br /> <br /> <label for="translation_de">Text Deutsch:</label><br />
				<textarea name="translation_de" rows="5" cols="80" maxlength="10000"><?php echo ($technique->getTranslation()->getDecodedTranslation_de()); ?></textarea>
				<br /> <label for="translation_en">Text Englisch:</label><br />
				<textarea name="translation_en" rows="5" cols="80" maxlength="10000"><?php echo ($technique->getTranslation()->getDecodedTranslation_en()); ?></textarea>
				<br />

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