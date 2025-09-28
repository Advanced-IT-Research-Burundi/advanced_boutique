<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\StockProduct;
use Dom\Element;
use Illuminate\Console\Command;

class ImportProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-product';

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
        // create Category produit FIDODIDO  If not exist
        $category = $this->createCategoryIfNotExist('FIDODIDO');
        $stock = $this->createStockIfNotExist('Stock FIDODIDO');

        // Get TMP Products
        $tmpProducts = \App\Models\ProduitsTmp::all();

        // init progress bar
        $bar = $this->output->createProgressBar(count($tmpProducts));
        $bar->start();  

        foreach ($tmpProducts as $tmpProduct) {
            // Check if the product already exists
            $bar->advance();
            $existingProduct = \App\Models\Product::where('code', $tmpProduct->code)->first();
            if ($existingProduct) {
                $this->info("Product with code {$tmpProduct->code} already exists. Skipping...");
                continue;   
            }

            if(!$tmpProduct->designation) {
                $this->error("Category or Stock not found. Cannot proceed with product import.");
               
                 continue;  
            }

           $product = Product::create([
                'code' => $tmpProduct->code,
                'name' => $tmpProduct->designation,
                'description' => $tmpProduct->designation, 
                'category_id' => $category->id,
                'purchase_price' => floatval($tmpProduct->PVTTC) ,
                'sale_price_ht' => floatval($tmpProduct->PVHT) ,
                'sale_price_ttc' => floatval($tmpProduct->PVTTC) ,
                'unit' => "piece",
                'unit_id' => 1,
                'image' => "",
                'alert_quantity' => 10,
                'agency_id' => 1,
                'created_by' => 1,
                'user_id' => 1,
            ]);

            StockProduct::create([
                'product_id' => $product->id,
                'stock_id' => $stock->id,
                'product_name' => $product->name,
                'purchase_price' => $product->purchase_price,
                'sale_price_ht' => $product->sale_price_ht,
                'sale_price_ttc' => $product->sale_price_ttc,
                'quantity' => 0,
                'agency_id' => 1,
                'created_by' => 1,
                'user_id' => 1,
            ]);
        }
        
    }


    private function createCategoryIfNotExist(string $categoryName)
    {
        $category = \App\Models\Category::where('name', $categoryName)->first();

        if (!$category) {
            $category = new \App\Models\Category();
            $category->name = $categoryName;
            $category->description = 'Category for ' . $categoryName;
            $category->agency_id = 1; // Set a default agency_id or modify as needed
            $category->created_by = 1; // Set a default created_by or modify as needed
            $category->user_id = 1; // Set a default user_id or modify as needed
            $category->save();

            $this->info("Category '$categoryName' created.");
        } else {
            $this->info("Category '$categoryName' already exists.");
        }

        return  $category;
    }

    private function createStockIfNotExist($stockName)
    {
        $stock = \App\Models\Stock::where('name', $stockName)->first();

        if (!$stock) {
            $stock = new \App\Models\Stock();
            $stock->name = $stockName;
            $stock->description = 'Stock for ' . $stockName;
            $stock->agency_id = 1; // Set a default agency_id or modify as needed
            $stock->created_by = 1; // Set a default created_by or modify as needed
            $stock->user_id = 1; // Set a default user_id or modify as needed
            $stock->save();

            $this->info("Stock '$stockName' created.");
        } else {
            $this->info("Stock '$stockName' already exists.");
        }

        return  $stock;
    }
}
