<?php

namespace App\Console\Commands;

use App\Events\UserBalanceUpdated;
use App\Models\CryptoWallet;
use Illuminate\Console\Command;

class MockEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mock:event {balance} {token_id} {user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Broadcast an event';

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
        $balance = $this->argument('balance');
        $token_id = $this->argument('token_id');
        $user_id = $this->argument('user_id');

        UserBalanceUpdated::dispatch($balance, $token_id, $user_id);
        return Command::SUCCESS;
    }
}
