<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const REFUND_STATUS_PENDING = 'pending';
    const REFUND_STATUS_APPLIED = 'applied';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS = 'success';
    const REFUND_STATUS_FAILED = 'failed';

    const SHIP_STATUS_PENDING = 'pending';
    const SHIP_STATUS_DELIVERED = 'delivered';
    const SHIP_STATUS_RECEIVED = 'received';

    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING    => 'Not yet refund',
        self::REFUND_STATUS_APPLIED    => 'Refund applied',
        self::REFUND_STATUS_PROCESSING => 'Refund processing',
        self::REFUND_STATUS_SUCCESS    => 'Refund success',
        self::REFUND_STATUS_FAILED     => 'Refund fail',
    ];

    public static $shipStatusMap = [
        self::SHIP_STATUS_PENDING   => 'Not shipping',
        self::SHIP_STATUS_DELIVERED => 'Shipped',
        self::SHIP_STATUS_RECEIVED  => 'Delivered',
    ];

    protected $fillable = [
        'no',
        'address',
        'total_amount',
        'remark',
        'paid_at',
        'payment_method',
        'payment_no',
        'refund_status',
        'refund_no',
        'closed',
        'reviewed',
        'ship_status',
        'ship_data',
        'extra',
    ];

    protected $casts = [
        'closed'    => 'boolean',
        'reviewed'  => 'boolean',
        'address'   => 'json',
        'ship_data' => 'json',
        'extra'     => 'json',
    ];

    protected $dates = [
        'paid_at',
    ];

    protected static function boot()
    {
        parent::boot();
        //  listen the model creating event before recording it in db it will create number
        static::creating(function ($model) {
            // if the no is empty
            if (!$model->no) {
                // use findAvailableNo to create number
                $model->no = static::findAvailableNo();
                //if find stop creating the order
                if (!$model->no) {
                    return false;
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function findAvailableNo()
    {
        // prefix of the order number
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            // radomly create a 6 digit number
            $no = $prefix.str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // check if it is exist
            if (!static::query()->where('no', $no)->exists()) {
                return $no;
            }
        }
        \Log::warning('find order no failed');

        return false;
    }
}