@extends('avl.default')

@section('js')
	<script src="{{ asset('vendor/adminnpa/js/comment.js') }}" charset="utf-8"></script>
	<script src="/avl/js/tinymce/tinymce.min.js" charset="utf-8"></script>
@endsection

@section('main')
	<div class="card">
		<div class="card-header">
			<i class="fa fa-align-justify"></i> Комментарий к документу: {{ $comment->npa_id }}
		</div>
		<div class="card-body">
			<b>{{ $comment->author->getFioAttribute() }}</b>: {{ $comment->getComment() }}
		</div>
	</div>

	@php $reply = $comment->reply @endphp
	@if ($reply)
		<div class="card">
			<div class="card-header">
				<i class="fa fa-align-justify"></i> Ответ:
			</div>
			<div class="card-body">
				<div>
					<b>{{ $reply->author->getFioAttribute() }}</b>: {{ $reply->getComment() }}
				</div>
				@if ($reply->npa->created_user == $user->id)
					<div class="comment-reply">
						<form class="form-group" method="post" action="{{ route('adminnpa::sections.npa.comment.reply', ['id' => $reply->npa->id, 'comment_id' => $comment->id]) }}">
							{!! csrf_field(); !!}
							<input type="hidden" name="replyId" value="{{ $reply->id }}">
							<textarea class="tinymce" name="comment">{{ $reply->getComment() }}</textarea>
							<button class="btn btn-outline-primary" type="submit">Ответить</button>
						</form>
					</div>
				@endif
			</div>
		</div>
	@else
		<div class="card">
			<div class="card-header">
				<i class="fa fa-align-justify"></i> Добавить ответ:
			</div>
			<div class="card-body">
				@if ($reply->npa->created_user == $user->id)
					<div class="comment-reply">
						<form class="form-group" method="post" action="{{ route('adminnpa::sections.npa.comment.reply', ['id' => $reply->npa->id, 'comment_id' => $comment->id]) }}">
							{!! csrf_field(); !!}
							<textarea class="tinymce" name="comment"></textarea>
							<button class="btn btn-outline-primary" type="submit">Ответить</button>
						</form>
					</div>
				@endif
			</div>
		</div>
	@endif
@endsection
