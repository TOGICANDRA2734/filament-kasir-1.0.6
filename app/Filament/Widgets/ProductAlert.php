<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ProductAlert extends BaseWidget
{
    protected static ?int $sort = 4;
    protected static ?string $heading = 'Stok Produk Rendah (< 20)';
    public function table(Table $table): Table
    {
        
        return $table
            ->query(
                Product::query()->where('stock', '<=', 10)->orderBy('stock', 'asc')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
               
                Tables\Columns\TextColumn::make('name')
                    ->description(fn (Product $record): string => ($record->category) ? $record->category->name : '-')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->color(static function ($state): string {
                        if ($state < 10) {
                            return 'danger';
                        } elseif ($state < 20) {
                            return 'warning';
                        } else {
                            return 'success';
                        }
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('Rp.')
                    ->sortable(),
            ])->defaultPaginationPageOption(5);
    }
}