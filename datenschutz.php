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
require_once ("./controller/translation_controller.php");

session_start();
$sessionManager = new SessionManager();
$language = $sessionManager->getUserLanguage();
$tc = new TranslationController($language);
$tc->setUserRole($sessionManager->getUserRole());

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
			<h1>Datenschutzerkl&auml;rung</h1>
			<h3>Datenschutz</h3>
			Die Betreiber dieser Seiten nehmen den Schutz Ihrer pers&ouml;nlichen
			Daten sehr ernst. Wir behandeln Ihre personenbezogenen Daten
			vertraulich und entsprechend der gesetzlichen Datenschutzvorschriften
			sowie dieser Datenschutzerkl&auml;rung.
			<p>Die Nutzung unserer Webseite ist in der Regel ohne Angabe
				personenbezogener Daten m&ouml;glich. Soweit auf unseren Seiten
				personenbezogene Daten (beispielsweise Name, Anschrift oder
				E-Mail-Adressen) erhoben werden, erfolgt dies, soweit m&ouml;glich,
				stets auf freiwilliger Basis. Diese Daten werden ohne Ihre
				ausdr&uuml;ckliche Zustimmung nicht an Dritte weitergegeben.</p>
			<p>Wir weisen darauf hin, dass die Daten&uuml;bertragung im Internet
				(z.B. bei der Kommunikation per E-Mail) Sicherheitsl&uuml;cken
				aufweisen kann. Ein l&uuml;ckenloser Schutz der Daten vor dem
				Zugriff durch Dritte ist nicht m&ouml;glich.</p>

			<h3>Cookies</h3>
			Die Internetseiten verwenden teilweise so genannte Cookies. Cookies
			richten auf Ihrem Rechner keinen Schaden an und enthalten keine
			Viren. Cookies dienen dazu, unser Angebot nutzerfreundlicher,
			effektiver und sicherer zu machen. Cookies sind kleine Textdateien,
			die auf Ihrem Rechner abgelegt werden und die Ihr Browser speichert.
			<p>Die meisten der von uns verwendeten Cookies sind so genannte
				„Session-Cookies“. Sie werden nach Ende Ihres Besuchs automatisch
				gel&ouml;scht. Andere Cookies bleiben auf Ihrem Endger&auml;t
				gespeichert, bis Sie diese l&ouml;schen. Diese Cookies
				erm&ouml;glichen es uns, Ihren Browser beim n&auml;chsten Besuch
				wiederzuerkennen.</p>
			<p>Sie k&ouml;nnen Ihren Browser so einstellen, dass Sie &uuml;ber
				das Setzen von Cookies informiert werden und Cookies nur im
				Einzelfall erlauben, die Annahme von Cookies f&uuml;r bestimmte
				F&auml;lle oder generell ausschlie&szlig;en sowie das automatische
				L&ouml;schen der Cookies beim Schlie&szlig;en des Browser
				aktivieren. Bei der Deaktivierung von Cookies kann die
				Funktionalit&auml;t dieser Website eingeschr&auml;nkt sein.</p>

			<h3>Kontaktformular</h3>
			Wenn Sie uns per Kontaktformular Anfragen zukommen lassen, werden
			Ihre Angaben aus dem Anfrageformular inklusive der von Ihnen dort
			angegebenen Kontaktdaten zwecks Bearbeitung der Anfrage und f&uuml;r
			den Fall von Anschlussfragen bei uns gespeichert. Diese Daten geben
			wir nicht ohne Ihre Einwilligung weiter.

			<h3>Recht auf Auskunft, L&ouml;schung, Sperrung</h3>
			Sie haben jederzeit das Recht auf unentgeltliche Auskunft &uuml;ber
			Ihre gespeicherten personenbezogenen Daten, deren Herkunft und
			Empf&auml;nger und den Zweck der Datenverarbeitung sowie ein Recht
			auf Berichtigung, Sperrung oder L&ouml;schung dieser Daten. Hierzu
			sowie zu weiteren Fragen zum Thema personenbezogene Daten k&ouml;nnen
			Sie sich jederzeit unter der im Impressum angegebenen Adresse an uns
			wenden.

			<h3>Widerspruch Werbe-Mails</h3>
			Der Nutzung von im Rahmen der Impressumspflicht ver&ouml;ffentlichten
			Kontaktdaten zur &Uuml;bersendung von nicht ausdr&uuml;cklich
			angeforderter Werbung und Informationsmaterialien wird hiermit
			widersprochen. Die Betreiber der Seiten behalten sich
			ausdr&uuml;cklich rechtliche Schritte im Falle der unverlangten
			Zusendung von Werbeinformationen, etwa durch Spam-E-Mails, vor.

		</article>
		</main>
	</div>
	<?php include ("./template/footer.php"); ?>
</body>
</html>