<?php

namespace App\Modules\Estacionamento;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Usuarios\Models\Gestor;
use App\Modules\Endereco\Endereco;

class Estacionamento extends Model
{
    protected $table = 'estacionamentos';
    protected $primaryKey = 'id_estacionamento';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'capacidade',
        'hora_abertura',
        'hora_fechamento',
        'lotado',
        'gestor_id',
        'endereco_id'
    ];

    protected $casts = [
        'capacidade' => 'integer'
    ];

    /**
     * Relacionamento: Um estacionamento pertence a um gestor
     */
    public function gestor()
    {
        return $this->belongsTo(Gestor::class, 'gestor_id', 'id_gestor');
    }

    /**
     * Relacionamento: Um estacionamento pertence a um endereço
     */
    public function endereco()
    {
        return $this->belongsTo(Endereco::class, 'endereco_id', 'id_endereco');
    }
}
