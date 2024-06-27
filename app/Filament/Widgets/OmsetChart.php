<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class OmsetChart extends ChartWidget
{
    use InteractsWithPageFilters;
    protected static ?string $heading = 'Omset';

    protected static ?int $sort = 1;
 
    protected function getData(): array
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();
        

        if (!is_null($this->filters['startDate'] ?? null)) {
            $startDate = Carbon::parse($this->filters['startDate']);
        }

        if (!is_null($this->filters['endDate'] ?? null)) {
            $endDate = Carbon::parse($this->filters['endDate']);
        }
        
        
        $data = Trend::model(Order::class)
            ->between(
                start: $startDate,
                end: $endDate,
            )
            ->perDay()
            ->sum('total_price');
    
        return [
            'datasets' => [
                [
                    'label' => 'Omset Perhari',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }
 
    protected function getType(): string
    {
        return 'line';
    }
}
