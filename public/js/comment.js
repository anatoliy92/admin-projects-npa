$(document).ready(function() {

  $('body').on('click', '.remove--comment', function(e) {
    e.preventDefault();
    var id = $(this).attr('data-id');

    $.ajax({
      url: '/sections/' + section + '/npa/comment/remove/' + id,
      type: 'GET',
      dataType: 'json',
      data : { _token: $('meta[name="_token"]').attr('content')},
      success: function(data) {
        if (data.success) {
          $("#comment--item-" + id).remove();
          messageSuccess(data.success);
        } else {
          messageError(data.errors);
        }
      }
    });
  });
});
