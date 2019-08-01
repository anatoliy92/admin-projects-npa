<?php namespace Avl\AdminNpa\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelTrait;
use LaravelLocalization;
use App\Models\Media;

class Npa extends Model
{
    use ModelTrait;

    protected $table     = 'npa';

    protected $modelName = __CLASS__;

    protected $fillable  = ['title_ru'];

    protected $lang      = null;

    public function __construct()
    {
        $this->lang = LaravelLocalization::getCurrentLocale();
    }

    public function section()
    {
        return $this->belongsTo('App\Models\Sections', 'section_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany('Avl\AdminNpa\Models\NpaComments', 'npa_id', 'id');
    }

    public function rubric()
    {
        return $this->belongsTo('App\Models\Rubrics', 'rubric_id', 'id');
    }

    public function media($type = 'image')
    {
        return Media::whereModel('Avl\AdminNpa\Models\Npa')->where('model_id', $this->id)->where('type', $type);
    }

    public function files()
    {
        return Media::whereModel('Avl\AdminNpa\Models\Npa')->where('model_id', $this->id)->where('type', 'file');
    }

    public function getUpdatedAtAttribute($value)
    {
        return (!is_null($this->updated_date)) ? $this->updated_date : $value;
    }

    public function getGoodAttribute($value, $lang = null)
    {
        $good = (!is_null($lang)) ? $lang : $this->lang;

        return ($this->{'good_' . $good}) ? $this->{'good_' . $good} : $this->good_ru;
    }

    public function getTitleAttribute($value, $lang = null)
    {
        $title = (!is_null($lang)) ? $lang : $this->lang;

        return ($this->{'title_' . $title}) ? $this->{'title_' . $title} : null;
    }

    public function getShortAttribute($value, $lang = null)
    {
        $short = (!is_null($lang)) ? $lang : $this->lang;

        return ($this->{'short_' . $short}) ? $this->{'short_' . $short} : $this->short_ru;
    }

    public function getFullAttribute($value, $lang = null)
    {
        $full = (!is_null($lang)) ? $lang : $this->lang;

        return ($this->{'full_' . $full}) ? $this->{'full_' . $full} : $this->full_ru;
    }

    public function getUrlAttribute($value, $lang = null)
    {
        return '/' . $this->lang . '/' . $this->section->type . '/' . $this->section->alias . '/' . $this->id;
    }

    public function isCommentable()
    {
        return $this->commented_until_date > Carbon::now();
    }

    public function isOld()
    {
        return $this->until_date < Carbon::now();
    }
}
