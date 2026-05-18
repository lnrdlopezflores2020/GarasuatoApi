<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Comentario;
use App\Models\User;

class Producto extends Model
{

    protected $table = 'producto';
    protected $primaryKey = 'ID_prod';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'Precio_uni',
        'imagen'
    ];

    // 🔹 RELACIÓN CATEGORÍAS
    public function categorias()
    {
        return $this->belongsToMany(
            Categoria::class,
            'tiene',
            'ID_prod',
            'ID_cat'
        );
    }

    // 🔹 FAVORITOS (usuarios que marcaron este producto)
    public function favoritos()
    {
        return $this->belongsToMany(
            User::class,
            'marca',
            'ID_prod',
            'ID_usu'
        );
    }

    // 🔹 VERIFICAR SI ES FAVORITO
    public function esFavorito()
    {
        if (!auth()->check()) return false;

        return $this->favoritos()
            ->where('ID_usu', auth()->user()->ID_usu)
            ->exists();
    }

    // 🔹 COMENTARIOS
    public function comentarios()
    {
        return $this->hasMany(
            Comentario::class,
            'ID_prod',
            'ID_prod'
        );
        
    }

    

    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class, 'contiene', 'ID_prod', 'num_ped')
            ->withPivot('cantidad', 'precio_unitario', 'personalizacion');
    }
}