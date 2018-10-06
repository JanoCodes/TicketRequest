<?php

namespace Jano\Modules\TicketRequest\Console;

use Illuminate\Console\Command;
use Setting;

class OpenTicketRequests extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'request:open';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Open the creation of ticket requests';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Setting::set('system.ticket_request.open', true);
    }
}
