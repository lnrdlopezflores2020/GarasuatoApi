<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorito extends Model
{

    protected $table = 'marca';

    public $timestamps = false;

    protected $fillable =
    [
        'ID_prod',
        'ID_usu'
    ];

}