
  <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
  <script type="text/javascript">

  $(function() {
    $("input:file").change(function (){
      $(this).html(this.files[0].name);
      $("#preview").attr("src", URL.createObjectURL(this.files[0]));
      $("div.submit").fadeIn(1000);
    });
  });

  $(document).ready(function (e) {
    $("#avatarUpload").on('submit',(function(e) {
      e.preventDefault();
      $.ajax({
        url: $(this).attr("action"),
        type: "POST",
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData:false,
        success: function(data) {
          $('#addAvatar').html(data);
          $('#avatarUpload').hide();
          $("div.submit").hide();
          $("div.file").show();
        },
      });
    }));
  });

  </script>