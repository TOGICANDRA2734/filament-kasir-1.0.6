<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

/*
|--------------------------------------------------------------------------
| Build Instructions
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Illuminate\Support\Facades\App;
use Qruto\Flora\Run;

App::install('local', fn (Run $run) => $run
    ->command('key:generate')
    ->command('migrate', ['--force' => true])
    ->command('storage:link')
);

App::install('production', fn (Run $run) => $run
    ->command('key:generate', ['--force' => true])
    ->command('migrate', ['--force' => true])
    ->command('storage:link')
);

App::update('local', fn (Run $run) => $run
    ->command('migrate')
);

App::update('production', fn (Run $run) => $run
    ->command('migrate')
);
