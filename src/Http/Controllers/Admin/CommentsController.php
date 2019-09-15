<?php namespace Avl\AdminNpa\Controllers\Admin;

use App\Http\Controllers\Avl\AvlController;
use App\Models\{
    Media, Langs, Rubrics, Sections
};

use App\Traits\MediaTrait;
use Avl\AdminNpa\Models\Npa;
use Avl\AdminNpa\Models\NpaComments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentsController extends AvlController
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
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index($id, Request $request)
    {
        $npa = Npa::findOrFail($id);

        return view(
            'adminnpa::comments.index',
            [
                'npa'     => $npa,
                'comments' => $npa->comments,
                'user'     => Auth::user()
            ]);
    }

    /**
     * Промодерировать комментарий
     *
     * @param                          $id
     * @param                          $commentId
     * @param \Illuminate\Http\Request $request
     */
    public function show($id, $commentId, Request $request)
    {
        $comment = NpaComments::findOrFail($commentId);

        $comment->moderated = $request->get('hide', false) ? false : true;

        if ($comment->save()) {
            return redirect()->route('adminnpa::sections.npa.comment.index', ['id' => $id]);
        }

        return redirect()->back()->with(['errors' => ['Произошла ошибка']]);
    }

    /**
     * Удалить комментарий
     *
     * @param                          $id
     * @param                          $commentId
     * @param \Illuminate\Http\Request $request
     */
    public function remove($id, $commentId, Request $request)
    {
        $comment = NpaComments::findOrFail($commentId);

        if ($comment->delete()) {

            return ['success' => ['Комментарий удален']];
        }

        return ['errors' => ['Ошибка удаления.']];
    }

    /**
     * Ответить на комментарий
     *
     * @param                          $id
     * @param                          $commentId
     * @param \Illuminate\Http\Request $request
     */
    public function reply($id, $commentId, Request $request)
    {
        $comment = NpaComments::findOrFail($commentId);

        $post = $request->input();

        $this->validate(
            request(),
            [
                'comment' => 'required',
            ]);

        $reply                             = new NpaComments();
        $reply->npa_id                     = $comment->npa_id;
        $reply->created_user               = Auth::user()->id;
        $reply->{'comment_' . $comment->getCommentLang()} = $post['comment'];
        $reply->moderated                  = true;
        $reply->comment_id = $comment->id;

        if ($reply->save()) {
            return redirect()->route('adminnpa::sections.npa.comment.index', ['id' => $id]);
        }

        return redirect()->back()->with(['errors' => ['Произошла ошибка']]);
    }

    /**
     * Редактировать комментарий
     *
     * @param                          $id
     * @param                          $commentId
     * @param \Illuminate\Http\Request $request
     */
    public function edit($id, $commentId, Request $request)
    {
        $comment = NpaComments::findOrFail($commentId);


        return view(
            'adminnpa::comments.edit',
            [
                'comment' => $comment,
                'user'     => Auth::user()
            ]);
    }

}
