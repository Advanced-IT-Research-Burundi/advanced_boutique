<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\CommandeDetails;
use App\Models\Product;
use App\Models\Proforma;
use App\Models\Stock;
use App\Models\StockProduct;
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

        $detailsCommande = CommandeDetails::all();
        foreach ($detailsCommande as $detail) {
            $detail->total_weight = $detail->quantity * $detail->weight_kg;
            $detail->save();
        }

       
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
