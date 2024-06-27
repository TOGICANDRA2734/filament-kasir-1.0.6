<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class CategoriesExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new ProductsExport(),
            new CategoriesSheetExport(),
        ];
    }
}

class ProductsExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return collect([]);
    }

    public function headings(): array
    {
        return [
            'name',
            'category_id',
            'stock',
            'price',
            'is_active',
            'barcode'
        ];
    }

    public function title(): string
    {
        return 'Products';
    }
}

class CategoriesSheetExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return Category::select('id', 'name')->get();
    }

    public function headings(): array
    {
        return [
            'id',
            'Name',
        ];
    }

    public function title(): string
    {
        return 'Categories';
    }
}
