<?php

namespace App\Broadcasting;

class SaleChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @return bool
     */
    public function join()
    {
        return true;
    }
}
