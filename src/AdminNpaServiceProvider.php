<?php

namespace Avl\AdminNpa;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Config;

class AdminNpaServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Публикуем файл конфигурации
        $this->publishes(
            [
                __DIR__ . '/../config/adminnpa.php' => config_path('adminnpa.php'),
            ]);

        $this->publishes(
            [
                __DIR__ . '/../public' => public_path('vendor/adminnpa'),
            ],
            'public');

        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'adminnpa');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Добавляем в глобальные настройки системы новый тип раздела
        Config::set('avl.sections.npa', 'Нормотивно-правовая база');

        // объединение настроек с опубликованной версией
        $this->mergeConfigFrom(__DIR__ . '/../config/adminnpa.php', 'adminnpa');

        // migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

    }

}
