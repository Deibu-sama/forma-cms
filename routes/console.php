<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('formacms:about', function () {
    $this->info('FormaCMS — customizable portfolio CMS starter');
})->purpose('Display FormaCMS project information');
