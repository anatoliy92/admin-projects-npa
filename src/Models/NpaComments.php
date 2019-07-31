<?php namespace Avl\AdminNpa\Models;

use App\Models\Langs;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelTrait;
use LaravelLocalization;
use App\Models\Media;

class NpaComments extends Model
{
    use ModelTrait;

    protected $table     = 'npa_comments';

    protected $modelName = __CLASS__;

    protected $fillable  = ['comment_ru'];

    protected $lang      = null;

    public function __construct()
    {
        $this->lang = LaravelLocalization::getCurrentLocale();
    }

    public function npa()
    {
        return $this->belongsTo('Avl\AdminNpa\Models\Npa', 'npa_id', 'id');
    }

    public function author()
    {
        return $this->belongsTo('App\Models\User', 'created_user', 'id');
    }

    public function getCommentAttribute($value, $lang = null)
    {
        $comment = (!is_null($lang)) ? $lang : $this->lang;

        return ($this->{'comment_' . $comment}) ? $this->{'comment_' . $comment} : null;
    }

    public function getComment()
    {
        foreach (Langs::all() as $lang) {
            if ($this->{'comment_' . $lang->key}) {
                return $this->{'comment_' . $lang->key};
            }
        }
    }

    public function getCommentLang()
    {
        foreach (Langs::all() as $lang) {
            if ($this->{'comment_' . $lang->key}) {
                return $lang->key;
            }
        }
    }

    static function getList($comments)
    {
        $list = [];

        foreach ($comments as $comment) {
            if (is_null($comment->comment_id)) {
                $list[$comment->id]['comment'] = $comment;
                $list[$comment->id]['replies'] = [];
            } else {
                $list[$comment->comment_id]['replies'][$comment->id]['comment'] = $comment;
            }
        }

        return $list;
    }
}
