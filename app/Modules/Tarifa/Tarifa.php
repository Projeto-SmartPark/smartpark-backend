<?php

namespace App\Modules\Tarifa;

use App\Modules\Estacionamento\Estacionamento;
use Illuminate\Database\Eloquent\Model;

class Tarifa extends Model
{
    protected $table = 'tarifas';

    protected $primaryKey = 'id_tarifa';

    public $timestamps = false;

    protected $fillable = [
        'nome',
        'valor',
        'tipo',
        'estacionamento_id',
        'ativa',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
    ];

    /**
     * Relacionamento: Uma tarifa pertence a um estacionamento
     */
    public function estacionamento()
    {
        return $this->belongsTo(Estacionamento::class, 'estacionamento_id', 'id_estacionamento');
    }
}
