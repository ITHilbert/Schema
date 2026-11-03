<?php

namespace ITHilbert\Schema;

use Illuminate\Support\ServiceProvider;

class SchemaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/Config/config.php', 'schemaOrg');
    }

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Config Datei kopieren
        $this->publishes([
            __DIR__.'/Config/config.php' => config_path('schemaOrg.php'),
        ]);
    }
}
