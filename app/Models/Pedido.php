<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    
    protected $table = 'pedido';
    protected $primaryKey = 'num_ped';
    public $incrementing = true;
    public $timestamps = false;
    
    protected $fillable = [
        'ID_usu',
        'estado',
        'pago_total'

        
    ];

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'contiene', 'num_ped', 'ID_prod')
            ->withPivot(
                'ID_contiene',
                'cantidad',
                'precio_unitario',
                'personalizacion'
            );
    }
}
