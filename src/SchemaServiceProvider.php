<?php

namespace ITHilbert\Site;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class SchemaServiceProvider extends ServiceProvider
{

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        //Config Datei kopieren
        $this->publishes([
            __DIR__ .'/Config/config.php' => config_path('schemaOrg.php')
        ]);
    }


}
