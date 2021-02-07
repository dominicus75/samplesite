  <form method="post" action="{{url}}">
    <fieldset>
      <legend>{{title}}</legend>
      <ul>
        <li>
          <i class="fa fa-database"></i>
          <input type="text" name="dbname" id="dbname" placeholder="Adatbázisnév" value="">
        </li>
        <li>
          <i class="fa fa-database"></i>
          <input type="text" name="host" id="host" placeholder="Adatbázisszerver" value="">
        </li>
        <li>
          <i class="fa fa-user"></i>
          <input type="text" name="username" id="username" placeholder="Felhasználónév" value="">
        </li>
        <li>
          <i class="fa fa-lock"></i>
          <input type="password" name="password" id="password" placeholder="Jelszó" value="">
        </li>
        <li>
          <input type="submit" name="submit" value="Tovább">
        </li>
      </ul>
    </fieldset>
  </form>
