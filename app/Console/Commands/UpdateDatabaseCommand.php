<?php

namespace App\Console\Commands;

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
        $products = Product::all();
        // add progress indicator
        $this->output->progressStart(count($products));
        //StockProduct::truncate();
        foreach ($products as $product) {
            StockProduct::create([
                'stock_id' => 1,
                'product_name' => $product->name,
                'product_id' => $product->id,
                'quantity' => 10,
                'agency_id' => 1,
            ]);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    }
}
