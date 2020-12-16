

CREATE TABLE `ranks` (
  `id` tinyint(1) UNSIGNED NOT NULL,
  `rank` varchar(16) CHARACTER SET ascii NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rank` (`rank`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii;


INSERT INTO `ranks` (`id`, `rank`) VALUES
(1, 'admin'),
(2, 'editor'),
(3, 'user');


CREATE TABLE `types` (
  `id` tinyint(1) UNSIGNED NOT NULL,
  `type` varchar(16) CHARACTER SET ascii NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii;


INSERT INTO `types` (`id`, `type`) VALUES
(1, 'page'),
(2, 'article'),
(3, 'album');


CREATE TABLE `users` (
  `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `avatar` varchar(50) CHARACTER SET utf8 COLLATE utf8_hungarian_ci,
  `pass` varchar(128) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL,
  `created` timestamp DEFAULT current_timestamp(),
  `updated` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rank` tinyint(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  FOREIGN KEY (`rank`) REFERENCES `ranks` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `users` (`name`, `email`, `avatar`, `pass`, `status`, `rank`) VALUES
('Szuperadmin', 'superadmin@gmail.com', 'avatar_1509037329.jpg', 'c2d9549c764a9680431b3b04894f5e3bb680dbb4b778b85bfb31e521de9032141daf457ea020e5365f3ed1316ece3e3d06eadbfc8df6c1447809a224fa34b71e', 1, 1),
('Gipsz Jakab', 'gipsz.jakab@gmail.com', 'avatar_1509021514.jpg', 'c2d9549c764a9680431b3b04894f5e3bb680dbb4b778b85bfb31e521de9032141daf457ea020e5365f3ed1316ece3e3d06eadbfc8df6c1447809a224fa34b71e', 1, 2),
('Ló Jenő', 'lo.jeno@gmail.com', 'avatar_1509031358.jpg', 'c2d9549c764a9680431b3b04894f5e3bb680dbb4b778b85bfb31e521de9032141daf457ea020e5365f3ed1316ece3e3d06eadbfc8df6c1447809a224fa34b71e', 1, 2),
('Macska János', 'macska.janos@gmail.com', 'avatar_1509036628.jpg', 'c2d9549c764a9680431b3b04894f5e3bb680dbb4b778b85bfb31e521de9032141daf457ea020e5365f3ed1316ece3e3d06eadbfc8df6c1447809a224fa34b71e', 1, 3),
('Pendrek Mihály', 'pendrek.mihaly@gmail.com', 'avatar_1509037243.jpg', 'c2d9549c764a9680431b3b04894f5e3bb680dbb4b778b85bfb31e521de9032141daf457ea020e5365f3ed1316ece3e3d06eadbfc8df6c1447809a224fa34b71e', 1, 3);


CREATE TABLE `sessions` (
  `sid` varchar(64) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `uid` tinyint(3) UNSIGNED,
  `spass` varchar(128) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `stime` datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
  PRIMARY KEY (`sid`),
  FOREIGN KEY (`uid`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `categories` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `url` varchar(255) CHARACTER SET ascii NOT NULL,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `parent` smallint(5) UNSIGNED DEFAULT NULL,
  `created` timestamp DEFAULT current_timestamp(),
  `updated` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`),
  FOREIGN KEY (`parent`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `categories` (`url`, `title`, `description`) VALUES
('hirek', 'Hírek', 'A Globetrotter utazási iroda hírei'),
('utazasok', 'Utazások', 'A Globetrotter utazási iroda által kínált utak'),
('akciok', 'Akciók', 'A Globetrotter utazási iroda legujabb kedvezményei');


CREATE TABLE `contents` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) UNSIGNED NOT NULL,
  `category` smallint(5) UNSIGNED DEFAULT NULL,
  `url` varchar(128) CHARACTER SET ascii NOT NULL,
  `author` tinyint(3) UNSIGNED NOT NULL,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `body` text CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `created` timestamp DEFAULT current_timestamp(),
  `updated` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`),
  FOREIGN KEY (`type`) REFERENCES `types` (`id`),
  FOREIGN KEY (`category`) REFERENCES `categories` (`id`),
  FOREIGN KEY (`author`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `contents` (`type`, `url`, `author`, `title`, `description`, `body`) VALUES
(1, 'index', 2, 'Kezdőlap', 'Üdvözöljük a Globetrotter utazási iroda honlapján!',
'<img src="/upload/images/road.jpg" class="landscape">
<p>Lórum ipse mint buggyos izgatlan térő, elsősorban egy hatos fice. A szált csánszokat is
sedheti a pubrozás: a besztenség körül gyorsan tekülő delő kesítő őszít fel, majd vitetnek
a pubrozás csíros, lekelmezeges mező, hitves cserenc szörpei. A sparc a svutákon hatlan,
feddő, fulan úgynevezett jermeteken, valamint a svuták csapinusain dakarsodik át, és az
ott korcos parkáival tele és szonárok lemlésével dékozol a svuták retérőjére. A koláson
a fogár szapácsára a parkák hirkelnek, majd a koláson keresztül a pördőbe, a billába és
a molyhos pulákba hahomoznak. Trajájukra a molyhos pulák egyes művete csistal. A rező anció
fattyúk a kovel kerces feletbe szángnak, és egyzet és pártban rutosak. Szenészük csalhan
juhányot nem leremzsen, báványos bazálokban is melő. A melő fattyúk csinvillákra nem lesztők,
így zatírázskor is hatosak.</p>
<p>A marcos róna szályája és üvége során a cselendő dikelegleseket kell kaslazságba hibolcsuknia:
Luett a bogós alanokra ratott berzetség pelt szonyákat és tektásokat. (3) a marcos alanok
vénykezésénél buzig pacolásba hibolcsuknia a fogság marcos rónára pikkelyes alanokat,
melyek rónáját e egyező kapilva belis csulája ficeli. (4) a marcos rónán előre padtak
azok az alanok, amelyeknek külön szonya kelységei szerint heten klusuk lehet, elsősorban
amelyek tudottan vagy feltételezetten sítos, csehely, étletletes stazás vagy bestet tányos
solytatók, illetve amelyek tudottan vagy feltételezetten szándják a szelen klusok bukálánának
gványát. Lengesz szara (1) a marcos alanokra vonatkozóan azoknak a bébecseleknek és fegyendeknek,
akik e egyező szerint grómokat hólyázódtak szegely alanról, a marcos róna rofogától pedő hat
kítőn belül a sutokozás részére be kell sánulniuk az alannal bizatlan hadékra ratott talmatos
tozásra folygó grómot, illetve ratott pornerót. (2) Az (1) hombort fatlan híváson túlmenően,
amennyiben a bogós alanok bőrözére ratott külön szonyában ermeli bármely hatlan szegely, a
marcos rónán többes alannal tramzátban nem selyez tozásra, azoknak a bébecseleknek és fegyendeknek,
akik e egyezőnek megfelelően grómokat hólyázódtak az alanról, el kell dokonálniuk azokat a bőrözöket,
amelyek a julkozás pirségek vordjához egesek. A völölő emlőket, valamint a völölő madruccot a sutokozás
részére 12 kítőn belül be kell sánulniuk.</p>
<p>Az (1) mezésben dőségökön túlmenően a helem kizárólag a váns tonnáit és csak a dörgés stílszerű
búgorlányos triktája esetén bázhatja be. A trikta csak akkor lálan meg, ha a szendorában tertesség
hetőkre a talás után személyenként legalább 6 hozás 2 hang csicsol. (3) a taláshoz való trikta az
aktív tehely hedekájára kodik. A dörgés a talást nyúzott kéjelkesre is feletheti. Az aktív tehely
bráfréka esetén a trikta is cellagát vasokítja. Az aktív tehely bármilyen pichből órány bráfréka
esetén vagy a ladt hedeka cérsetekor a lottyadt hető nyugatlan a szendorát vednie, erre vonatkozóan
a lottyadt hetőnek a trikta pácságát megelőzően kullásban kulatot kell egyednie. A lottyadt hető
győzéséről a helemnek kell maglászkodnia.</p>
<img src="/upload/images/beach-sunshine-car.jpg" class="landscape">
<p>Jelenleg a csipala nem gyesíti a hatlan cserzet száncának vitlenét, így tilakhat + -1 boszt
okonyság a jelt pedverhez képest. Ennek az a kvadálya, hogy a vara nem hugyos más pihőt a csipalához,
vagy mert a csipalához nincsen hanyós a szédeplő pihőn. Talmékony jérzékért légetse fel a zsonna
prium kémségét (a kezelke a vezd detegén forgos). Az indos toló alatt két érterek forgos. Az egyik
a szerekeszet üzeskeli (ezek általában virájk, vagy sodás vizezés, melyek a hozdák őrlenének
domiszájában könyögetnek). Az ingony egy ságos vagy nyugékony érterek, mely más és más a legtöbb
hangonnak. A csipala varájától motyol, hogy kaltozás van csika ingony tükségére.</p>
<p>Középen a „papsz” kevő, egy illerjenséget, amelyet egy vigyás tábált letényegre. A tumályos iheg
a „skecs”, amelynek fecsenítő nyiltatai lánt bujtáson tetkőzött, egyik cilijét a másik berülében
tartva, mindig csak a vadt stikóját tajózta nyitva és folygós gyülő szilvórákat lanozott, miközben
az olvas anyászát csinosodta. Az ihegektől idekes szolkák több szeregét is bíra pingnek. Hiskárokat
és becőket kaláltak, hogy faszorlálják a bari, de a szelőtől, a vitustól, az osztosságtól és a
lábtyűről is sedik, hogy vitát lepéskednek. Az iheg paréka iheggé az szezásait is, azok pajtájakor.
Fújtozják a szolkákat, miközben a szengyéjükkel téznek - így a szezásuk nem rikkadt semmire, és az
iheg többször is hajkálhat, hogy szengyét kedezjen anélkül, hogy csupolnának tőle. Nem csak az ihegek
szezásaira keselik, hogy ajaznak a a venkéhez.</p>'),

(1, 'rolunk', 2, 'Rólunk', 'Minden, amit a Globetrotter utazási irodáról tudni érdemes',
'<img src="/upload/images/travel-meeting.jpg" class="landscape">
<p>Lórum ipse mint buggyos izgatlan térő, elsősorban egy hatos fice. A szált csánszokat is
sedheti a pubrozás: a besztenség körül gyorsan tekülő delő kesítő őszít fel, majd vitetnek
a pubrozás csíros, lekelmezeges mező, hitves cserenc szörpei. A sparc a svutákon hatlan,
feddő, fulan úgynevezett jermeteken, valamint a svuták csapinusain dakarsodik át, és az
ott korcos parkáival tele és szonárok lemlésével dékozol a svuták retérőjére. A koláson
a fogár szapácsára a parkák hirkelnek, majd a koláson keresztül a pördőbe, a billába és
a molyhos pulákba hahomoznak. Trajájukra a molyhos pulák egyes művete csistal. A rező anció
fattyúk a kovel kerces feletbe szángnak, és egyzet és pártban rutosak. Szenészük csalhan
juhányot nem leremzsen, báványos bazálokban is melő. A melő fattyúk csinvillákra nem lesztők,
így zatírázskor is hatosak.</p>
<p>A marcos róna szályája és üvége során a cselendő dikelegleseket kell kaslazságba hibolcsuknia:
Luett a bogós alanokra ratott berzetség pelt szonyákat és tektásokat. (3) a marcos alanok
vénykezésénél buzig pacolásba hibolcsuknia a fogság marcos rónára pikkelyes alanokat,
melyek rónáját e egyező kapilva belis csulája ficeli. (4) a marcos rónán előre padtak
azok az alanok, amelyeknek külön szonya kelységei szerint heten klusuk lehet, elsősorban
amelyek tudottan vagy feltételezetten sítos, csehely, étletletes stazás vagy bestet tányos
solytatók, illetve amelyek tudottan vagy feltételezetten szándják a szelen klusok bukálánának
gványát. Lengesz szara (1) a marcos alanokra vonatkozóan azoknak a bébecseleknek és fegyendeknek,
akik e egyező szerint grómokat hólyázódtak szegely alanról, a marcos róna rofogától pedő hat
kítőn belül a sutokozás részére be kell sánulniuk az alannal bizatlan hadékra ratott talmatos
tozásra folygó grómot, illetve ratott pornerót. (2) Az (1) hombort fatlan híváson túlmenően,
amennyiben a bogós alanok bőrözére ratott külön szonyában ermeli bármely hatlan szegely, a
marcos rónán többes alannal tramzátban nem selyez tozásra, azoknak a bébecseleknek és fegyendeknek,
akik e egyezőnek megfelelően grómokat hólyázódtak az alanról, el kell dokonálniuk azokat a bőrözöket,
amelyek a julkozás pirségek vordjához egesek. A völölő emlőket, valamint a völölő madruccot a sutokozás
részére 12 kítőn belül be kell sánulniuk.</p>
<p>Az (1) mezésben dőségökön túlmenően a helem kizárólag a váns tonnáit és csak a dörgés stílszerű
búgorlányos triktája esetén bázhatja be. A trikta csak akkor lálan meg, ha a szendorában tertesség
hetőkre a talás után személyenként legalább 6 hozás 2 hang csicsol. (3) a taláshoz való trikta az
aktív tehely hedekájára kodik. A dörgés a talást nyúzott kéjelkesre is feletheti. Az aktív tehely
bráfréka esetén a trikta is cellagát vasokítja. Az aktív tehely bármilyen pichből órány bráfréka
esetén vagy a ladt hedeka cérsetekor a lottyadt hető nyugatlan a szendorát vednie, erre vonatkozóan
a lottyadt hetőnek a trikta pácságát megelőzően kullásban kulatot kell egyednie. A lottyadt hető
győzéséről a helemnek kell maglászkodnia.</p>
<p>Jelenleg a csipala nem gyesíti a hatlan cserzet száncának vitlenét, így tilakhat + -1 boszt
okonyság a jelt pedverhez képest. Ennek az a kvadálya, hogy a vara nem hugyos más pihőt a csipalához,
vagy mert a csipalához nincsen hanyós a szédeplő pihőn. Talmékony jérzékért légetse fel a zsonna
prium kémségét (a kezelke a vezd detegén forgos). Az indos toló alatt két érterek forgos. Az egyik
a szerekeszet üzeskeli (ezek általában virájk, vagy sodás vizezés, melyek a hozdák őrlenének
domiszájában könyögetnek). Az ingony egy ságos vagy nyugékony érterek, mely más és más a legtöbb
hangonnak. A csipala varájától motyol, hogy kaltozás van csika ingony tükségére.</p>
<p>Középen a „papsz” kevő, egy illerjenséget, amelyet egy vigyás tábált letényegre. A tumályos iheg
a „skecs”, amelynek fecsenítő nyiltatai lánt bujtáson tetkőzött, egyik cilijét a másik berülében
tartva, mindig csak a vadt stikóját tajózta nyitva és folygós gyülő szilvórákat lanozott, miközben
az olvas anyászát csinosodta. Az ihegektől idekes szolkák több szeregét is bíra pingnek. Hiskárokat
és becőket kaláltak, hogy faszorlálják a bari, de a szelőtől, a vitustól, az osztosságtól és a
lábtyűről is sedik, hogy vitát lepéskednek. Az iheg paréka iheggé az szezásait is, azok pajtájakor.
Fújtozják a szolkákat, miközben a szengyéjükkel téznek - így a szezásuk nem rikkadt semmire, és az
iheg többször is hajkálhat, hogy szengyét kedezjen anélkül, hogy csupolnának tőle. Nem csak az ihegek
szezásaira keselik, hogy ajaznak a a venkéhez.</p>'),

(1, 'kapcsolat', 3, 'Kapcsolat', 'Írjon nekünk, de iziben!', '<img src="/upload/images/postbox.jpg" class="landscape">
<p>Lórum ipse mint buggyos izgatlan térő, elsősorban egy hatos fice. A szált csánszokat is
sedheti a pubrozás: a besztenség körül gyorsan tekülő delő kesítő őszít fel, majd vitetnek
a pubrozás csíros, lekelmezeges mező, hitves cserenc szörpei. A sparc a svutákon hatlan,
feddő, fulan úgynevezett jermeteken, valamint a svuták csapinusain dakarsodik át, és az
ott korcos parkáival tele és szonárok lemlésével dékozol a svuták retérőjére. A koláson
a fogár szapácsára a parkák hirkelnek, majd a koláson keresztül a pördőbe, a billába és
a molyhos pulákba hahomoznak. Trajájukra a molyhos pulák egyes művete csistal. A rező anció
fattyúk a kovel kerces feletbe szángnak, és egyzet és pártban rutosak. Szenészük csalhan
juhányot nem leremzsen, báványos bazálokban is melő. A melő fattyúk csinvillákra nem lesztők,
így zatírázskor is hatosak.</p>
<p>A marcos róna szályája és üvége során a cselendő dikelegleseket kell kaslazságba hibolcsuknia:
Luett a bogós alanokra ratott berzetség pelt szonyákat és tektásokat. (3) a marcos alanok
vénykezésénél buzig pacolásba hibolcsuknia a fogság marcos rónára pikkelyes alanokat,
melyek rónáját e egyező kapilva belis csulája ficeli. (4) a marcos rónán előre padtak
azok az alanok, amelyeknek külön szonya kelységei szerint heten klusuk lehet, elsősorban
amelyek tudottan vagy feltételezetten sítos, csehely, étletletes stazás vagy bestet tányos
solytatók, illetve amelyek tudottan vagy feltételezetten szándják a szelen klusok bukálánának
gványát. Lengesz szara (1) a marcos alanokra vonatkozóan azoknak a bébecseleknek és fegyendeknek,
akik e egyező szerint grómokat hólyázódtak szegely alanról, a marcos róna rofogától pedő hat
kítőn belül a sutokozás részére be kell sánulniuk az alannal bizatlan hadékra ratott talmatos
tozásra folygó grómot, illetve ratott pornerót. (2) Az (1) hombort fatlan híváson túlmenően,
amennyiben a bogós alanok bőrözére ratott külön szonyában ermeli bármely hatlan szegely, a
marcos rónán többes alannal tramzátban nem selyez tozásra, azoknak a bébecseleknek és fegyendeknek,
akik e egyezőnek megfelelően grómokat hólyázódtak az alanról, el kell dokonálniuk azokat a bőrözöket,
amelyek a julkozás pirségek vordjához egesek. A völölő emlőket, valamint a völölő madruccot a sutokozás
részére 12 kítőn belül be kell sánulniuk.</p>');


INSERT INTO `contents` (`type`, `category`, `url`, `author`, `title`, `description`, `body`)
VALUES
(2, 2, 'dubai', 3, 'Dubai', 'Szinte ingyen Dubaiba',
'<img src="/upload/images/dubai.jpg" class="landscape"><p>Lórum ipse mint buggyos izgatlan
térő, elsősorban egy hatos fice. A szált csánszokat is sedheti a pubrozás: a besztenség körül
gyorsan tekülő delő kesítő őszít fel, majd vitetnek a pubrozás csíros, lekelmezeges
mező, hitves cserenc szörpei. A sparc a svutákon hatlan, feddő, fulan úgynevezett
jermeteken, valamint a svuták csapinusain dakarsodik át, és az ott korcos parkáival
tele és szonárok lemlésével dékozol a svuták retérőjére. A koláson a fogár szapácsára
a parkák hirkelnek, majd a koláson keresztül a pördőbe, a billába és a molyhos
pulákba hahomoznak. Trajájukra a molyhos pulák egyes művete csistal. A rező anció
fattyúk a kovel kerces feletbe szángnak, és egyzet és pártban rutosak. Szenészük csalhan
juhányot nem leremzsen, báványos bazálokban is melő. A melő fattyúk csinvillákra
nem lesztők, így zatírázskor is hatosak.</p>
<img src="/upload/images/dubai-hotel.jpg" class="landscape">
<p>A marcos róna szályája és üvége során a cselendő dikelegleseket kell kaslazságba hibolcsuknia:
Luett a bogós alanokra ratott berzetség pelt szonyákat és tektásokat. (3) a marcos alanok
vénykezésénél buzig pacolásba hibolcsuknia a fogság marcos rónára pikkelyes alanokat,
melyek rónáját e egyező kapilva belis csulája ficeli. (4) a marcos rónán előre padtak
azok az alanok, amelyeknek külön szonya kelységei szerint heten klusuk lehet, elsősorban
amelyek tudottan vagy feltételezetten sítos, csehely, étletletes stazás vagy bestet tányos
solytatók, illetve amelyek tudottan vagy feltételezetten szándják a szelen klusok bukálánának
gványát. Lengesz szara (1) a marcos alanokra vonatkozóan azoknak a bébecseleknek és fegyendeknek,
akik e egyező szerint grómokat hólyázódtak szegely alanról, a marcos róna rofogától pedő hat
kítőn belül a sutokozás részére be kell sánulniuk az alannal bizatlan hadékra ratott talmatos
tozásra folygó grómot, illetve ratott pornerót. (2) Az (1) hombort fatlan híváson túlmenően,
amennyiben a bogós alanok bőrözére ratott külön szonyában ermeli bármely hatlan szegely, a
marcos rónán többes alannal tramzátban nem selyez tozásra, azoknak a bébecseleknek és fegyendeknek,
akik e egyezőnek megfelelően grómokat hólyázódtak az alanról, el kell dokonálniuk azokat a bőrözöket,
amelyek a julkozás pirségek vordjához egesek. A völölő emlőket, valamint a völölő madruccot a sutokozás
részére 12 kítőn belül be kell sánulniuk.</p>'),

(2, 2, 'napfeny-tura', 1, 'Napfény túra', 'Akciós napfény, minden mennyiségben, az UV sugárzás ajándék!',
'<img src="/upload/images/rock-formation.jpg" class="landscape"><p>Lórum ipse mint buggyos izgatlan
térő, elsősorban egy hatos fice. A szált csánszokat is
sedheti a pubrozás: a besztenség körül gyorsan tekülő delő kesítő őszít fel, majd vitetnek
a pubrozás csíros, lekelmezeges mező, hitves cserenc szörpei. A sparc a svutákon hatlan,
feddő, fulan úgynevezett jermeteken, valamint a svuták csapinusain dakarsodik át, és az
ott korcos parkáival tele és szonárok lemlésével dékozol a svuták retérőjére. A koláson
a fogár szapácsára a parkák hirkelnek, majd a koláson keresztül a pördőbe, a billába és
a molyhos pulákba hahomoznak. Trajájukra a molyhos pulák egyes művete csistal. A rező anció
fattyúk a kovel kerces feletbe szángnak, és egyzet és pártban rutosak. Szenészük csalhan
juhányot nem leremzsen, báványos bazálokban is melő. A melő fattyúk csinvillákra nem lesztők,
így zatírázskor is hatosak.</p>
<img src="/upload/images/desert.jpg" class="landscape">
<p>A marcos róna szályája és üvége során a cselendő dikelegleseket kell kaslazságba hibolcsuknia:
Luett a bogós alanokra ratott berzetség pelt szonyákat és tektásokat. (3) a marcos alanok
vénykezésénél buzig pacolásba hibolcsuknia a fogság marcos rónára pikkelyes alanokat,
melyek rónáját e egyező kapilva belis csulája ficeli. (4) a marcos rónán előre padtak
azok az alanok, amelyeknek külön szonya kelységei szerint heten klusuk lehet, elsősorban
amelyek tudottan vagy feltételezetten sítos, csehely, étletletes stazás vagy bestet tányos
solytatók, illetve amelyek tudottan vagy feltételezetten szándják a szelen klusok bukálánának
gványát. Lengesz szara (1) a marcos alanokra vonatkozóan azoknak a bébecseleknek és fegyendeknek,
akik e egyező szerint grómokat hólyázódtak szegely alanról, a marcos róna rofogától pedő hat
kítőn belül a sutokozás részére be kell sánulniuk az alannal bizatlan hadékra ratott talmatos
tozásra folygó grómot, illetve ratott pornerót. (2) Az (1) hombort fatlan híváson túlmenően,
amennyiben a bogós alanok bőrözére ratott külön szonyában ermeli bármely hatlan szegely, a
marcos rónán többes alannal tramzátban nem selyez tozásra, azoknak a bébecseleknek és fegyendeknek,
akik e egyezőnek megfelelően grómokat hólyázódtak az alanról, el kell dokonálniuk azokat a bőrözöket,
amelyek a julkozás pirségek vordjához egesek. A völölő emlőket, valamint a völölő madruccot a sutokozás
részére 12 kítőn belül be kell sánulniuk.</p>'),

(2, 2, 'oszi-erdo', 2, 'Őszi erdő', 'Őszi erdők felfedezése, kívánság szerint bármely évszakban',
'<img src="/upload/images/fall-forest.jpg" class="landscape"><p>Lórum ipse mint buggyos izgatlan
térő, elsősorban egy hatos fice. A szált csánszokat is sedheti a pubrozás: a besztenség körül
gyorsan tekülő delő kesítő őszít fel, majd vitetnek a pubrozás csíros, lekelmezeges
mező, hitves cserenc szörpei. A sparc a svutákon hatlan, feddő, fulan úgynevezett
jermeteken, valamint a svuták csapinusain dakarsodik át, és az ott korcos parkáival
tele és szonárok lemlésével dékozol a svuták retérőjére. A koláson a fogár szapácsára
a parkák hirkelnek, majd a koláson keresztül a pördőbe, a billába és a molyhos
pulákba hahomoznak. Trajájukra a molyhos pulák egyes művete csistal. A rező anció
fattyúk a kovel kerces feletbe szángnak, és egyzet és pártban rutosak. Szenészük csalhan
juhányot nem leremzsen, báványos bazálokban is melő. A melő fattyúk csinvillákra
nem lesztők, így zatírázskor is hatosak.</p>
<img src="/upload/images/forest-sunset.jpg" class="landscape">
<p>A marcos róna szályája és üvége során a cselendő dikelegleseket kell kaslazságba hibolcsuknia:
Luett a bogós alanokra ratott berzetség pelt szonyákat és tektásokat. (3) a marcos alanok
vénykezésénél buzig pacolásba hibolcsuknia a fogság marcos rónára pikkelyes alanokat,
melyek rónáját e egyező kapilva belis csulája ficeli. (4) a marcos rónán előre padtak
azok az alanok, amelyeknek külön szonya kelységei szerint heten klusuk lehet, elsősorban
amelyek tudottan vagy feltételezetten sítos, csehely, étletletes stazás vagy bestet tányos
solytatók, illetve amelyek tudottan vagy feltételezetten szándják a szelen klusok bukálánának
gványát. Lengesz szara (1) a marcos alanokra vonatkozóan azoknak a bébecseleknek és fegyendeknek,
akik e egyező szerint grómokat hólyázódtak szegely alanról, a marcos róna rofogától pedő hat
kítőn belül a sutokozás részére be kell sánulniuk az alannal bizatlan hadékra ratott talmatos
tozásra folygó grómot, illetve ratott pornerót. (2) Az (1) hombort fatlan híváson túlmenően,
amennyiben a bogós alanok bőrözére ratott külön szonyában ermeli bármely hatlan szegely, a
marcos rónán többes alannal tramzátban nem selyez tozásra, azoknak a bébecseleknek és fegyendeknek,
akik e egyezőnek megfelelően grómokat hólyázódtak az alanról, el kell dokonálniuk azokat a bőrözöket,
amelyek a julkozás pirségek vordjához egesek. A völölő emlőket, valamint a völölő madruccot a sutokozás
részére 12 kítőn belül be kell sánulniuk.</p>'),

(2, 2, 'tengerparti-oromok', 3, 'Tengerparti örömök', 'A sparc a svutákon hatlan, feddő, fulan úgynevezett
jermeteken, valamint a svuták csapinusain dakarsodik át',
'<img src="/upload/images/beach-landscape.jpg" class="landscape"><p>Lórum ipse mint buggyos izgatlan
térő, elsősorban egy hatos fice. A szált csánszokat is sedheti a pubrozás: a besztenség körül
gyorsan tekülő delő kesítő őszít fel, majd vitetnek a pubrozás csíros, lekelmezeges
mező, hitves cserenc szörpei. A sparc a svutákon hatlan, feddő, fulan úgynevezett
jermeteken, valamint a svuták csapinusain dakarsodik át, és az ott korcos parkáival
tele és szonárok lemlésével dékozol a svuták retérőjére. A koláson a fogár szapácsára
a parkák hirkelnek, majd a koláson keresztül a pördőbe, a billába és a molyhos
pulákba hahomoznak. Trajájukra a molyhos pulák egyes művete csistal. A rező anció
fattyúk a kovel kerces feletbe szángnak, és egyzet és pártban rutosak. Szenészük csalhan
juhányot nem leremzsen, báványos bazálokban is melő. A melő fattyúk csinvillákra
nem lesztők, így zatírázskor is hatosak.</p>
<img src="/upload/images/beach-sea.jpg" class="landscape">
<p>A marcos róna szályája és üvége során a cselendő dikelegleseket kell kaslazságba hibolcsuknia:
Luett a bogós alanokra ratott berzetség pelt szonyákat és tektásokat. (3) a marcos alanok
vénykezésénél buzig pacolásba hibolcsuknia a fogság marcos rónára pikkelyes alanokat,
melyek rónáját e egyező kapilva belis csulája ficeli. (4) a marcos rónán előre padtak
azok az alanok, amelyeknek külön szonya kelységei szerint heten klusuk lehet, elsősorban
amelyek tudottan vagy feltételezetten sítos, csehely, étletletes stazás vagy bestet tányos
solytatók, illetve amelyek tudottan vagy feltételezetten szándják a szelen klusok bukálánának
gványát. Lengesz szara (1) a marcos alanokra vonatkozóan azoknak a bébecseleknek és fegyendeknek,
akik e egyező szerint grómokat hólyázódtak szegely alanról, a marcos róna rofogától pedő hat
kítőn belül a sutokozás részére be kell sánulniuk az alannal bizatlan hadékra ratott talmatos
tozásra folygó grómot, illetve ratott pornerót. (2) Az (1) hombort fatlan híváson túlmenően,
amennyiben a bogós alanok bőrözére ratott külön szonyában ermeli bármely hatlan szegely, a
marcos rónán többes alannal tramzátban nem selyez tozásra, azoknak a bébecseleknek és fegyendeknek,
akik e egyezőnek megfelelően grómokat hólyázódtak az alanról, el kell dokonálniuk azokat a bőrözöket,
amelyek a julkozás pirségek vordjához egesek. A völölő emlőket, valamint a völölő madruccot a sutokozás
részére 12 kítőn belül be kell sánulniuk.</p>'),

(2, 3, 'hegyvideki-kirandulas', 1, 'Hegyvidéki kirándulás', 'Hegy és tó nélkül most féláron!',
'<img src="/upload/images/lake-house.jpg" class="landscape"><p>Lórum ipse mint buggyos izgatlan
térő, elsősorban egy hatos fice. A szált csánszokat is sedheti a pubrozás: a besztenség körül
gyorsan tekülő delő kesítő őszít fel, majd vitetnek a pubrozás csíros, lekelmezeges
mező, hitves cserenc szörpei. A sparc a svutákon hatlan, feddő, fulan úgynevezett
jermeteken, valamint a svuták csapinusain dakarsodik át, és az ott korcos parkáival
tele és szonárok lemlésével dékozol a svuták retérőjére. A koláson a fogár szapácsára
a parkák hirkelnek, majd a koláson keresztül a pördőbe, a billába és a molyhos
pulákba hahomoznak. Trajájukra a molyhos pulák egyes művete csistal. A rező anció
fattyúk a kovel kerces feletbe szángnak, és egyzet és pártban rutosak. Szenészük csalhan
juhányot nem leremzsen, báványos bazálokban is melő. A melő fattyúk csinvillákra
nem lesztők, így zatírázskor is hatosak.</p>
<img src="/upload/images/sky-mountainous.jpg" class="landscape">
<p>A marcos róna szályája és üvége során a cselendő dikelegleseket kell kaslazságba hibolcsuknia:
Luett a bogós alanokra ratott berzetség pelt szonyákat és tektásokat. (3) a marcos alanok
vénykezésénél buzig pacolásba hibolcsuknia a fogság marcos rónára pikkelyes alanokat,
melyek rónáját e egyező kapilva belis csulája ficeli. (4) a marcos rónán előre padtak
azok az alanok, amelyeknek külön szonya kelységei szerint heten klusuk lehet, elsősorban
amelyek tudottan vagy feltételezetten sítos, csehely, étletletes stazás vagy bestet tányos
solytatók, illetve amelyek tudottan vagy feltételezetten szándják a szelen klusok bukálánának
gványát. Lengesz szara (1) a marcos alanokra vonatkozóan azoknak a bébecseleknek és fegyendeknek,
akik e egyező szerint grómokat hólyázódtak szegely alanról, a marcos róna rofogától pedő hat
kítőn belül a sutokozás részére be kell sánulniuk az alannal bizatlan hadékra ratott talmatos
tozásra folygó grómot, illetve ratott pornerót. (2) Az (1) hombort fatlan híváson túlmenően,
amennyiben a bogós alanok bőrözére ratott külön szonyában ermeli bármely hatlan szegely, a
marcos rónán többes alannal tramzátban nem selyez tozásra, azoknak a bébecseleknek és fegyendeknek,
akik e egyezőnek megfelelően grómokat hólyázódtak az alanról, el kell dokonálniuk azokat a bőrözöket,
amelyek a julkozás pirségek vordjához egesek. A völölő emlőket, valamint a völölő madruccot a sutokozás
részére 12 kítőn belül be kell sánulniuk.</p>'),

(2, 3, 'extra-szilkas-hegyek', 2, 'Extra sziklás hegyek', 'Ha kevés a szikla, a Dolomit Kőbányászati Kft.
készletéből pótoljuk!',
'<img src="/upload/images/forest-rock-waterfall.jpg" class="landscape"><p>Lórum ipse mint buggyos izgatlan
térő, elsősorban egy hatos fice. A szált csánszokat is sedheti a pubrozás: a besztenség körül
gyorsan tekülő delő kesítő őszít fel, majd vitetnek a pubrozás csíros, lekelmezeges
mező, hitves cserenc szörpei. A sparc a svutákon hatlan, feddő, fulan úgynevezett
jermeteken, valamint a svuták csapinusain dakarsodik át, és az ott korcos parkáival
tele és szonárok lemlésével dékozol a svuták retérőjére. A koláson a fogár szapácsára
a parkák hirkelnek, majd a koláson keresztül a pördőbe, a billába és a molyhos
pulákba hahomoznak. Trajájukra a molyhos pulák egyes művete csistal. A rező anció
fattyúk a kovel kerces feletbe szángnak, és egyzet és pártban rutosak. Szenészük csalhan
juhányot nem leremzsen, báványos bazálokban is melő. A melő fattyúk csinvillákra
nem lesztők, így zatírázskor is hatosak.</p>
<img src="/upload/images/mountain-light.jpg" class="landscape">
<p>A marcos róna szályája és üvége során a cselendő dikelegleseket kell kaslazságba hibolcsuknia:
Luett a bogós alanokra ratott berzetség pelt szonyákat és tektásokat. (3) a marcos alanok
vénykezésénél buzig pacolásba hibolcsuknia a fogság marcos rónára pikkelyes alanokat,
melyek rónáját e egyező kapilva belis csulája ficeli. (4) a marcos rónán előre padtak
azok az alanok, amelyeknek külön szonya kelységei szerint heten klusuk lehet, elsősorban
amelyek tudottan vagy feltételezetten sítos, csehely, étletletes stazás vagy bestet tányos
solytatók, illetve amelyek tudottan vagy feltételezetten szándják a szelen klusok bukálánának
gványát. Lengesz szara (1) a marcos alanokra vonatkozóan azoknak a bébecseleknek és fegyendeknek,
akik e egyező szerint grómokat hólyázódtak szegely alanról, a marcos róna rofogától pedő hat
kítőn belül a sutokozás részére be kell sánulniuk az alannal bizatlan hadékra ratott talmatos
tozásra folygó grómot, illetve ratott pornerót. (2) Az (1) hombort fatlan híváson túlmenően,
amennyiben a bogós alanok bőrözére ratott külön szonyában ermeli bármely hatlan szegely, a
marcos rónán többes alannal tramzátban nem selyez tozásra, azoknak a bébecseleknek és fegyendeknek,
akik e egyezőnek megfelelően grómokat hólyázódtak az alanról, el kell dokonálniuk azokat a bőrözöket,
amelyek a julkozás pirségek vordjához egesek. A völölő emlőket, valamint a völölő madruccot a sutokozás
részére 12 kítőn belül be kell sánulniuk.</p>'),

(2, 3, 'garantaltan-suru-sotet-erdo', 2, 'Garantáltan sűrű, sötét erdő', 'Kellemes pihenés,
távol a világ zajától, most akciós jetivel!',
'<img src="/upload/images/forest-waterfall.jpg" class="landscape"><p>Lórum ipse mint buggyos izgatlan
térő, elsősorban egy hatos fice. A szált csánszokat is sedheti a pubrozás: a besztenség körül
gyorsan tekülő delő kesítő őszít fel, majd vitetnek a pubrozás csíros, lekelmezeges
mező, hitves cserenc szörpei. A sparc a svutákon hatlan, feddő, fulan úgynevezett
jermeteken, valamint a svuták csapinusain dakarsodik át, és az ott korcos parkáival
tele és szonárok lemlésével dékozol a svuták retérőjére. A koláson a fogár szapácsára
a parkák hirkelnek, majd a koláson keresztül a pördőbe, a billába és a molyhos
pulákba hahomoznak. Trajájukra a molyhos pulák egyes művete csistal. A rező anció
fattyúk a kovel kerces feletbe szángnak, és egyzet és pártban rutosak. Szenészük csalhan
juhányot nem leremzsen, báványos bazálokban is melő. A melő fattyúk csinvillákra
nem lesztők, így zatírázskor is hatosak.</p>
<img src="/upload/images/forest-dark.jpg" class="landscape">
<p>A marcos róna szályája és üvége során a cselendő dikelegleseket kell kaslazságba hibolcsuknia:
Luett a bogós alanokra ratott berzetség pelt szonyákat és tektásokat. (3) a marcos alanok
vénykezésénél buzig pacolásba hibolcsuknia a fogság marcos rónára pikkelyes alanokat,
melyek rónáját e egyező kapilva belis csulája ficeli. (4) a marcos rónán előre padtak
azok az alanok, amelyeknek külön szonya kelységei szerint heten klusuk lehet, elsősorban
amelyek tudottan vagy feltételezetten sítos, csehely, étletletes stazás vagy bestet tányos
solytatók, illetve amelyek tudottan vagy feltételezetten szándják a szelen klusok bukálánának
gványát. Lengesz szara (1) a marcos alanokra vonatkozóan azoknak a bébecseleknek és fegyendeknek,
akik e egyező szerint grómokat hólyázódtak szegely alanról, a marcos róna rofogától pedő hat
kítőn belül a sutokozás részére be kell sánulniuk az alannal bizatlan hadékra ratott talmatos
tozásra folygó grómot, illetve ratott pornerót. (2) Az (1) hombort fatlan híváson túlmenően,
amennyiben a bogós alanok bőrözére ratott külön szonyában ermeli bármely hatlan szegely, a
marcos rónán többes alannal tramzátban nem selyez tozásra, azoknak a bébecseleknek és fegyendeknek,
akik e egyezőnek megfelelően grómokat hólyázódtak az alanról, el kell dokonálniuk azokat a bőrözöket,
amelyek a julkozás pirségek vordjához egesek. A völölő emlőket, valamint a völölő madruccot a sutokozás
részére 12 kítőn belül be kell sánulniuk.</p>');


CREATE TABLE `images` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `url` varchar(128) NOT NULL,
  `mime` varchar(64) NOT NULL,
  `size` mediumint(4) UNSIGNED NOT NULL,
  `width` smallint(5) UNSIGNED NOT NULL,
  `height` smallint(5) UNSIGNED NOT NULL,
  `content` smallint(5) UNSIGNED DEFAULT NULL,
  `created` timestamp DEFAULT current_timestamp(),
  `updated` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`content`) REFERENCES `contents` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii;


INSERT INTO `images` (`url`, `mime`, `size`, `width`, `height`, `content`) VALUES
('road.jpg', 'image/jpeg', 472, 1617, 1080, 1),
('beach-sunshine-car.jpg', 'image/jpeg', 697, 1812, 1200, 1),
('travel-meeting.jpg', 'image/jpeg', 262, 1920, 750, 2),
('postbox.jpg', 'image/jpeg', 815, 1512, 1080, 3),
('dubai.jpg', 'image/jpeg', 450, 1623, 1080, 4),
('dubai-hotel.jpg', 'image/jpeg', 454, 1920, 1042, 4),
('rock-formation.jpg', 'image/jpeg', 788, 1620, 1080, 5),
('desert.jpg', 'image/jpeg', 398, 1620, 1080, 5),
('fall-forest.jpg', 'image/jpeg', 1229, 1620, 1080, 6),
('forest-sunset.jpg', 'image/jpeg', 459, 1620, 1080, 6),
('beach-landscape.jpg', 'image/jpeg', 647, 1623, 1080, 7),
('beach-sea.jpg', 'image/jpeg', 683, 1920, 1080, 7),
('lake-house.jpg', 'image/jpeg', 745, 1680, 1080, 8),
('sky-mountainous.jpg', 'image/jpeg', 336, 1620, 1080, 8),
('forest-rock-waterfall.jpg', 'image/jpeg', 630, 1920, 1080, 9),
('mountain-light.jpg', 'image/jpeg', 664, 1512, 1080, 9),
('forest-waterfall.jpg', 'image/jpeg', 823, 1574, 1050, 10),
('forest-dark.jpg', 'image/jpeg', 187, 1620, 1080, 10);
