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
require_once ("./util/translation_type.php");
require_once ("./controller/translation_controller.php");
require_once ("./controller/report_controller.php");
require_once ("./controller/access_controller.php");
require_once ("./model/report.php");

session_start();
$sessionManager = new SessionManager();

$accessController = new AccessController($sessionManager->getUserRole());

if (! $accessController->access_Report()) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
    exit();
}

$language = $sessionManager->getUserLanguage();
$tc = new TranslationController($language);
$tc->setUserRole($sessionManager->getUserRole());

$formatter = new Formatter();

$publicationDate = DateTime::createFromFormat('d.m.Y',
        trim($_POST["publicationdate"]));
$obsolescenceDate = DateTime::createFromFormat('d.m.Y',
        trim($_POST["obsolescencedate"]));
$author = trim($_POST["author"]);
$codeText = trim($_POST["code"]);
$codeTitle = $codeText . "-title";
$title_de = trim($_POST["title_de"]);
$title_en = trim($_POST["title_en"]);
$text_de = trim($_POST["report_text_de"]);
$text_en = trim($_POST["report_text_en"]);
$exhibitionId = intval($_POST["exhibition"]);

$type = new TranslationType();
$type->setIsEndUserText();

$reportController = new ReportController();
$report = $reportController->getReportById(intval($_GET["id"]));

$report->setPublicationDate($publicationDate);
$report->setObsolescenceDate($obsolescenceDate);
$report->setAuthor($author);
$report->getExhibition()->setId($exhibitionId);

$translationTitle = $report->getTitleTranslation();
$translationTitle->setCode($codeTitle);
$translationTitle->setType($type);
$translationTitle->setTranslation_de($title_de);
$translationTitle->setTranslation_en($title_en);

$translationText = $report->getTextTranslation();
$translationText->setCode($codeText);
$translationText->setType($type);
$translationText->setTranslation_de($text_de);
$translationText->setTranslation_en($text_en);

$reportController->setReport($report);
$affectedRows = $reportController->updateReport();

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
			<h1>Ergebnis Änderung Bericht</h1>
<?php
if (($affectedRows == 0) || ($affectedRows == 1)) {
    echo ('Die Änderung des Berichts war erfolgreich. Es wurden die folgenden Daten übernommen: ');
    echo ("<br />");
    echo ("<br />");
    echo ("$report");
} else {
    echo ('Die Änderung des Berichts hat nicht funktioniert. Bitte überprüfen Sie die Daten und wiederholen Sie ggf. die   ');
    echo ('<a href="report_update.php">Änderung des Berichts</a>');
}
echo ("<br /> <br />");
echo ('<a href="admin.php">Administrationsfunktionen</a>');
?>
		</article>
		</main>
	</div>
	<?php include ("./template/footer.php"); ?>
	
</body>
</html>