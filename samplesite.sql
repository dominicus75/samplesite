
CREATE TABLE `ranks` (
  `rank` varchar(16) CHARACTER SET ascii NOT NULL DEFAULT 'editor',
  PRIMARY KEY (`rank`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii;


INSERT INTO `ranks` (`rank`) VALUES
('root'),
('admin'),
('editor'),
('author');


CREATE TABLE `admins` (
  `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `avatar` tinytext CHARACTER SET ascii DEFAULT NULL,
  `pass` varchar(128) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL,
  `created` timestamp DEFAULT current_timestamp(),
  `updated` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rank` varchar(16) CHARACTER SET ascii NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `email` (`email`),
  FOREIGN KEY (`rank`) REFERENCES `ranks` (`rank`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `users` (
  `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `avatar` tinytext CHARACTER SET ascii DEFAULT NULL,
  `pass` varchar(128) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL,
  `created` timestamp DEFAULT current_timestamp(),
  `updated` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `sessions` (
  `sid` varchar(64) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `aid` tinyint(3) UNSIGNED,
  `uid` tinyint(3) UNSIGNED,
  `spass` varchar(128) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `stime` int(12) UNSIGNED NOT NULL,
  PRIMARY KEY (`sid`),
  FOREIGN KEY (`aid`) REFERENCES `admins` (`id`),
  FOREIGN KEY (`uid`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `messages` (
  `url` varchar(64) CHARACTER SET ascii NOT NULL,
  `title` tinytext CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `description` tinytext CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `image` tinytext CHARACTER SET ascii NOT NULL,
  PRIMARY KEY (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `messages` (`url`, `title`, `description`, `image`) VALUES
('400', '400-as HTTP hiba', 'Hibás kérelem. A szerver képtelen volt értelmezni a kérelem szintaxisát.', '/images/400.jpg'),
('401', '401-es HTTP hiba', 'Nincs hitelesítve. A kérelem hitelesítést igényel. A szerver bejelentkezés után megtekinthető oldalak esetén adhatja vissza ezt a választ.', '/images/401.jpg'),
('403', '403-as HTTP hiba', 'Hozzáférés megtagadva. A szerver visszautasítja a kérelmet', '/images/403.jpg'),
('404', '404-es HTTP hiba', 'A kért oldal nem található. Lehet, hogy a keresett lapot eltávolították, megváltoztatták a nevét, vagy átmenetileg nem érhető el', '/images/404.jpg'),
('405', '405-ös HTTP hiba', 'Hibás metódus. A kérelemben megadott HTTP metódus nem engedélyezett.', '/images/405.jpg'),
('408', '408-as HTTP hiba', 'Időtúllépés. Az oldal túlterhelt, illetve túl sokáig tart a kiszolgálónak megjelenítenie', '/images/408.jpg'),
('500', '500-as HTTP hiba', 'Belső szerverhiba. A szerver hibát észlelt, így nem tudja teljesíteni a kérelmet)', '/images/500.jpg'),
('502', '502-es HTTP hiba', 'Rossz átjáró. A szerver egy hibás HTTP-választ kapott egy másik szervertől', '/images/502.jpg'),
('503', '503-as HTTP hiba', 'A szolgáltatás nem elérhető. A szerver nem képes kezelni a kérést, túlterhelés vagy karbantartás miatt', '/images/503.jpg'),
('504', '504-es HTTP hiba', 'Átjáró időtúllépése. A szerver átjáróként vagy proxyként történő működése során nem kapott időben választ a felsőbb szintű szervertől.', '/images/504.jpg');

CREATE TABLE `categories` (
  `url` varchar(255) CHARACTER SET ascii NOT NULL,
  `title` varchar(64) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `image` tinytext CHARACTER SET ascii DEFAULT NULL,
  `parent` varchar(255) CHARACTER SET ascii DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`url`),
  FOREIGN KEY (`parent`) REFERENCES `categories` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `pages` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `url` varchar(128) CHARACTER SET ascii NOT NULL,
  `author` tinyint(3) UNSIGNED NOT NULL,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `image` tinytext CHARACTER SET ascii  DEFAULT NULL,
  `body` text CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `created` timestamp DEFAULT current_timestamp(),
  `updated` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`author`) REFERENCES `admins` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `articles` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category` varchar(255) CHARACTER SET ascii DEFAULT NULL,
  `url` varchar(128) CHARACTER SET ascii NOT NULL,
  `author` tinyint(3) UNSIGNED NOT NULL,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `image` tinytext CHARACTER SET ascii DEFAULT NULL,
  `body` text CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `created` timestamp DEFAULT current_timestamp(),
  `updated` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`category`) REFERENCES `categories` (`url`),
  FOREIGN KEY (`author`) REFERENCES `admins` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `page_images` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `url` varchar(128) NOT NULL,
  `mime` varchar(64) NOT NULL,
  `size` mediumint(4) UNSIGNED NOT NULL,
  `width` smallint(5) UNSIGNED NOT NULL,
  `height` smallint(5) UNSIGNED NOT NULL,
  `page` smallint(5) UNSIGNED DEFAULT NULL,
  `created` timestamp DEFAULT current_timestamp(),
  `updated` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`page`) REFERENCES `pages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii;


CREATE TABLE `article_images` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `url` varchar(128) NOT NULL,
  `mime` varchar(64) NOT NULL,
  `size` mediumint(4) UNSIGNED NOT NULL,
  `width` smallint(5) UNSIGNED NOT NULL,
  `height` smallint(5) UNSIGNED NOT NULL,
  `article` smallint(5) UNSIGNED DEFAULT NULL,
  `created` timestamp DEFAULT current_timestamp(),
  `updated` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`article`) REFERENCES `articles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

