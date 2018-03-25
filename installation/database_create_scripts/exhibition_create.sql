CREATE TABLE `exhibition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start` date NOT NULL,
  `end` date NOT NULL,
  `title_translation` int(11) NOT NULL,
  `address` varchar(200) NOT NULL,
  `openinghours` varchar(200) NOT NULL,
  `description_translation` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
