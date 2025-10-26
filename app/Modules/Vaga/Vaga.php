<?php

namespace App\Modules\Vaga;

use App\Modules\Estacionamento\Estacionamento;
use Illuminate\Database\Eloquent\Model;

class Vaga extends Model
{
    protected $table = 'vagas';

    protected $primaryKey = 'id_vaga';

    public $timestamps = false;

    protected $fillable = [
        'identificacao',
        'tipo',
        'disponivel',
        'estacionamento_id',
    ];

    /**
     * Relacionamento: Uma vaga pertence a um estacionamento
     */
    public function estacionamento()
    {
        return $this->belongsTo(Estacionamento::class, 'estacionamento_id', 'id_estacionamento');
    }
}
