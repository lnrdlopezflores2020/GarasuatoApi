<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Rol;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'usuario';
    protected $primaryKey = 'ID_usu';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'ape_pat',
        'ape_mat',
        'correo',
        'contrasena',
        'telefono',
        'calle',
        'num_ext',
        'num_int',
        'colonia',
        'municipio',
        'estado',
        'CP',
        'ID_rol',
    ];

    protected $hidden = [
        'contrasena',
    ];

    // Laravel usará "contrasena" como password
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    // Laravel usará "correo" como identificador de login
    public function getAuthIdentifierName()
    {
        return 'correo';
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'ID_rol', 'ID_rol');
    }

    // Marca de favoritos
    public function favoritos()
    {
        return $this->belongsToMany(
            Producto::class,
            'marca',
            'ID_usu',
            'ID_prod'
        );
    }
}