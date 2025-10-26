<?php

namespace App\Modules\Veiculo;

use App\Modules\Usuarios\Models\Cliente;
use Illuminate\Database\Eloquent\Model;

class Veiculo extends Model
{
    protected $table = 'veiculos';

    protected $primaryKey = 'id_veiculo';

    public $timestamps = false;

    protected $fillable = [
        'placa',
        'cliente_id',
    ];

    /**
     * Relacionamento: Um veículo pertence a um cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id_cliente');
    }
}
