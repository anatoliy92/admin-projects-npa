$(document).ready(function() {
  $('body').on('click', '.remove--npa', function(e) {
    e.preventDefault();
    var id = $(this).attr('data-id');
    var section = $(this).attr('data-section');

    $.ajax({
      url: '/sections/' + section + '/npa/' + id,
      type: 'DELETE',
      dataType: 'json',
      data : { _token: $('meta[name="_token"]').attr('content')},
      success: function(data) {
        if (data.success) {
          $("#npa--item-" + id).remove();
          messageSuccess(data.success);
        } else {
          messageError(data.errors);
        }
      }
    });
  });
});
