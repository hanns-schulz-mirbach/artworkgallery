<nav id="mainnav">
	<a href="index.php"><?php echo ($tc->getText("startpage"));?></a> | <a
		href="art.php"><?php echo ($tc->getText("art"));?></a> | <a
		href="exhibition.php"><?php echo ($tc->getText("exhibition"));?></a> |
	<a href="bio.php"><?php echo ($tc->getText("bio"));?></a> | <a
		href="news.php"><?php echo ($tc->getText("news"));?></a> | 
		<?php
echo ($tc->getLinkForLanguageSwitch());
echo ($tc->getAdminNavigationLink());
?>
</nav>