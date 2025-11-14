<?php

namespace App\Console\Commands;


use DB;
use Illuminate\Console\Command;
use Faker\Factory as Faker;

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

        //$faker = Faker::create();

        // Update Proforma 

        // command for adding column  exchange_rate and deleted_at, user_id  on table commandes if it does not exist

        DB::statement('ALTER TABLE commandes ADD COLUMN IF NOT EXISTS exchange_rate DECIMAL(15, 2) DEFAULT 1.00');
        DB::statement('ALTER TABLE commandes ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL');
        DB::statement('ALTER TABLE commandes ADD COLUMN IF NOT EXISTS user_id BIGINT UNSIGNED NULL DEFAULT NULL');

        // add total_price,  total_price_v to commande_details table if not exists
        DB::statement('ALTER TABLE commande_details ADD COLUMN IF NOT EXISTS total_price DECIMAL(20, 2) DEFAULT 0.00');
        DB::statement('ALTER TABLE commande_details ADD COLUMN IF NOT EXISTS total_price_v DECIMAL(20, 2) DEFAULT 0.00');
    

       
    var_dump("Done");
       
        /* // add progress indicator
        $products = Product::all();
        $stocks = Stock::all();
        $this->output->progressStart(count($products) * count($stocks));
        //stock

        foreach ($stocks as $stock) {
            foreach ($products as $product) {
                $stockProduct = StockProduct::where('stock_id', $stock->id)->where('product_id', $product->id)->first();
                if (!$stockProduct) {
                    StockProduct::create([
                        'stock_id' => $stock->id,
                        'product_name' => $product->name,
                        'product_id' => $product->id,
                        'quantity' => 20,
                        'price' => $product->sale_price,
                        'purchase_price' => $product->purchase_price,
                        'sale_price_ht' => $product->sale_price_ht,
                        'sale_price_ttc' => $product->sale_price_ttc,
                        'category_id' => $product->category_id,
                        'agency_id' =>1,//$stock->agency_id,
                    ]);
                }
                $this->output->progressAdvance();
            }
        }
 */
      //  $this->output->progressFinish();
    }
}
