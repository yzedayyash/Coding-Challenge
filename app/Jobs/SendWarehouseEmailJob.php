<?php

namespace App\Jobs;

use App\Mail\WarehouseEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWarehouseEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $email;
    protected $ingredient;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email , $ingredient)
    {
        $this->email = $email;
        $this->ingredient = $ingredient;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mail = new WarehouseEmail($this->ingredient);
        Mail::to($this->email)->send($mail);
    }
}
