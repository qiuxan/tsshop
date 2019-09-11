<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Order;

class CloseOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, $delay)
    {
        //

        $this->order = $order;
        // set the  delay time unit is second
        $this->delay($delay);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //

        // check whether the has been paid or not
        //  if paid no need to close just exist
        if ($this->order->paid_at) {
            return;
        }
        \DB::transaction(function() {
            // make the order closed meaning set the closed file to true
            $this->order->update(['closed' => true]);
            // add the stock back when closed an order
            foreach ($this->order->items as $item) {
                $item->productSku->addStock($item->amount);
            }
        });

    }
}
