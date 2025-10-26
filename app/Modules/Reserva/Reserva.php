<?php

namespace App\Modules\Reserva;

use App\Modules\Usuarios\Models\Cliente;
use App\Modules\Vaga\Vaga;
use App\Modules\Veiculo\Veiculo;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reservas';

    protected $primaryKey = 'id_reserva';

    public $timestamps = false;

    protected $fillable = [
        'data',
        'hora_inicio',
        'hora_fim',
        'status',
        'cliente_id',
        'veiculo_id',
        'vaga_id',
    ];

    /**
     * Relacionamento: Uma reserva pertence a um cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id_cliente');
    }

    /**
     * Relacionamento: Uma reserva pertence a um veículo
     */
    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id', 'id_veiculo');
    }

    /**
     * Relacionamento: Uma reserva pertence a uma vaga
     */
    public function vaga()
    {
        return $this->belongsTo(Vaga::class, 'vaga_id', 'id_vaga');
    }
}
