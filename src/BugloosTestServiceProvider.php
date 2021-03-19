<?php

namespace MrAlirezaEb\BugloosTest;

use Illuminate\Support\ServiceProvider;

class BugloosTestServiceProvider extends ServiceProvider
{
  public function register()
  {
    // registering BLTable class as a facade
    $this->app->bind('bltable', function($app) {
        return new BLTable();
    });
    // config file merging
    $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'bltable');
  }

  public function boot()
  {
    if ($this->app->runningInConsole()) {

        // exporting config file to project directory for modifing
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('bltable.php'),
        ], 'config');
    }
    $this->loadViewsFrom(__DIR__.'/../resources/views', 'bltable');
  }
}