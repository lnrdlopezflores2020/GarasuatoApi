<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Comentario extends Model
{
    protected $table = 'comentario';

    protected $primaryKey = 'ID_com'; 

    public $timestamps = false;

    protected $fillable = [
        'ID_prod',
        'ID_usu',
        'comentario',
        'calificacion'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'ID_usu');
    }
}