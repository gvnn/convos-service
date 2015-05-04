<?php namespace App\Handlers\Events;

use App\Events\illuminate;
use Illuminate\Support\Facades\Log;

class DatabaseQueryListener
{

    /**
     * Create the event handler.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  illuminate .query  $event
     * @return void
     */
    public function handle($event)
    {
        if (env('APP_DEBUG', false)) {
            Log::info($event);
        }
    }

}