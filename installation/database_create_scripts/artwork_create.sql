CREATE TABLE `artwork` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `depth` int(11) NOT NULL,
  `weight` int(11) NOT NULL,
  `title_translation` int(11) NOT NULL,
  `signature_date` date NOT NULL,
  `technique` int(11) NOT NULL,
  `material` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `location_translation` int(11) NOT NULL,
  `availability` int(11) NOT NULL,
  `artist` int(11) NOT NULL,
  `signature_name` varchar(100) NOT NULL,
  `explanation_translation` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
