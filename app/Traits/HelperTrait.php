<?php

namespace App\Traits;

trait HelperTrait
{

    public function convertQuantityUnit($from_unit , $to_unit , $quantity){
        $converted_quantity = $quantity;
        if ($from_unit == 'g' && $to_unit == 'kg') {
            $converted_quantity =  $quantity / 1000;
        }
        if ($from_unit == 'kg' && $to_unit == 'g') {
            $converted_quantity =  $quantity * 1000;
        }

        $converted_quantity = number_format($converted_quantity, 2, '.', '');
        return $converted_quantity;
    }
}
