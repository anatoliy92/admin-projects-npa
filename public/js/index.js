$(document).ready(function() {

  $('.datetimepicker').datetimepicker({
    lang: 'ru',
  	formatTime:'H:i',
  	formatDate:'Y-m-d',
  	step: 5,
  	timepickerScrollbar:true,
    onChangeDateTime:function(dp,$input) {

      var newDate = dateFormat(dp, "yyyy-mm-dd HH:MM");

      $.ajax({
        url: '/ajax/change-npa-date/' + $input.attr('data-id'),
        type: 'POST',
        dataType: 'json',
        data : {
          _token: $('meta[name="_token"]').attr('content'),
          published: newDate
        },
        success: function(data) {
          if (data.success) {
            $input.parents('.change--datetime').find('span').html(data.published);
            $input.val(data.published);
            messageSuccess(data.success);
          } else {
            messageError(data.errors);
          }
        }
      });
    }
  });

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
