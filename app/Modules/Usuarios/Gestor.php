<?php

namespace App\Modules\Usuarios;

class Gestor extends Usuario
{
    protected $table = 'gestores';
    protected $primaryKey = 'id_gestor';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'email',
        'senha',
        'cnpj',
        'usuario_id',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'id_usuario');
    }
}
