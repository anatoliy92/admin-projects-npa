<?php namespace Avl\AdminNpa\Controllers\Site\Ajax;

use App\Http\Controllers\Avl\AvlController;
use Avl\AdminNews\Models\News;
use Avl\AdminNpa\Models\Npa;
use Avl\AdminNpa\Models\NpaComments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NpaController extends AvlController
{
    /**
     * Добавление комментария
     *
     * @param integer $id номер записи
     * @param Request $request
     * @return json
     */
    public function sendComment($id, Request $request)
    {
        /**
         * @var \Avl\AdminNpa\Models\Npa $record
         */
        $record = Npa::findOrFail($id);

        if (!$record->isCommentable()) {
            return ['errors' => ['Комментарии закрыты']];
        }

        $post = $request->input();

        $this->validate(
            request(),
            [
                'comment' => 'required',
            ]);

        $comment                             = new NpaComments();
        $comment->npa_id                     = $record->id;
        $comment->created_user               = Auth::user()->id;
        $comment->{'comment_' . $this->lang} = $post['comment'];
        $comment->moderated                  = Auth::user()->id == $record->created_user;

        if (Auth::user()->id == $record->created_user && $post['comment_id']) {
            $comment->comment_id = $post['comment_id'];
        }

        if ($comment->save()) {
            return ['success' => 'ok'];
        }

        return ['errors' => ['Произошла ошибка']];
    }

}
