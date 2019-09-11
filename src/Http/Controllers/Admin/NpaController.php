<?php namespace Avl\AdminNpa\Controllers\Admin;

use App\Http\Controllers\Avl\AvlController;
use App\Models\{
    Media, Langs, Rubrics, Sections
};

use App\Traits\MediaTrait;
use Avl\AdminNpa\Models\Npa;
use App\Traits\SectionsTrait;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use File;

class NpaController extends AvlController
{

    use MediaTrait;

    protected $langs = null;

    public function __construct(Request $request)
    {

        parent::__construct($request);

        $this->langs = Langs::get();
    }

    /**
     * Страница вывода списка документов к определенному разделу
     *
     * @param int     $id номер раздела
     * @param  string $type
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index($id, $type, Request $request)
    {
        // Запоминаем номер страницы на которой находимся
        $request->session()->put('page', $request->input('page') ?? 1);

        $section = Sections::whereId($id)->firstOrFail();

        $this->authorize('view', $section);

        return view(
            'adminnpa::npa.index',
            [
                'id'      => $id,
                'section' => $section,
                'request' => $request,
                'langs'   => $this->langs,
                'npa'     => $this->getQuery($section->npa(), $request, $type)->paginate(30),
                'rubrics' => array_add(
                    toSelectTransform(
                        Rubrics::select('id', 'title_ru')->where('section_id', $section->id)->get()->toArray()),
                    0,
                    'Нормативно-правовые документы без рубрики'),
            ]);
    }

    /**
     * Вывод формы на добавление документов
     *
     * @param int $id Номер раздела
     * @return [type]     [description]
     */
    public function create($id)
    {
        $section = Sections::whereId($id)->firstOrFail();

        $this->authorize('create', $section);

        return view(
            'adminnpa::npa.create',
            [
                'langs'   => $this->langs,
                'section' => $section,
                'rubrics' => $section->rubrics()->orderBy('published_at', 'DESC')->get(),
                'id'      => $id
            ]);
    }

    /**
     * Метод для добавления новой записи в базу
     *
     * @param Request $request
     * @param int     $id номер раздела
     * @return redirect to index or create method
     */
    public function store(Request $request, $id)
    {
        $this->authorize('create', Sections::findOrFail($id));

        $post = $request->input();

        $this->validate(
            request(),
            [
                'button'                 => 'required|in:add,save,edit',
                'npa_rubric_id'          => 'sometimes',
                'npa_short_ru'           => '',
                'npa_full_ru'            => '',
                'npa_title_ru'           => 'max:255',
                'npa_type'               => '',
                'npa_published_at'       => 'required|date_format:"Y-m-d"',
                'npa_published_time'     => 'required|date_format:"H:i"',
                'npa_updated_date'       => 'date_format:"Y-m-d"',
                'npa_updated_time'       => 'date_format:"H:i"',
                'npa_until_date'         => 'date_format:"Y-m-d"',
                'npa_until_time'         => 'date_format:"H:i"',
                'npa_commented_until_date' => 'date_format:"Y-m-d',
                'npa_commented_until_time' => 'date_format:"H:i',
                'npa_updated'            => ''
            ]);

        $record               = new Npa();
        $record->section_id   = $id;
        $record->created_user = Auth::user()->id;
        $record->published_at = $post['npa_published_at'] . ' ' . $post['npa_published_time'];
        $record->type         = $post['npa_type'];

        foreach ($this->langs as $lang) {
            $record->{'good_' . $lang->key}  = $post['npa_good_' . $lang->key];
            $record->{'title_' . $lang->key} = $post['npa_title_' . $lang->key];
            $record->{'short_' . $lang->key} = $post['npa_short_' . $lang->key];
            $record->{'full_' . $lang->key}  = $post['npa_full_' . $lang->key];
        }

        if (isset($post['npa_updated'])) {
            $record->updated_date = $post['npa_updated_date'] . ' ' . $post['npa_updated_time'];
        }

        if (isset($post['npa_commented_until'])) {
            $record->commented_until_date = $post['npa_commented_until_date'] . ' ' . $post['npa_commented_until_time'];
        }

        if (isset($post['npa_until'])) {
            $record->until_date = $post['npa_until_date'] . ' ' . $post['npa_until_time'];
        }

        if (isset($post['npa_rubric_id']) && ($post['npa_rubric_id'] > 0)) {
            $record->rubric_id = $post['npa_rubric_id'];    // проставляему рубрику если ее выбрали
        }

        if ($record->save()) {
            switch ($post['button']) {
                case 'add':
                    {
                        return redirect()->route('adminnpa::sections.npa.create', ['id' => $id])->with(
                            ['success' => ['Сохранение прошло успешно!']]);
                    }
                case 'edit':
                    {
                        return redirect()->route(
                            'adminnpa::sections.npa.edit',
                            ['id' => $id, 'npa_id' => $record->id])->with(
                            ['success' => ['Сохранение прошло успешно!']]);
                    }
                default:
                    {
                        return redirect()->route('adminnpa::sections.npa.index', ['id' => $id])->with(
                            ['success' => ['Сохранение прошло успешно!']]);
                    }
            }
        }

        return redirect()->route('adminnpa::sections.npa.create', ['id' => $id])->with(
            ['errors' => ['Что-то пошло не так.']]);
    }

    /**
     * Отобразить запись на просмотр
     *
     * @param int $id Номер раздела
     * @param int $npa_id Номер записи
     * @return \Illuminate\Http\Response
     */
    public function show($id, $npa_id)
    {
        $this->authorize('view', Sections::findOrFail($id));

        return view(
            'adminnpa::npa.show',
            [
                'langs' => $this->langs,
                'npa'   => Npa::findOrFail($npa_id),
                'id'    => $id
            ]);
    }

    /**
     * Форма открытия записи на редактирование
     *
     * @param int $id Номер раздела
     * @param int $npa_id Номер записи
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $npa_id)
    {
        $section = Sections::whereId($id)->firstOrFail();

        $this->authorize('update', $section);

        $npa = $section->npa()->findOrFail($npa_id);

        return view(
            'adminnpa::npa.edit',
            [
                'npa'     => $npa,
                'id'      => $id,
                'section' => $section,
                'rubrics' => $section->rubrics()->orderBy('published_at', 'DESC')->get(),
                'images'  => $npa->media('image')->orderBy('sind', 'DESC')->get(),
                'files'   => $npa->media('file')->orderBy('sind', 'DESC')->get(),
                'langs'   => $this->langs,
            ]);
    }

    /**
     * Метод для обновления определенной записи
     *
     * @param Request $request
     * @param int     $id Номер раздела
     * @param int     $npa_id Номер записи
     * @return redirect to index method
     */
    public function update(Request $request, $id, $npa_id)
    {
        $this->authorize('update', Sections::findOrFail($id));

        $post = $request->input();

        $this->validate(
            request(),
            [
                'button'             => 'required|in:add,save',
                'npa_rubric_id'      => 'sometimes',
                'npa_title_ru'       => 'max:255',
                'npa_short_ru'       => '',
                'npa_full_ru'        => '',
                'npa_type'           => '',
                'npa_published_at'   => 'required|date_format:"Y-m-d"',
                'npa_published_time' => 'required|date_format:"H:i"',
                'npa_updated_date'   => 'date_format:"Y-m-d"',
                'npa_updated_time'   => 'date_format:"H:i"',
                'npa_updated'        => ''
            ]);

        $npa = Npa::findOrFail($npa_id);

        $npa->published_at = $post['npa_published_at'] . ' ' . $post['npa_published_time'];
        $npa->type         = $post['npa_type'];
        $npa->update_user  = Auth::user()->id;

        foreach ($this->langs as $lang) {
            $npa->{'good_' . $lang->key}  = $post['npa_good_' . $lang->key];
            $npa->{'title_' . $lang->key} = $post['npa_title_' . $lang->key];
            $npa->{'short_' . $lang->key} = $post['npa_short_' . $lang->key];
            $npa->{'full_' . $lang->key}  = $post['npa_full_' . $lang->key];
        }

        if (isset($post['npa_updated'])) {
            $npa->updated_date = $post['npa_updated_date'] . ' ' . $post['npa_updated_time'];
        } else {
            $npa->updated_date = null;
        }

        if (isset($post['npa_commented_until'])) {
            $npa->commented_until_date = $post['npa_commented_until_date'] . ' ' . $post['npa_commented_until_time'];
        } else {
            $npa->commented_until_date = null;
        }

        if (isset($post['npa_until'])) {
            $npa->until_date = $post['npa_until_date'] . ' ' . $post['npa_until_time'];
        } else {
            $npa->until_date = null;
        }

        if (isset($post['npa_rubric_id']) && ($post['npa_rubric_id'] > 0)) {
            $npa->rubric_id = $post['npa_rubric_id'];
        } else {
            $npa->rubric_id = null;
        }

        if ($npa->save()) {
            return redirect()->route(
                'adminnpa::sections.npa.index',
                ['id' => $id, 'page' => $request->session()->get('page', '1')])
                ->with(['success' => ['Сохранение прошло успешно!']]);
        }

        return redirect()->back()->with(['errors' => ['Что-то пошло не так.']]);
    }

    /**
     * Форма для переноса документа в другой раздел
     *
     * @param int     $id Номер раздела
     * @param int     $npa_id Номер записи
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function move($id, $npa_id, Request $request)
    {
        $this->authorize('update', Sections::findOrFail($id));

        return view(
            'adminnpa::npa.move',
            [
                'npa' => Npa::findOrFail($npa_id),
                'id'  => $id
            ]);
    }

    /**
     * Метод для обновления привязки записи к разделу
     *
     * @param int     $id Номер раздела
     * @param int     $npa_id Номер записи
     * @param Request $request
     * @return redirect to index method
     */
    public function moveSave($id, $npa_id, Request $request)
    {
        $this->authorize('update', Sections::findOrFail($id));

        $this->validate(
            $request,
            [
                'new_section' => 'required|exists:sections,id,type,npa'
            ]);

        if ($request->input('new_section') > 0) {
            $data = Npa::findOrFail($npa_id);

            $data->section_id = $request->input('new_section');

            if ($data->save()) {
                return redirect()->route('adminnpa::sections.npa.index', ['id' => $id]);
            }
        }

        return redirect()->back()->with(['errors' => ['Выберите раздел']]);
    }

    /**
     * Удаление записи и всех медиа файлов
     *
     * @param int $id Номер раздела
     * @param int $npa_id Номер записи
     * @return json
     */
    public function destroy($id, $npa_id, Request $request)
    {
        $this->authorize('delete', Sections::findOrFail($id));

        $record = Npa::find($npa_id);
        if (!is_null($record)) {

            /* Удаляем все изображения */
            if ($record->media('image')->count() > 0) {
                foreach ($record->media('image')->get() as $image) {
                    $this->deleteMedia($image->id, $request);
                }
            }

            /* Удаляем все файлы */
            if ($record->media('file')->count() > 0) {
                foreach ($record->media('file')->get() as $file) {
                    $this->deleteMedia($file->id, $request);
                }
            }

            /* Удаляем все видео */
            if ($record->media('video')->count() > 0) {
                foreach ($record->media('video')->get() as $video) {
                    $this->deleteMedia($video->id, $request);
                }
            }

            if ($record->delete()) {
                return ['success' => ['Новость удалена']];
            }
        }

        return ['errors' => ['Ошибка удаления.']];
    }

    /**
     * Функция для формирования фильтра в списке записей
     *
     * @param query   $query Eloquent
     * @param request $request
     * @param  string $type
     * @return query
     */
    private function getQuery($query, $request, $type)
    {
        if (!is_null($request->input('rubric'))) {
            if ($request->input('rubric') == 0) {
                $query = $query->whereNull('rubric_id');
            } else {
                $query = $query->where('rubric_id', $request->input('rubric'));
            }
        }

        switch ($type) {
            case "project":
                $query->where('type', 1);
                break;
            case "approve":
                $query->where('type', 2);
                break;
            default:
                $query->where('type', 1);
        }

        return $query->orderBy('published_at', 'DESC');
    }
}
