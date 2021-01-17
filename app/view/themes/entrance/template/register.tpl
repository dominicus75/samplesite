  <form method="post" action="{{url}}">
    <fieldset>
      <legend>{{title}}</legend>
      <ul>
        <li>
          <i class="fa fa-user"></i>
          <input type="text" name="name" id="name" placeholder="Felhasználónév" value="">
        </li>
        <li>
          <i class="fa fa-envelope"></i>
          <input type="email" name="email" id="email" placeholder="E-mail cím" value="">
        </li>
        <li>
          <i class="fa fa-lock"></i>
          <input type="password" name="pass" id="pass" placeholder="Jelszó" value="">
        </li>
        <li>
          <i class="fa fa-repeat"></i>
          <input type="password" name="repass" id="repass" placeholder="Jelszó ismét" value="">
        </li>
        <li>
          <input type="submit" name="submit" value="Küldés">
        </li>
      </ul>
    </fieldset>
  </form>