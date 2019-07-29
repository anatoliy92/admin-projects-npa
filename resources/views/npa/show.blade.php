@extends('avl.default')

@section('main')
	<div class="card">
		<div class="card-header">
			<i class="fa fa-align-justify"></i> Страница : {{$new->title_ru}}
			<div class="card-actions">
				<a href="{{ route('adminnews::sections.news.index', [ 'id' => $id, 'page' => session('page', '1') ]) }}" class="btn btn-primary pl-3 pr-3" title="Назад"><i class="fa fa-arrow-left"></i></a>
			</div>
		</div>
		<div class="card-body">
			<div class="form-group">
				<label>Дата публикации</label>
				<span class="form-control">{{ $new->published_at }}</span>
			</div>

			<ul class="nav nav-tabs" role="tablist">
				@foreach($langs as $lang)
					<li class="nav-item">
						<a class="nav-link @if($lang->key == 'ru') active show @endif" href="#title_{{ $lang->key }}" data-toggle="tab">{{ $lang->name }}</a>
					</li>
				@endforeach
			</ul>
			<div class="tab-content">
				@foreach ($langs as $lang)
					<div class="tab-pane @if($lang->key == "ru") active show @endif"  id="title_{{$lang->key}}" role="tabpanel">
						@if ($new->{'title_' . $lang->key} == "")
							<span class="form-control">Нет заголовка на данном языке</span>
						@else
							<span class="form-control">{{ $new->{'title_' . $lang->key} }}</span>
						@endif
					</div>
				@endforeach
			</div>

			</br>

			<ul class="nav nav-tabs" role="tablist">
				<li class="nav-item"><a class="nav-link active show" href="#short" data-toggle="tab">Короткая новость</a></li>
				<li class="nav-item"><a class="nav-link" href="#full" data-toggle="tab">Полная новость</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane" id="full" role="tabpanel">
					<ul class="nav nav-tabs" role="tablist">
						@foreach($langs as $lang)
							<li class="nav-item">
								<a class="nav-link @if($lang->key == "ru") active show @endif" href="#full_{{ $lang->key }}" data-toggle="tab"> {{ $lang->name }} </a>
							</li>
						@endforeach
					</ul>
					<div class="tab-content">
						@foreach ($langs as $lang)
							<div class="tab-pane @if($lang->key == "ru") active show @endif"  id="full_{{ $lang->key }}" role="tabpanel">
								@if ($new->{'full_' . $lang->key} == "")
									<span class="form-control">Нет полной новости на данном языке</span>
								@else
									<span class="form-control">{{$new->{'full_' . $lang->key} }}</span>
								@endif
							</div>
						@endforeach
					</div>
				</div>

				<div class="tab-pane active show" id="short" role="tabpanel">
						<ul class="nav nav-tabs" role="tablist">
							@foreach($langs as $lang)
								<li class="nav-item">
									<a class="nav-link @if($lang->key == "ru") active show @endif" href="#short_{{ $lang->key }}" data-toggle="tab"> {{ $lang->name }} </a>
								</li>
							@endforeach
						</ul>
						<div class="tab-content">
							@foreach ($langs as $lang)
								<div class="tab-pane @if($lang->key == "ru") active show @endif"  id="short_{{$lang->key}}" role="tabpanel">
									@if ($new->{'short_' . $lang->key} == "")
										<span class="form-control">Нет короткой новости на данном языке</span>
									@else
										<span class="form-control">{{ $new->{'short_' . $lang->key} }}</span>
									@endif
								</div>
							@endforeach
						</div>
					</div>
			</div>

		</div>
	</div>
@endsection
