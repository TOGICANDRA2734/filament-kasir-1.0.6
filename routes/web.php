<?php

use Illuminate\Support\Facades\Route;
use App\Exports\CategoriesExport;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/export-categories', function () {
    return Excel::download(new CategoriesExport, 'template_import_produk.xlsx');
})->name('export-categories');

Route::get('/', function () {
    return redirect(route('filament.admin.auth.login'));
});
