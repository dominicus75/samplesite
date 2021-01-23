
  $(function() {
    $("input:file").change(function (){
      $(this).html(this.files[0].name);
      $("#preview").attr("src", URL.createObjectURL(this.files[0]));
      $("div.danger").hide();
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
          $("div.file").show();
        },
      });
    }));
  });

