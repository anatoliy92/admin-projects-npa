<?php namespace Avl\AdminNpa\Controllers\Admin\Ajax;

use App\Http\Controllers\Avl\AvlController;
use App\Models\{Sections, Media, Langs};
use Illuminate\Support\Facades\Storage;
use Avl\AdminNpa\Models\Npa;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use Carbon\Carbon;
use Image;
use Hash;

class MediaController extends AvlController
{

    /**
     * Загрузка изображений
     *
     * @param Request $request
     * @return JSON
     */
    public function npaImages(Request $request)
    {
        if ($request->Filedata->getSize() < config('adminnpa.max_file_size')) {

            if (in_array(strtolower($request->Filedata->extension()), config('adminnpa.valid_image_types'))) {

                $npa = Npa::where('section_id', $request->input('section_id'))->find($request->input('npa_id'));

                if ($npa) {
                    $sind = $npa->media()->orderBy('sind', 'DESC')->first();
                    $item = ($sind) ? ++$sind->sind : 1;

                    $picture               = new Media;
                    $picture->model        = 'Avl\AdminNpa\Models\Npa';
                    $picture->model_id     = $npa->id;
                    $picture->type         = 'image';
                    $picture->sind         = $item;
                    $picture->title_ru     = $request->Filedata->getClientOriginalName();
                    $picture->published_at = Carbon::now();

                    if ($picture) {

                        /* Загружаем файл и получаем путь */
                        $path = $request->Filedata->store(config('adminnpa.path_to_image'));

                        $img = Image::make(Storage::get($path));
                        $img->resize(
                            1000,
                            1000,
                            function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            })->stream();

                        Storage::put($path, $img);

                        $picture->url = $path;

                        if ($picture->save()) {
                            return [
                                'success' => true,
                                'file'    => Media::find($picture->id)->toArray(),
                                'storage' => env('STORAGE_URL')
                            ];
                        }

                        $picture->delete();
                    }
                }

                return ['errors' => ['Ошибка загрузки, обратитесь к администратору.']];
            }

            return ['errors' => ['Ошибка загрузки, формат изображения не допустим для загрузки.']];
        }

        return ['errors' => ['Размер фотографии не более <b>12-х</b> мегабайт.']];
    }

    /**
     * Загрузка файлов
     *
     * @param Request $request
     * @return JSON
     */
    public function npaFiles(Request $request)
    {
        $npa = Npa::where('section_id', $request->input('section_id'))->find($request->input('npa_id'));

        if ($npa) {
            $sind = $npa->media('file')->orderBy('sind', 'DESC')->first();
            $item = ($sind) ? ++$sind->sind : 1;

            $media = new Media();

            $media->model                                = 'Avl\AdminNpa\Models\Npa';
            $media->model_id                             = $npa->id;
            $media->good                                 = 1;
            $media->type                                 = 'file';
            $media->sind                                 = $item;
            $media->lang                                 = $request->input('lang');
            $media->{'title_' . $request->input('lang')} = $request->Filedata->getClientOriginalName();
            $media->published_at                         = Carbon::now();

            if ($npa->type == 2) {
                $oldFile = $npa->media('file')->find($npa->mainFile);

                if ($oldFile) {
                    $media->fullName = $oldFile->fullName;
                }
            }

            if ($media->save()) {
                $path = $request->Filedata->store(config('adminnpa.path_to_file'));

                if ($path) {
                    $media->url = $path;

                    if ($media->save()) {
                        if ($npa->type == 2) {
                            $npa->mainFile = $media->id;
                            $npa->save();
                        }

                        return [
                            'success' => true,
                            'file'    => $media->toArray()
                        ];
                    }

                    $media->delete();
                }
            }
        }

        return ['errors' => ['Ошибка загрузки, обратитесь к администратору.']];
    }



    /**
     * Сохранение данных файла
     * @param  integer  $id      Номер записи
     * @param  Request $request
     * @return JSON
     */
    public function saveFile ($id, Request $request)
    {
        $media = Media::find($id);
        $npa = Npa::find($media->model_id);

        if (!is_null($media)) {

            $media->{'title_' . ($media->lang ? $media->lang : 'ru')} = $request->input('title');
            $post = $request->post();

            if (isset($post['published_at'])) {
                $media->published_at = $post['published_at'];
            }

            if (isset($post['fullTitle'])) {
                $media->fullName = $post['fullTitle'];
            }

            if (isset($post['regNumber'])) {
                $media->regNumber = $post['regNumber'];
            }

            if ($request->has('main') && !is_null($media)) {
                $npa->mainFile = $media->id;
                $npa->save();
            }

            if ($media->save()) {
                return ['success' => ['Сохранено!!!']];
            }
        }

        return ['errors' => ['Ошибка, файл не найден.']];
    }

}
