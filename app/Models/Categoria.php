<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categoria';
    protected $primaryKey = 'ID_cat';
    public $timestamps = false;

    public function productos()
    {
        return $this->belongsToMany(
            Producto::class,
            'tiene',
            'ID_cat',
            'ID_prod'
        );
    }
}
