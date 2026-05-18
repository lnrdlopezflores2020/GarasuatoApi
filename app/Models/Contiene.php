<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contiene extends Model
{
    protected $table = 'contiene';
    public $timestamps = false;

    protected $primaryKey = 'ID_contiene';
    public $incrementing = true;

    protected $fillable = [
        'num_ped',
        'ID_prod',
        'cantidad',
        'precio_unitario',
        'personalizacion'
    ];

}
