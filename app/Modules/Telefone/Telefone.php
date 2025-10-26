<?php

namespace App\Modules\Telefone;

use App\Modules\Estacionamento\Estacionamento;
use Illuminate\Database\Eloquent\Model;

class Telefone extends Model
{
    protected $table = 'telefones';

    protected $primaryKey = 'id_telefone';

    public $timestamps = false;

    protected $fillable = [
        'ddd',
        'numero',
    ];

    /**
     * Relacionamento: Um telefone pode estar associado a vários estacionamentos (many-to-many)
     */
    public function estacionamentos()
    {
        return $this->belongsToMany(
            Estacionamento::class,
            'estacionamento_telefones',
            'id_telefone',
            'id_estacionamento'
        );
    }
}
