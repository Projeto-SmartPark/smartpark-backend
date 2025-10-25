<?php

namespace App\Modules\Endereco;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Estacionamento\Estacionamento;

class Endereco extends Model
{
    protected $table = 'enderecos';
    protected $primaryKey = 'id_endereco';
    public $timestamps = false;

    protected $fillable = [
        'cep',
        'estado',
        'cidade',
        'bairro',
        'numero',
        'logradouro',
        'complemento',
        'ponto_referencia',
        'latitude',
        'longitude'
    ];

    protected $casts = [
        'latitude' => 'decimal:10',
        'longitude' => 'decimal:10'
    ];

    /**
     * Relacionamento: Um endereço está associado a apenas um estacionamento
     */
    public function estacionamento()
    {
        return $this->hasOne(Estacionamento::class, 'endereco_id');
    }
}
