<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Personalizado extends Model
{

    protected $table = 'personalizado';

    protected $primaryKey = 'ID_personalizado';

    public $timestamps = false;

    protected $fillable = [

        'ID_usu',
        'ID_prod',
        'json_diseno',
        'imagen_preview',
        'fecha_creacion'

    ];


    // RELACIÓN USUARIO
    public function usuario()
    {

        return $this->belongsTo(
            User::class,
            'ID_usu',
            'ID_usu'
        );

    }


    // RELACIÓN PRODUCTO
    public function producto()
    {

        return $this->belongsTo(
            Producto::class,
            'ID_prod',
            'ID_prod'
        );

    }

}