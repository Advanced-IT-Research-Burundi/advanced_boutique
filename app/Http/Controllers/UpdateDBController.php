<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UpdateDBController extends Controller
{
    //

    public function update_database()
    {
        // Logic to trigger the database update/migration
        // This could involve calling Artisan commands or other update logic

        // create column on product prix_promotionnel if it doens't exists 

        $this->addColumn('products', 'prix_promotionnel', 'double', ['default' => 0, 'total' => 64, 'places' => 2]);
        $this->addColumn('products', 'tva', 'double', ['default' => 0, 'total' => 64, 'places' => 2]);
        //stock_products 
        $this->addColumn('stock_products', 'prix_promotionnel', 'double', ['default' => 0, 'total' => 64, 'places' => 2]);
        $this->addColumn('stock_products', 'tva', 'double', ['default' => 0, 'total' => 64, 'places' => 2]);

        return response()->json(['message' => 'Database updated successfully.']);
    }

    public function addColumn($table, $column, $type,  $options = [])
    {
        if (!\Schema::hasColumn($table, $column)) {
            \Schema::table($table, function ($table) use ($column, $type, $options) {
                // add 64, 4 if is double add total , places options
                if ($type == 'double' && !isset($options['total']) && !isset($options['places'])) {
                    $options['total'] = 64;
                    $options['places'] = 2;
                }
                $col = $table->$type($column, $options['total'] ?? null, $options['places'] ?? null);

                foreach ($options as $option => $value) {
                    if (method_exists($col, $option)) {
                        $col->$option($value);
                    }
                }
            });
        }
    }
}
