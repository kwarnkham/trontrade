<?php

namespace App\Console\Commands;

use App\Models\NetworkTransaction;
use Illuminate\Console\Command;

class SyncNetworkTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:networkTransaction {model} {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync the transactions from the network';

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
        $id = $this->option('id');
        $model = $this->argument('model');
        if (!in_array($model, ['Purchase', 'Withdraw', 'Approval', 'CryptoWallet'])) return;
        $x = call_user_func("\\App\\Models\\" . ucwords($model) . '::find', $id);
        switch ($model) {
            case 'Purchase':
                NetworkTransaction::recordTrade($x);
                break;
            case 'Withdraw':
                NetworkTransaction::recordWithdraw($x);
                break;
            case 'Approval':
                NetworkTransaction::recordApproval($x);
                break;
            case 'CryptoWallet':
                NetworkTransaction::recordWalletActivation($x);
                break;
            default:
                # code...
                break;
        }

        echo "Finished";
        return Command::SUCCESS;
    }
}
