<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;
use App\Models\Order;
use App\Models\Expense;
use Illuminate\Support\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;
    protected static ?int $sort = 0;
    protected function getStats(): array
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();
        

        if (!is_null($this->filters['startDate'] ?? null)) {
            $startDate = Carbon::parse($this->filters['startDate']);
        }

        if (!is_null($this->filters['endDate'] ?? null)) {
            $endDate = Carbon::parse($this->filters['endDate']);
        }

        
        $product_count = Product::count();
        $order_count = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $omset = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total_price');
       
        
        $expense = Expense::whereBetween('date_expense', [$startDate, $endDate])->sum('amount');
        return [
            Stat::make('Total Produk', $product_count),
            Stat::make('Total Order', $order_count),
            Stat::make('Omset', 'Rp. '.$omset),
            Stat::make('Pengeluaran', 'Rp. '.$expense)
        ];
    }
}
