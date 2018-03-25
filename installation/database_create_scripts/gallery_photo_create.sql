CREATE TABLE `gallery_photo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `photo` int(11) NOT NULL,
  `index_of_photo` int(11) NOT NULL,
  `gallery` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
