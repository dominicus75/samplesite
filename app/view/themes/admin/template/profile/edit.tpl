    <div id="edit">
      <h1>{{title}}</h1>
      @@message@@
      <form action="/{{role}}/ajax/post/avatar.html" method="post" id="avatarUpload" enctype="multipart/form-data">
        <div class="file">
          <label for="avatar"><img src="/upload/images/{{avatar}}" alt="avatar" title="Avatár kép kiválasztása" id="preview"></label>
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
            <input type="text" name="name" id="name" value="{{user}}">
          </li>
          <li>
            <label for="email" title="E-mail cím"><i class="fa fa-envelope"></i></label>
            <input type="text" name="email" id="email" value="{{email}}">
          </li>
          <li>
            <label for="oldpass" title="Régi jelszó"><i class="fa fa-lock"></i></label>
            <input type="password" name="oldpass" id="oldpass" placeholder="Régi jelszó" value="">
          </li>
          <li>
            <label for="pass" title="Új jelszó"><i class="fa fa-lock"></i></label>
            <input type="password" name="pass" id="pass" placeholder="Új jelszó" value="">
          </li>
          <li>
            <label for="repass" title="Új jelszó megerősítése"><i class="fa fa-repeat"></i></label>
            <input type="password" name="repass" id="repass" placeholder="Új jelszó megerősítése" value="">
          </li>
          <li>
            <input type="submit" name="submit" value="Mentés">
          </li>
        </ul>
      </form>
    </div>
