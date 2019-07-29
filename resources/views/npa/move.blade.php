@extends('avl.default')

@section('main')
  <div class="card">
    <div class="card-header">
        <i class="fa fa-align-justify"></i> Переместить документ : {{ $npa->title_ru }}
        <div class="card-actions">
          <a href="{{ route('adminnpa::sections.npa.index', [ 'id' => $id, 'page' => session('page', '1') ]) }}" class="btn btn-primary pl-3 pr-3" title="Назад"><i class="fa fa-arrow-left"></i></a>
          <button type="submit" form="submit" name="button" value="save" class="btn btn-success pl-3 pr-3" title="Сохранить"><i class="fa fa-floppy-o"></i></button>
        </div>
    </div>
    <div class="card-body">
      <form action="{{ route('adminnpa::sections.npa.move.save', ['id' => $id, 'npa' => $npa->id]) }}" method="post" id="submit">
        {!! csrf_field(); !!}

        <div class="form-group">
          <label for="new_section">Переместить в</label>
          <select id="new_section" name="new_section" class="form-control">
            <option selected value="0">------</option>
            @include('avl.settings.sections.blocks.parent', ['sections' => $structures, 'parent' => 0, 'current' => $id, 'pre' => '' ,'level' => 0])
          </select>
        </div>

      </form>
    </div>
  </div>
@endsection
