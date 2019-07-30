<?php namespace Avl\AdminNpa\Controllers\Site;

use App\Facades\ApiFacade;
use App\Http\Controllers\Site\Sections\SectionsController;
use Illuminate\Http\Request;
use App\Models\Sections;
use Carbon\Carbon;
use Cache;
use Illuminate\Support\Facades\Auth;
use View;

class NpaController extends SectionsController
{

    public function index(Request $request)
    {
        if ((is_null($this->section->rubric) || $this->section->rubric == 0) || $this->section->alias == 'npa') {

            $template = 'site.templates.npa.short.' . $this->getTemplateFileName(
                    $this->section->current_template->file_short);

            $records = $this->getQuery($this->section->npa(), $request);

            $records = $records->orderBy('published_at', 'DESC')->paginate($this->section->current_template->records);

            $rubrics = $this->section->rubrics()->where('good_' . $this->lang, 1)->orderBy(
                'title_' . $this->lang,
                'ASC')->get();

            $template = (View::exists($template)) ? $template : 'site.templates.npa.short.default';

            return view(
                $template,
                [
                    'records'    => $records,
                    'rubrics'    => toSelectTransform($rubrics->toArray()),
                    'pagination' => $records->appends($_GET)->links(),
                    'request'    => $request
                ]);
        }

        return redirect()->route('site.npa.rubrics', ['alias' => $this->section->alias]);
    }

    public function show($alias, $id)
    {
        $template = 'site.templates.npa.full.' . $this->getTemplateFileName(
                $this->section->current_template->file_full);

        $data = $this->section->npa()->where('good_' . $this->lang, 1)->findOrFail($id);

        $data->timestamps = false;  // отключаем обновление даты

        return view(
            $template,
            [
                'data'     => $data,
                'images'   => $data->media()->where('good', 1)->orderBy('main', 'DESC')->orderBy('sind', 'DESC')->get(),
                'files'    => $data->media('file')->where('lang', $this->lang)->where('good', 1)->orderBy(
                    'sind',
                    'DESC')->get(),
                'comments' => $data->comments()->where('moderated', 1)->whereNotNull('comment_' . $this->lang)->get(),
                'print'    => true
            ]);
    }

    /**
     * View all rubrics if instance on
     *
     * @param string $alias alias off section
     * @return to view all rubrics
     */
    public function rubrics($alias, Request $request)
    {
        $records = $this->section->rubrics()->where('good_' . $this->lang, 1)->orderBy('published_at', 'DESC');

        $template = 'site.templates.npa.category.' . $this->getTemplateFileName(
                $this->section->current_template->file_category);

        $records = $records->paginate($this->section->current_template->records);

        return view(
            $template,
            [
                'records'    => $records,
                'pagination' => $records->appends($_GET)->links(),
                'byPage'     => $this->section->current_template->records
            ]);
    }

    public function rubricsShow($alias, $rubric = null, Request $request)
    {
        $template = 'site.templates.npa.short.' . $this->getTemplateFileName(
                $this->section->current_template->file_short);

        $records = $this->getQuery($this->section->npa(), $request);

        $records = $records->where('rubric_id', $rubric)->orderBy('published_at', 'DESC')->paginate(
            $this->section->current_template->records);

        return view(
            $template,
            [
                'records'    => $records,
                'rubrics'    => $this->section->rubrics()->orderBy('published_at', 'desc')->get(),
                'rubricOne'  => $this->section->rubrics()->find($rubric),
                'pagination' => $records->appends($_GET)->links(),
                'request'    => $request
            ]);
    }

    /**
     * Добавление комментария
     *
     * @param integer $id номер записи
     * @param Request $request
     * @return json
     */
    public function sendComment($id, Request $request)
    {
        $post = $request->input();

        $this->validate(
            request(),
            [
                'comment' => 'required',
            ]);

        $response = ApiFacade::request('POST', 'api/npa/' . $id . '/comment', [
            'created_id' => Auth::user()->id,
            'lang'       => $this->lang,
            'comment'    => $post['comment'],
            'comment_id' => $post['comment_id'] ?? null
        ]);

        if (isset($response->errors)) {
            return redirect()->back()->withErrors($response->errors);
        }

        return redirect()->back()->with("Комментарий добавлен");
    }

    public function getQuery($result, $request)
    {

        $result = $result->where('good_' . $this->lang, 1);

        // фильтр если приходит
        if ($request->input('rubric') && $request->input('rubric') > 0) {
            $result = $result->where('rubric_id', $request->input('rubric'))->whereHas(
                'rubric',
                function ($query) {
                    $query->where('good_' . $this->lang, 1);
                });
        }

        if ($request->input('date')) {
            $result = $result->whereDate('published_at', $request->input('date'));
        }

        $result = $result->with('rubric');
        $result = $result->where('published_at', '<=', Carbon::now());

        return $result;
    }

}
