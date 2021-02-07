    <div id="edit">
      <h1>{{title}}</h1>
      @@message@@
      <form action="/{{role}}/ajax/post/avatar.html" method="post" id="avatarUpload" enctype="multipart/form-data">
        <div class="file">
          <label for="avatar"><img src="/images/default_avatar.png" alt="avatar" title="Avatár kép kiválasztása" id="preview"></label>
          <input type="file" name="avatar" id="avatar" accept="image/*">
          <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
        </div>
        <div class="submit"><input type="submit" name="upload" id="upload" value="Avatar feltöltés"></div>
      </form>
      <form method="post" id="editProfile" action="{{url}}">
        <ul>
          <li id="addAvatar">
          </li>
          <li>
            <label for="name" title="Felhasználónév"><i class="fa fa-user"></i></label>
            <input type="text" name="name" id="name" placeholder="Felhasználónév" value="">
          </li>
          <li>
            <label for="email" title="E-mail cím"><i class="fa fa-envelope"></i></label>
            <input type="text" name="email" id="email" placeholder="E-mail cím" value="">
          </li>
          <li>
            <label for="rank" title="Rang"><i class="fa fa-users"></i></label>
            <select name="rank" id="rank">
@@options@@
            </select>
          </li>
          <li>
            <label for="pass" title="Jelszó"><i class="fa fa-lock"></i></label>
            <input type="password" name="pass" id="pass" placeholder="Jelszó" value="">
          </li>
          <li>
            <label for="repass" title="Jelszó megerősítése"><i class="fa fa-repeat"></i></label>
            <input type="password" name="repass" id="repass" placeholder="Jelszó megerősítése" value="">
          </li>
          <li>
            <input type="submit" name="submit" value="Létrehozás">
          </li>
        </ul>
      </form>
    </div>
