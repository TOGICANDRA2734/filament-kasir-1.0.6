<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected static function boot(){
        parent::boot();

        static::creating(function ($order){
            $order->assignShift();
            $order->user_id = Auth::id();
        });
    }

    protected $fillable = [
        'name',
        'no_meja',
        'total_price',
        'shift',
        'notes',
        'payment_method_id',
        'paid_amount',
        'change_amount',
        'user_id'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'order_id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function calculateTotalPrice(): float
    {
        $totalPrice = 0;
        foreach ($this->items as $item) {
            $totalPrice += $item->quantity * $item->unit_price;
        }
        return $totalPrice;
    }

    public function assignShift()
    {
        $this->shift = self::determineShift();
    }

    public static function determineShift()
    {
        $currentHour = Carbon::now()->timezone('Asia/Singapore')->hour;

        if ($currentHour >= 9 && $currentHour < 18) {
            return 'shift 1';
        } elseif ($currentHour >= 18 && $currentHour < 24) {
            return 'shift 2';
        } else {
            return 'shift 1'; // Default to shift 1 if outside defined hours (e.g., 00:00-09:00)
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
