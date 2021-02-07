    <article>
      <h1><i class="fa fa-wrench"></i>&nbsp;&nbsp;Üdv a Samplesite telepítőjében!&nbsp;&nbsp;<i class="fa fa-wrench"></i></h1>
      <p>Jelen bemutató oldal kizárólag szabad szoftver felhasználásával készült.
      IDE: Geany (v. 1.33), grafikai program: GIMP (v 2.10.8), operációs rendszer: Debian Linux 10 (Buster).</p>
      <p>Alkalmazott nyelvek: PHP 7.4, HTML 5, CSS 3</p>
      <p>Alkalmazott programtervezési minták: MVC, Front Controller, Singleton</p>
      <p>Figyelembe vett szabványok: PSR-4 (autoloader), PSR-7 (HTTP-üzenetek), PSR-12 (Kódstílus útmutató)</p>
      <h2>A program felépítése</h2>
      <p>A projekt gyökérkönyvtárában három könyvtár található: a tulajdonképpeni
      programot tartalmazó <strong>app</strong>, a web felől látható elemeket (képek,
      index.php) magában foglaló <strong>public</strong> (a $_SERVER['DOCUMENT_ROOT'] környezeti változónak
      ide kell mutatnia) és az alap osztályokat tartalmazó <strong>vendor</strong>
      (ez nagyjából a kiírás 'classes' mappájának felel meg). Ez a felépítés a
      Composer által létrehozott és általánosan bevett struktúrára hajaz (persze Composer nélkül, hogy
      ne bonyolítsuk a kelleténél jobban a történetet). A nem publikus mappák (app és vendor)
      egy-egy külön <em>.htaccess</em> állománnyal vannak védve a nem kívánt érdelődőktől,
      a public mappában található <em>.htaccess</em> feladata pedig a bejövő - a valóságban nem létező -
      tartalomra irányuló http-kérések átirányítása az index.php-ra (ami az oldal
      egyetlen belépési pontjaként szolgál, a Front Controller mintának megfelelően).</p>
      <h2>A program működése</h2>
      <p>Az index.php (miután beinclude-olta az autoloadert és a konstansokat)
      példányosítja és futtatja a <strong>Application\Samplesite</strong> osztályt, ami
      a tulajdonképpeni Front Controllerként szolgál. A Samplesite konstruktora
      a rendelkezésre álló szuperglobálisokat átadja a <strong>Request</strong> osztály
      konstruktorának. Ezután létrejün egy-egy példány a <strong>Response</strong>,
      <strong>Router</strong> és az <strong>Authority</strong> osztályokból is.
      A Samplesite osztály inicializációjának lezárásaként - a Router által biztosított Route példány
      segítségével - létrejön a meghívott Controller egy példányai is (a Router előzőleg
      ellenőrzi, hogy létezik-e maga a meghívott Controller, rendelkezik-e a kért metódussal
      és a hivónak van-e jogosultsága ehhez hozzáférni - mindez az app/config/router.php
      konfigurációs állományban van megénekelve).</p>
      <p>A példányosított Controller a <strong>Application\Core\AbstractController</strong>
      absztrakt osztály leszármazottja. Ez hozza létre a szükséges <strong>model</strong>
      osztályt (ami a <strong>\Dominicus75\Model\Entity</strong> osztály egy példánya).
      A nézetet (ami a <strong>\Dominicus75\Templater\RenderableSource</strong> osztályból származik,
      közvetlenül vagy közvetve) már a leszármazott Controller példányosítja.</p>
      <p>Az inicializálás után fut le a Samplesite::run() metódusa, ami újfent
      ellenőrzi a jogosultságokat és ha a kért oldal megtekintése azonosításhoz
      kötött, akkor átirányítja a látogatót a login oldalra. Végezetül a Controllertől kapott
      tartalmat átadja a Response példánynak, ami elküldi azt a kliensnek.</p>
      <p>Az alábbi hivatkozásra kattintva megkezdhető a program telepítése:</p>
      <a href="/admin/install/database.html">Adatbáziskapcsolat beállítása</a>
    </article>