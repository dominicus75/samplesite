  <form method="post" action="{{url}}">
    <fieldset>
      <legend>{{title}}</legend>
      <ul>
        <li>
          <i class="fa fa-envelope"></i>
          <input type="email" name="email" id="email" placeholder="E-mail cím" value="">
        </li>
        <li>
          <i class="fa fa-lock"></i>
          <input type="password" name="pass" id="pass" placeholder="Jelszó" value="">
        </li>
        <li>
          <input type="submit" name="submit" value="Belépés">
        </li>
      </ul>
    </fieldset>
  </form>
