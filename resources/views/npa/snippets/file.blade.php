<li class=" list-group-item files--item bg-light mb-3 border p-1" id="mediaSortable_{{ $file['id'] }}">
    <div class="row">
        <div class="col-11 pr-0">
            <div class="row">
                <div class="col-12">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="btn border"><a href="#" class="change--lang" data-lang="{{ $file['lang'] }}" data-id="{{ $file['id'] }}"><img src="/avl/img/icons/flags/{{ $file['lang'] ?? 'null' }}--16.png"></a></span>
                            <span class="btn border file-move"><i class="fa fa-arrows"></i></span>
                            <span class="btn border"><a href="#" class="change--status" data-model="App\Models\Media" data-id="{{ $file['id'] }}"><i class="fa @if($file['good'] == 1){{ 'fa-eye' }}@else{{ 'fa-eye-slash' }}@endif"></i></a></span>
                        </div>
                        {{ Form::text(null, $file['title_' . $file['lang']] ?? null, ['class' => 'form-control', 'id' => 'title--' . $file['id']]) }}
                        @if ($type == 2)
                            {{ Form::text(null, $file['published_at'] ? date('Y-m-d', strtotime($file['published_at'])) : null, ['class' => 'form-control datepicker acting', 'id' => 'file-published-at-' . $file['id']]) }}
                            {{ Form::text(null, $file['published_at'] ? date('H:i', strtotime($file['published_at'])) : null, ['class' => 'form-control timepicker acting', 'id' => 'file-published-time-at-' . $file['id']]) }}
                        @endif
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top: -1px;">
                <div class="col-12">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="btn border"><a href="#" class="change-main-file" data-model="App\Models\Media" data-lang="{{ $file['lang'] }}" data-id="{{ $file['id'] }}"><i class="fa @if($file['id'] == $npa->{'mainFile_' . $file['lang'] ?? 'ru'}){{ 'fa-star' }}@else{{ 'fa-star-o' }}@endif"></i></a></span>
                            <span class="btn border"><a href="/file/download/{{ $file['id'] }}" target="_blank"><i class="fa fa-download"></i></a></span>
                            <span class="btn border"><a href="#" class="deleteMedia" data-id="{{ $file['id'] }}"><i class="fa fa-trash-o"></i></a></span>
                        </div>
                        @if ($type == 2)
                            <input type="text" id="full-title--{{ $file['id'] }}" class="form-control" value="{{ $file['fullName'] }}" placeholder="Полное название">
                            <input type="text" id="file-reg-number-{{ $file['id'] }}" class="form-control reg-number acting" value="{{ $file['regNumber'] }}" placeholder="Регистрационный номер">
                        @else
                            {{ Form::text(null, $file['published_at'] ? date('Y-m-d', strtotime($file['published_at'])) : null, ['class' => 'form-control datepicker', 'id' => 'file-published-at-' . $file['id']]) }}
                            {{ Form::text(null, $file['published_at'] ? date('H:i', strtotime($file['published_at'])) : null, ['class' => 'form-control timepicker', 'id' => 'file-published-time-at-' . $file['id']]) }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-1 pl-0">
            <button class="save--file w-100 h-100 btn btn-success d-flex align-items-center justify-content-center" data-id="{{ $file['id'] }}"><i class="fa fa-floppy-o"></i></button>
        </div>
    </div>
</li>