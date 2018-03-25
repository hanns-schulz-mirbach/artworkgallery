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
require_once ("./util/translation_generator.php");
require_once ("./util/formatter.php");
require_once ("./util/translation_type.php");
require_once ("./controller/access_controller.php");
require_once ("./controller/translation_controller.php");

session_start();
$sessionManager = new SessionManager();

$accessController = new AccessController($sessionManager->getUserRole());

if (! $accessController->access_Translation()) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
    exit();
}

$language = $sessionManager->getUserLanguage();
$tc = new TranslationController($language);
$tc->setUserRole($sessionManager->getUserRole());

$translationTypeCode = intval($_GET["type"]);
$translationType = new TranslationType();
$translationType->setTypeCode($translationTypeCode);

$formatter = new Formatter();

$gen = new TranslationGenerator();
if ($translationType->isSystemText()) {
    $num = $gen->generateAllSystemTranslations();
} elseif ($translationType->isEndUserText()) {
    $num = $gen->generateAllUserTranslations();
}

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
			<h1>Neuerzeugung Systemübersetzungen</h1>
		Die Systemübersetzungen wurden neu erzeugt. 
		
		
			<?php
echo ("<br /> <br />");
echo ('<a href="admin.php">Administrationsfunktionen</a>');
?>
			</article>
		</main>
	</div>
	<?php include ("./template/footer.php"); ?>
	
	<script type="text/javascript" src="script/index.js"></script>
</body>
</html>