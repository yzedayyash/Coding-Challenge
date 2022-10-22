<?php

namespace App\Models;

use App\Jobs\SendWarehouseEmailJob;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;
    protected $fillable = ['name' , 'unit' , 'stock','alert_on'];



    protected static function boot()
    {


        static::saved(function ($model) {
         if($model->alert_sent_at == null && $model->stock <= $model->alert_on){
            dispatch(new SendWarehouseEmailJob('test@test.com', $model->name));
            $model->alert_sent_at = \Carbon\Carbon::now();
            $model->save();
         }
        });
        parent::boot();
    }


}
