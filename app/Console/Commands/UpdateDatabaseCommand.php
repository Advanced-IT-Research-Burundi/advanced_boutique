<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Product;
use App\Models\StockProduct;
use Illuminate\Console\Command;

class UpdateDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-database-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //

        // add progress indicator
        $this->output->progressStart(100);
        //Ajouter la liste des clients de test
        for($i=0; $i<100; $i++){
            Client::create([
                'name' => 'Client ' . $i,
                'phone' => '0606060606',
                'email' => 'client' . $i . '@gmail.com',
                'address' => '123 Main St',
                'agency_id' => 1,
                'created_by' => 1,


            ]);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    }
}
