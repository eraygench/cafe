<?php

namespace App\Jobs;

use App\Events\NewOrder;
use App\Models\Desk;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $desk, public $details)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        NewOrder::broadcast($this->desk, $this->details);
    }
}
