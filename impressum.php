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
			<div class='impressum'>
				<h1><?php echo ($tc->getText("about"));?></h1>
				<p>Angaben gemäß § 5 TMG</p>
				<p>
					Dr. Cathrin Schulz-Mirbach <br> Aublick 18<br> 23611 Bad Schwartau
					<br>
				</p>
				<p>
					<strong>Kontakt:</strong> <br> E-Mail: <a
						href='mailto:info@cathrin-schulz-mirbach.art'>info@cathrin-schulz-mirbach.art</a><br />
				</p>

				<h3>Haftung für Inhalte</h3>
				Die Inhalte unserer Seiten wurden mit größter Sorgfalt erstellt. Für
				die Richtigkeit, Vollständigkeit und Aktualität der Inhalte können
				wir jedoch keine Gewähr übernehmen. Als Diensteanbieter sind wir
				gemäß § 7 Abs.1 TMG für eigene Inhalte auf diesen Seiten nach den
				allgemeinen Gesetzen verantwortlich. Nach §§ 8 bis 10 TMG sind wir
				als Diensteanbieter jedoch nicht verpflichtet, übermittelte oder
				gespeicherte fremde Informationen zu überwachen oder nach Umständen
				zu forschen, die auf eine rechtswidrige Tätigkeit hinweisen.
				Verpflichtungen zur Entfernung oder Sperrung der Nutzung von
				Informationen nach den allgemeinen Gesetzen bleiben hiervon
				unberührt. Eine diesbezügliche Haftung ist jedoch erst ab dem
				Zeitpunkt der Kenntnis einer konkreten Rechtsverletzung möglich. Bei
				Bekanntwerden von entsprechenden Rechtsverletzungen werden wir diese
				Inhalte umgehend entfernen.
				<h3>Haftung für Links</h3>
				Unser Angebot enthält Links zu externen Webseiten Dritter, auf deren
				Inhalte wir keinen Einfluss haben. Deshalb können wir für diese
				fremden Inhalte auch keine Gewähr übernehmen. Für die Inhalte der
				verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber
				der Seiten verantwortlich. Die verlinkten Seiten wurden zum
				Zeitpunkt der Verlinkung auf mögliche Rechtsverstöße überprüft.
				Rechtswidrige Inhalte waren zum Zeitpunkt der Verlinkung nicht
				erkennbar. Eine permanente inhaltliche Kontrolle der verlinkten
				Seiten ist jedoch ohne konkrete Anhaltspunkte einer Rechtsverletzung
				nicht zumutbar. Bei Bekanntwerden von Rechtsverletzungen werden wir
				derartige Links umgehend entfernen.
				<h3>Urheberrecht</h3>
				Die durch die Seitenbetreiber erstellten Inhalte und Werke auf
				diesen Seiten unterliegen dem deutschen Urheberrecht. Die
				Vervielfältigung, Bearbeitung, Verbreitung und jede Art der
				Verwertung außerhalb der Grenzen des Urheberrechtes bedürfen der
				schriftlichen Zustimmung des jeweiligen Autors bzw. Erstellers.
				Downloads und Kopien dieser Seite sind nur für den privaten, nicht
				kommerziellen Gebrauch gestattet. Soweit die Inhalte auf dieser
				Seite nicht vom Betreiber erstellt wurden, werden die Urheberrechte
				Dritter beachtet. Insbesondere werden Inhalte Dritter als solche
				gekennzeichnet. Sollten Sie trotzdem auf eine
				Urheberrechtsverletzung aufmerksam werden, bitten wir um einen
				entsprechenden Hinweis. Bei Bekanntwerden von Rechtsverletzungen
				werden wir derartige Inhalte umgehend entfernen.

				<h3>
					<a href="datenschutz.php">Datenschutzerklärung</a>
				</h3>


			</div>


		</article>
		</main>
	</div>
	<?php include ("./template/footer.php"); ?>
</body>
</html>