<?php

namespace App\Modules\Acesso;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Veiculo\Veiculo;
use App\Modules\Vaga\Vaga;
use App\Modules\Clientes\Cliente;

class Acesso extends Model
{
    protected $table = 'acessos';
    protected $primaryKey = 'id_acesso';
    public $timestamps = false;

    protected $fillable = [
        'data',
        'hora_inicio',
        'hora_fim',
        'valor_total',
        'veiculo_id',
        'vaga_id',
        'cliente_id'
    ];

    protected $casts = [
        'valor_total' => 'decimal:2'
    ];

    /**
     * Relacionamento: Um acesso pertence a um veículo
     */
    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id', 'id_veiculo');
    }

    /**
     * Relacionamento: Um acesso pertence a uma vaga
     */
    public function vaga()
    {
        return $this->belongsTo(Vaga::class, 'vaga_id', 'id_vaga');
    }

    /**
     * Relacionamento: Um acesso pertence a um cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id_cliente');
    }
}
