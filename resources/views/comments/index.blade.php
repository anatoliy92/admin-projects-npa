@extends('avl.default')

@section('js')
	<script src="{{ asset('vendor/adminnpa/js/comment.js') }}" charset="utf-8"></script>
	<script src="/avl/js/tinymce/tinymce.min.js" charset="utf-8"></script>
@endsection

@section('main')
	<div class="card">
		<div class="card-header">
			<i class="fa fa-align-justify"></i> Комментарии к документу: {{ $npa->title }}
		</div>
		<div class="card-body">
			@if ($comments)
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th width="50" class="text-center">#</th>
								<th class="text-center">Язык комментария</th>
								<th>Автор</th>
								<th class="text-center">Комментарий</th>
								<th class="text-center" style="width: 160px">Дата публикации</th>
								<th class="text-center" style="width: 100px;">Действие</th>
							</tr>
						</thead>
						<tbody>
							@foreach (\Avl\AdminNpa\Models\NpaComments::getList($comments) as $data)
								@php $comment = $data['comment']; @endphp
								<tr class="position-relative" id="comment--item-{{ $comment->id }}">
									<td class="text-center">{{ $comment->id }}</td>
									<td class="text-center">
										{{ $comment->getCommentLang(false)->name }}
									</td>
									<td><b>{{ $comment->author->getFioAttribute() }}</b><br/></td>
									<td>{{ $comment->getComment() }}<br/><br/>
										@if (!empty($data['replies']))
											@foreach ($data['replies'] as $reply)
												<b>{{ $comment->author->getFioAttribute() }}</b>: {{ $reply['comment']->getComment() }}
											@endforeach
										@endif

										@if ($npa->created_user == $user->id AND empty($data['replies']))
											<a href="{{ route("adminnpa::sections.npa.comment.edit", ['id' => $npa->id, 'comment_id' => $comment->id]) }}">Ответить</a>
										@endif


										@if ($npa->created_user == $user->id AND !empty($data['replies']))
											<a href="{{ route("adminnpa::sections.npa.comment.edit", ['id' => $npa->id, 'comment_id' => $comment->id]) }}">Редактировать</a>
										@endif
									</td>
									<td class="text-center">
										<span>{{ date('Y-m-d H:i', strtotime($comment->created_at)) }}</span>
									</td>
									<td class="text-right">
										<div class="btn-group" role="group">
											@if ($comment->moderated)
												<a href="{{ route('adminnpa::sections.npa.comment.show', ['id' => $npa->id, 'comment_id' => $comment->id, 'hide' => 1]) }}" class="btn btn btn-outline-primary" title="Скрыть с сайта"><i class="fa fa-eye-slash"></i></a>
											@else
												<a href="{{ route('adminnpa::sections.npa.comment.show', ['id' => $npa->id, 'comment_id' => $comment->id]) }}" class="btn btn btn-outline-primary" title="Показать на сайте"><i class="fa fa-eye"></i></a>
											@endif

											<a href="#" class="btn btn btn-outline-danger remove--record" title="Удалить"><i class="fa fa-trash"></i></a>
										</div>
											<div class="remove-message">
													<span>Вы действительно желаете удалить запись?</span>
													<span class="remove--actions btn-group btn-group-sm">
															<button class="btn btn-outline-primary cancel"><i class="fa fa-times-circle"></i> Нет</button>
															<button class="btn btn-outline-danger remove--comment" data-id="{{ $comment->id }}" data-npa="{{ $npa->id }}"><i class="fa fa-trash"></i> Да</button>
													</span>
											</div>

									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			@endif
		</div>
	</div>
@endsection
