<?php

namespace App\Modules\Usuarios\Models;

use Illuminate\Database\Eloquent\Model;

abstract class Usuario extends Model
{
    protected $primaryKey = 'id_usuario';
     public $timestamps = false;

    protected $fillable = [
        'perfil',
    ];

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'usuario_id', 'id_usuario');
    }

    public function gestor()
    {
        return $this->hasOne(Gestor::class, 'usuario_id', 'id_usuario');
    }
}
