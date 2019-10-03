<?php namespace Avl\AdminNpa\Controllers\Site;

use App\Http\Controllers\Site\Sections\SectionsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Facades\ApiFacade;
use Carbon\Carbon;
use View;

class NpaController extends SectionsController
{

    public function index(Request $request)
    {
        if (is_null($this->section->rubric) || $this->section->rubric == 0) {

            $template = 'site.templates.npa.short.' . $this->getTemplateFileName($this->section->current_template->file_short);

            $records = $this->getQuery($this->section->npa(), $request);

            $records = $records->orderBy('published_at', 'DESC')->paginate($this->section->current_template->records);

            $template = (View::exists($template)) ? $template : 'site.templates.npa.short.default';

            return view ($template, [
                'records'    => $records,
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

        $data = $this->section->npa()->where('good_' . $this->lang, 1)->whereNull('until_date')->orWhere('until_date', '<=', Carbon::now())->findOrFail($id);

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

        return redirect()->back()->with("status", "Комментарий проходит модерацию");
    }

    public function getQuery($result, $request)
    {

        switch ($request->type) {
          case "project": { $result = $result->where('type', 1); break; }
          default: { $result = $result->where('type', 2); break; }
        }

        $result = $result->where('good_' . $this->lang, 1);

        $result = $result->whereNull('until_date')->orWhere('until_date', '<=', Carbon::now());

        $result = $result->where('published_at', '>=', Carbon::now());

        return $result;
    }

}
