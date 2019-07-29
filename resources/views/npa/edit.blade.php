@extends('avl.default')

@section('css')
	<link rel="stylesheet" href="/avl/js/jquery-ui/jquery-ui.min.css">
	<link rel="stylesheet" href="/avl/js/uploadifive/uploadifive.css">
	<link rel="stylesheet" href="/avl/js/jquery-ui/timepicker/jquery.ui.timepicker.css">
@endsection

@section('main')
		<div class="card">
			<div class="card-header">
				<i class="fa fa-align-justify"></i> Редактирование : {{ str_limit($npa->title_ru, 100) }}
				<div class="card-actions">
					<a href="{{ route('adminnpa::sections.npa.index', [ 'id' => $id, 'page' => session('page', '1') ]) }}" class="btn btn-default pl-3 pr-3" style="width: 70px;" title="Назад"><i class="fa fa-arrow-left"></i></a>
					<button type="submit" form="submit" name="button" value="save" class="btn btn-success pl-3 pr-3" style="width: 70px;" title="Сохранить изменения"><i class="fa fa-floppy-o"></i></button>
				</div>
			</div>

			<div class="card-body">
				<form action="{{ route('adminnpa::sections.npa.update', ['id' => $id, 'npa' => $npa->id]) }}" method="post" id="submit">
					{!! csrf_field(); !!}
					{{ method_field('PUT') }}
					<input id="section_id" type="hidden" name="section_id" value="{{ $npa->section_id }}">
					<input id="model-name" type="hidden" value="Avl\AdminNews\Models\News">
					<input id="model-id" type="hidden" name="npa_id" value="{{ $npa->id }}">

					<div class="row">
						<div class="col-12 col-sm-3">
							<div class="form-group">
								{{ Form::label(null, 'Дата публикации') }}
								{{ Form::text('npa_published_at', date('Y-m-d', strtotime($npa->published_at)), ['class' => 'form-control datepicker', 'id' => '']) }}
							</div>
						</div>
						<div class="col-12 col-sm-3">
							<div class="form-group">
								{{ Form::label(null, 'Время публикации') }}
								{{ Form::text('npa_published_time', date('H:i', strtotime($npa->published_at)), ['class' => 'form-control timepicker']) }}
							</div>
						</div>
						<div class="col-12 col-sm-3">
							<div class="form-group">
								{{ Form::label(null, 'Дата обновления') }}
								<div class="controls">
									<div class="input-prepend input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">{{ Form::checkbox('npa_updated', 'on', ($npa->updated_date) ? true : false, ['class' => 'change--updated-date']) }}</span>
										</div>
										{{ Form::text('npa_updated_date', $npa->updated_date ? date('Y-m-d', strtotime($npa->updated_date)) : null, ['class' => 'form-control datepicker updated--date', 'disabled' => !$npa->updated_date ? true : false]) }}
									</div>
								</div>
							</div>
						</div>
						<div class="col-12 col-sm-3">
							<div class="form-group">
								{{ Form::label(null, 'Время обновления') }}
								{{ Form::text('npa_updated_time', $npa->updated_date ? date('H:i', strtotime($npa->updated_date)) : null, ['class' => 'form-control timepicker updated--date', 'disabled' => !$npa->updated_date ? true : false]) }}
							</div>
						</div>
						<div class="col-12 col-sm-3">
							<div class="form-group">
								{{ Form::label(null, 'Дата окончания публикации') }}
								<div class="controls">
									<div class="input-prepend input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">{{ Form::checkbox('npa_until', 'on', ($npa->until_date) ? true : false, ['class' => 'change--until-date']) }}</span>
										</div>
										{{ Form::text('npa_until_date', $npa->until_date ? date('Y-m-d', strtotime($npa->until_date)) : null, ['class' => 'form-control datepicker until--date', 'disabled' => !$npa->until_date ? true : false]) }}
									</div>
								</div>
							</div>
						</div>
						<div class="col-12 col-sm-3">
							<div class="form-group">
								{{ Form::label(null, 'Время окончания публикации') }}
								{{ Form::text('npa_until_time', $npa->until_date ? date('H:i', strtotime($npa->until_date)) : null, ['class' => 'form-control timepicker until--date', 'disabled' => !$npa->until_date ? true : false]) }}
							</div>
						</div>
						<div class="col-12 col-sm-3">
							<div class="form-group">
								{{ Form::label(null, 'Дата окончания приема коментариев') }}
								<div class="controls">
									<div class="input-prepend input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">{{ Form::checkbox('npa_comment_until', 'on', ($npa->comment_until_date) ? true : false, ['class' => 'change--comment-until-date']) }}</span>
										</div>
										{{ Form::text('npa_comment_until_date', $npa->comment_until_date ? date('Y-m-d', strtotime($npa->comment_until_date)) : null, ['class' => 'form-control datepicker comment-until--date', 'disabled' => !$npa->comment_until_date ? true : false]) }}
									</div>
								</div>
							</div>
						</div>
						<div class="col-12 col-sm-3">
							<div class="form-group">
								{{ Form::label(null, 'Время окончания приема комментариев') }}
								{{ Form::text('npa_comment_until_time', $npa->comment_until_date ? date('H:i', strtotime($npa->comment_until_date)) : null, ['class' => 'form-control timepicker comment-until--date', 'disabled' => !$npa->comment_until_date ? true : false]) }}
							</div>
						</div>

						@if ($section->rubric == 1)
							<div class="col-12">
								<div class="form-group">
									<label>Рубрика</label>
									<select class="form-control" name="npa_rubric_id">
										<option value="0">---</option>
										@if (!is_null($rubrics))
											@foreach ($rubrics as $rubric)
												<option value="{{ $rubric->id }}" @if(old('npa_rubric_id') == $rubric->id){{ 'selected' }}@elseif($npa->rubric_id == $rubric->id){{ 'selected' }}@endif>{{ !is_null($rubric->title_ru) ? $rubric->title_ru : str_limit(strip_tags($rubric->description_ru), 100) }}</option>
											@endforeach
										@endif
									</select>
								</div>
							</div>
						@endif

					</div>

					<ul class="nav nav-tabs" role="tablist">
						@foreach($langs as $lang)
							<li class="nav-item">
								<a class="nav-link @if($lang->key == 'ru') active show @endif" href="#title_{{ $lang->key }}" data-toggle="tab">{{ $lang->name }}</a>
							</li>
						@endforeach
						@if (array_key_exists('image', $section->modules ?? []))<li class="nav-item"><a class="nav-link" href="#image" data-toggle="tab">Изображения</a></li>@endif
						@if (array_key_exists('file', $section->modules ?? []))<li class="nav-item"><a class="nav-link" href="#file" data-toggle="tab">Файлы</a></li>@endif
					</ul>
					<div class="tab-content">
						@foreach ($langs as $lang)
							<div class="tab-pane @if($lang->key == "ru") active show @endif"  id="title_{{$lang->key}}" role="tabpanel">
								<ul class="nav nav-tabs" role="tablist">
									<li class="nav-item"><a class="nav-link active show" href="#sub-tab_{{ $lang->key }}-index" data-toggle="tab">Основные</a></li>
									<li class="nav-item"><a class="nav-link" href="#sub-tab_{{ $lang->key }}-full" data-toggle="tab">Полный текст Нормативно-правового документа</a></li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane active show"  id="sub-tab_{{ $lang->key }}-index" role="tabpanel">
										<div class="row">
											<div class="col-1">
												<div class="form-group">
													<label for="npa_good_{{ $lang->key }}">Вкл / Выкл</label><br/>
													<label class="switch switch-3d switch-primary">
														<input name='npa_good_{{ $lang->key }}' type='hidden' value='0'>
														<input type="checkbox" class="switch-input" name="npa_good_{{ $lang->key }}" value="1" @if ($npa->{'good_' . $lang->key} == 1) checked @endif>
														<span class="switch-label"></span>
														<span class="switch-handle"></span>
													</label>
												</div>
											</div>
											<div class="col-11">
												<div class="form-group">
													{{ Form::label(null, 'Заголовок') }}
													{{ Form::text('npa_title_' . $lang->key, $npa->{'title_' . $lang->key} ?? null, ['class' => 'form-control']) }}
												</div>
											</div>
											<div class="col-12">
												{{ Form::textarea('npa_short_' . $lang->key, $npa->{'short_' . $lang->key} ?? null, ['class' => 'tinymce']) }}
											</div>
										</div>
									</div>
									<div class="tab-pane"  id="sub-tab_{{ $lang->key }}-full" role="tabpanel">
										{{ Form::textarea('npa_full_' . $lang->key, $npa->{'full_' . $lang->key} ?? null, ['class' => 'tinymce']) }}
									</div>
								</div>
							</div>
						@endforeach
						@if (array_key_exists('image', $section->modules ?? []))
							<div class="tab-pane" id="image" role="tabpanel">
								<div class="block--file-upload">
									<input id="upload-photos" name="upload" type="file" />
								</div>
								<div class="row">
									<div class="photo--npa col-lg-12">
										<ul id="sortable" class="row list-unstyled">
											@foreach ($images as $image)
												<li class="col-6 col-sm-4 col-xl-3 col-xxl-2" id="mediaSortable_{{ $image['id'] }}">
													<div class="card card-stat">
														<div class="card-header">
															<div class="row">
																<div class="col-4 text-left">
																	<a href="#" class="change--status" data-model="App\Models\Media" data-id="{{ $image['id'] }}"><i class="fa fa-eye{{ ($image['good'] == 0) ? '-slash' : '' }}"></i></a>
																</div>
																<div class="col-4 text-center">
																	<a href="#" class="toMainPhoto" data-model="Media" data-id="{{ $image['id'] }}"><i class="fa {{ ($image['main'] == 1) ? 'fa-check-circle-o' : 'fa-circle-o' }}"></i></a>
																</div>
																<div class="col-4 text-right">
																	<a href="" class="change--lang" data-id="{{ $image['id'] }}"><img src="/avl/img/icons/flags/{{ $image['lang'] ?? 'null' }}--16.png"></a>
																</div>
															</div>
														</div>
														<div class="card-body p-0">
															<img src="/image/resize/200/190/{{ $image['url'] }}">
														</div>
														<div class="card-footer">
															<div class="row">
																<div class="col-6 text-left"><a href="#" class="deleteMedia" data-id="{{ $image['id'] }}"><i class="fa fa-trash-o"></i></a></div>
																<div class="col-6 text-right"><a href="#" class="open--modal-translates" data-id="{{ $image['id'] }}" data-toggle="modal" data-target="#translates-modal"><i class="fa fa-pencil"></i></a></div>
															</div>
														</div>
													</div>
												</li>
											@endforeach
										</ul>
									</div>
								</div>
							</div>
						@endif
						@if (array_key_exists('file', $section->modules ?? []))
							<div class="tab-pane" id="file" role="tabpanel">
								<div class="block--file-upload block--file-upload-npa position-relative">
									<div class="form-group">
										<select class="form-control" id="select--language-file">
											@foreach($langs as $lang)
												<option value="{{ $lang->key }}">{{ $lang->key }}</option>
											@endforeach
										</select>
									</div>
									<input id="upload-files" name="upload" type="file" />
								</div>
								<div class="row files--npa">
									<div class="col-md-12">
										<ul id="sortable-files" class="list-group">
											@foreach ($files as $file)
													<li class="col-md-12 list-group-item files--item" id="mediaSortable_{{ $file['id'] }}">
														<div class="img-thumbnail">
															<div class="input-group">
																<div class="input-group-prepend">
																	<span class="input-group-text"><a href="" class="change--lang" data-id="{{ $file['id'] }}"><img src="/avl/img/icons/flags/{{ $file['lang'] ?? 'null' }}--16.png"></a></span>
																	<span class="input-group-text file-move" style="cursor: move;"><i class="fa fa-arrows"></i></span>
																	<span class="input-group-text"><a href="#" class="change--status" data-model="App\Models\Media" data-id="{{ $file['id'] }}"><i class="fa @if($file['good'] == 1){{ 'fa-eye' }}@else{{ 'fa-eye-slash' }}@endif"></i></a></span>
																	<span class="input-group-text"><a href="/file/download/{{ $file['id'] }}" target="_blank"><i class="fa fa-download"></i></a></span>
																	<span class="input-group-text"><a href="#" class="deleteMedia" data-id="{{ $file['id'] }}"><i class="fa fa-trash-o"></i></a></span>
																</div>
																<input type="text" id="title--{{ $file['id'] }}" class="form-control" value="{{ $file['title_' . $file['lang'] ] }}">
																<div class="input-group-append">
																	<a href="#" class="input-group-text save--file-name" data-id="{{ $file['id'] }}"><i class="fa fa-floppy-o"></i></a>
																</div>
															</div>
														</div>
													</li>
											@endforeach
										</ul>
									</div>
								</div>
							</div>
						@endif
					</div>
				</form>
			</div>

			<div class="card-footer position-relative">
					<i class="fa fa-align-justify"></i> Редактирование : {{ str_limit($npa->title_ru, 100) }}
					<div class="card-actions">
						<a href="{{ route('adminnpa::sections.npa.index', [ 'id' => $id, 'page' => session('page', 1) ]) }}" class="btn btn-default pl-3 pr-3" style="width: 70px;" title="Назад"><i class="fa fa-arrow-left"></i></a>
						<button type="submit" form="submit" name="button" value="save" class="btn btn-success pl-3 pr-3" style="width: 70px;" title="Сохранить изменения"><i class="fa fa-floppy-o"></i></button>
					</div>
			</div>
		</div>
@endsection

@section('js')
	<script src="/avl/js/jquery-ui/jquery-ui.min.js" charset="utf-8"></script>
	<script src="/avl/js/uploadifive/jquery.uploadifive.min.js" charset="utf-8"></script>

	<script src="/avl/js/tinymce/tinymce.min.js" charset="utf-8"></script>
	<script src="/avl/js/jquery-ui/timepicker/jquery.ui.timepicker.js" charset="utf-8"></script>
	<script src="{{ asset('vendor/adminnpa/js/edit.js') }}" charset="utf-8"></script>
@endsection
