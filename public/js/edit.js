$(document).ready(function() {

	/* Загрузка медиа объектов */
	$('#upload-photos').uploadifive({
		'auto'			: true,
		'removeCompleted' : true,
		'simUploadLimit' : 1,
		'buttonText'	: 'Выберите Изображение',
		'height'	    : '100%',
		'width'			: '100%',
		'checkScript'	: '/ajax/check',
		'uploadScript'	: '/ajax/npa-images',
		'fileType'		: 'image/*',
		'formData'		: {
			'_token'      : $('meta[name="_token"]').attr('content'),
			'section_id'  : $('#section_id').val(),
			'npa_id'	  : $('#model-id').val()
		},
		'folder'		: '/uploads/tmps/',

		'onUploadComplete' : function( file, data ) {
			var $data = JSON.parse(data);
			if ($data.success) {
				var html =
					'<li id="mediaSortable_' + $data.file.id + '" class="col-6 col-sm-4 col-xl-3 col-xxl-2 ui-sortable-handle">'+
					'<div class="card card-stat">' +
					'<div class="card-header">' +
					'<div class="row">' +
					'<div class="col-4 text-left"> <a href="#" class="change--status" data-model="App\\Models\\Media" data-id="' + $data.file.id + '"><i class="fa fa-eye"></i></a> </div>' +
					'<div class="col-4 text-center"> <a href="#" class="toMainPhoto" data-model="Media" data-id="' + $data.file.id + '"><i class="fa fa-circle-o"></i></a> </div>' +
					'<div class="col-4 text-right"> <a href="" class="change--lang" data-id="' + $data.file.id + '"><img src="/avl/img/icons/flags/'+ ( $data.file.lang ? $data.file.lang : 'null' ) +'--16.png"></a> </div>' +
					'</div>' +
					'</div>' +
					'<div class="card-body p-0"><img src="/image/resize/200/190/' + $data.file.url + '"></div>'+
					'<div class="card-footer">' +
					'<div class="row">' +
					'<div class="col-6 text-left"><a href="#" class="deleteMedia" data-id="' + $data.file.id + '"><i class="fa fa-trash-o"></i></a></div>' +
					'<div class="col-6 text-right"><a href="#" class="open--modal-translates" data-id="' + $data.file.id + '" data-toggle="modal" data-target="#translates-modal"><i class="fa fa-pencil"></i></a></div>' +
					'</div>' +
					'</div>' +
					'</div>' +
					'</li>';
				$('#sortable').prepend(html);
			}

			if ($data.errors) {
				messageError($data.errors);
			}
		}
	});

	$('#upload-files').uploadifive({
		'auto'			: true,
		'removeCompleted' : true,
		'buttonText'	: 'Выберите файл для загрузки',
		'height'	    : '100%',
		'width'			: '100%',
		'checkScript'	: '/ajax/check',
		'uploadScript'	: '/ajax/npa-files',
		'folder'		: '/uploads/tmps/',
		'onUpload'     : function(filesToUpload) {
			$('#upload-files').data('uploadifive').settings.formData = {
				'_token'      : $('meta[name="_token"]').attr('content'),
				'section_id'  : $('#section_id').val(),
				'npa_id'	  : $('#model-id').val(),
				'type'		  : $('#npa_type').val(),
				'lang'        : $("#select--language-file").val()
			};
		},
		'onUploadComplete' : function( file, data ) {
			var $data = JSON.parse(data);
			if ($data.success) {
				$(".change-main-file." + $("#select--language-file").val()).find('.fa').removeClass('fa-star').addClass('fa-star-o');

				$('#sortable-files').prepend($data.html);

				$('.datepicker').datepicker(globalDatePickerConfig);
				$('.timepicker').timepicker(globalTimePickerConfig);

			}

			if ($data.errors) {
				messageError($data.errors);
			}
		}
	});
	/* Загрузка медиа объектов */

	$("body").on('click', '.change--updated-date', function (e) {
		if ($(this).is(':checked')) {
			$('.updated--date').attr({'disabled': false});
		} else {
			$('.updated--date').attr({'disabled': true});
		}
	});
	$("body").on('click', '.change--until-date', function (e) {
		if ($(this).is(':checked')) {
			$('.until--date').attr({'disabled': false});
		} else {
			$('.until--date').attr({'disabled': true});
		}
	});
	$("body").on('click', '.change--commented-until-date', function (e) {
		if ($(this).is(':checked')) {
			$('.commented-until--date').attr({'disabled': false});
		} else {
			$('.commented-until--date').attr({'disabled': true});
		}
	});

	/* Обновление media */
	$("body").on('click', '.save--file', function(e) {
		e.preventDefault();
		let id = $(this).attr('data-id'),
			title = $("#title--" + id).val(),
			fullTitle = $("#full-title--" + id).val(),
			published_at = $('#file-published-at-' + id).val(),
			published_time = $('#file-published-time-at-' + id).val(),
			reg_number = $('#file-reg-number-' + id).val();

		$.ajax({
			url: '/ajax/saveFile/'+ id,
			type: 'POST',
			async: false,
			dataType: 'json',
			data : {
				_token: $('meta[name="_token"]').attr('content'),
				title: title,
				published_time: published_time,
				published_at: published_at,
				fullTitle: fullTitle,
				regNumber: reg_number
			},
			success: function(data) {
				if (data.errors) {
					messageError(data.errors);
				} else {
					messageSuccess(data.success);
				}
			}
		});
	});

	/* Обновление media */
	$("body").on('click', '.change-main-file', function(e) {
		e.preventDefault();
		var id = $(this).attr('data-id');
		var lang = $(this).attr('data-lang');

		$.ajax({
			url: '/ajax/saveFile/'+ id,
			type: 'POST',
			async: false,
			dataType: 'json',
			data : { _token: $('meta[name="_token"]').attr('content'), main: 1},
			success: function(data) {
				if (data.errors) {
					messageError(data.errors);
				} else {
					messageSuccess(data.success);
					$('.change-main-file[data-lang="' + lang + '"]').find('i').removeClass('fa-star').addClass('fa-star-o');
					$('*[data-id="' + id + '"]').find('i').removeClass('fa-star-o').addClass('fa-star');
				}
			}
		});
	});


});
