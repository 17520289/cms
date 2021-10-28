<?php

namespace App\Console\Commands;

use App\Leave;
use Carbon\Carbon;
use Illuminate\Console\Command;
use PhpSlackBot\Bot;
use App\User;
Use App\EmployeeDetails;
use App\Notifications\StandupDaily;
use Illuminate\Support\Facades\Notification;

class StandUpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'standup-slack';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user = User::all()->first();
        Notification::send($user, new StandupDaily());

    }
}
