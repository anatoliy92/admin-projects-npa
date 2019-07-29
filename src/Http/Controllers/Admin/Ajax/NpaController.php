<?php namespace Avl\AdminNpa\Controllers\Admin\Ajax;

use App\Http\Controllers\Avl\AvlController;
use Avl\AdminNews\Models\News;
use Avl\AdminNpa\Models\Npa;
use Avl\AdminNpa\Models\NpaComments;
use Illuminate\Http\Request;

class NpaController extends AvlController
{
    /**
     * Изменение даты в списке записей
     *
     * @param integer $id номер записи
     * @param Request $request
     * @return json
     */
    public function changeNewsDate($id, Request $request)
    {
        $record = Npa::findOrFail($id);

        $record->published_at = $request->input('published') . ':00';

        if ($record->save()) {
            return [
                'success'   => ['Дата изменена'],
                'published' => date('Y-m-d H:i', strtotime($record->published_at)),
            ];
        }

        return ['errors' => ['Произошла ошибка']];
    }
}
