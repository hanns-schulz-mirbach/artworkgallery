CREATE TABLE `report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text_translation` int(11) NOT NULL,
  `title_translation` int(11) NOT NULL,
  `publication_date` date NOT NULL,
  `obsolescence_date` date NOT NULL,
  `author` varchar(100) NOT NULL,
  `exhibition` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
