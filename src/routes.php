<?php

/**
 * Route for news module
 */

Route::group(
    ['namespace' => 'Avl\AdminNpa\Controllers\Admin', 'middleware' => ['web', 'admin'], 'as' => 'adminnpa::'],
    function () {

        Route::group(
            ['namespace' => 'Ajax', 'prefix' => 'ajax'],
            function () {
                Route::post('/change-npa-date/{id}', 'NpaController@changeNpasDate');

                /* маршруты для работы с медиа */
                Route::post('npa-images', 'MediaController@npaImages');
                Route::post('npa-files', 'MediaController@npaFiles');
                Route::post('saveFile/{id}', 'MediaController@saveFile');
                /* маршруты для работы с медиа */
            });

        Route::get('sections/{id}/npa/move/{npa}', 'NpaController@move')->name('sections.npa.move');
        Route::get('npa/{id}/comment', 'CommentsController@index')->name('sections.npa.comment.index');
        Route::get('npa/{id}/comment/show/{comment_id}', 'CommentsController@show')->name('sections.npa.comment.show');
        Route::get('npa/{id}/comment/remove/{comment_id}', 'CommentsController@remove')->name('sections.npa.comment.remove');
        Route::post('npa/{id}/comment/reply/{comment_id}', 'CommentsController@reply')->name('sections.npa.comment.reply');
        Route::post('sections/{id}/npa/move/{npa}', 'NpaController@moveSave')->name('sections.npa.move.save');

        Route::resource('sections/{id}/npa', 'NpaController', ['as' => 'sections']);
    });


Route::group(
    ['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localizationRedirect', 'web']],
    function () {
        Route::group(
            ['namespace' => 'Avl\AdminNpa\Controllers\Site'],
            function () {
                Route::post('/npa/{id}/comment', 'NpaController@sendComment')->name('site.npa.comment.send');
                Route::get('npa/{alias}/', 'NpaController@index')->name('site.npa.index');
                Route::get('npa/{alias}/{type}', 'NpaController@index')->name('site.npa.index')->where('type', '(project|approve)');
                Route::get('npa/{alias}/{id}', 'NpaController@show')->name('site.npa.show')->where('id', '[0-9]+');
                Route::get('npa/{alias}/rubrics', 'NpaController@rubrics')->name('site.npa.rubrics');
                Route::get('npa/{alias}/rubrics/{rubric}', 'NpaController@rubricsShow')->name(
                    'site.npa.rubrics.show')->where('rubric', '[0-9]+');
            });
    });
