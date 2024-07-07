<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;

class BiggestOrder extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 6;
    protected static ?string $heading = 'Order Terbesar';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $startDate = now()->startOfDay();
        $endDate = now()->endOfDay();

        if (!is_null($this->filters['startDate'] ?? null)) {
            $startDate = Carbon::parse($this->filters['startDate'])->startOfDay();
        }

        if (!is_null($this->filters['endDate'] ?? null)) {
            $endDate = Carbon::parse($this->filters['endDate'])->endOfDay();
        }

        return $table
            ->query( function() use ($startDate, $endDate){
                return Order::query()->whereBetween('created_at', [$startDate, $endDate])->orderBy('total_price', 'desc')->limit(5);
            })
            
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('PIC')
                    ->sortable(),
                Tables\Columns\TextColumn::make('shift')
                    ->label('Shift')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Customer')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()
                        ->label('Total')
                        ->numeric()
                    ),
            ])
            ->filters([
                SelectFilter::make('shift')
                    ->options([
                        'Shift 1' => 'Shift 1',
                        'Shift 2' => 'Shift 2',
                    ])
                    ->label('Shift'),
            ])
            ->groups([
                Tables\Grouping\Group::make('user.name')
                ->label('PIC')
                // ->getTitleFromRecordUsing('user.name')
                ->collapsible(),
            ]);
    }
}
