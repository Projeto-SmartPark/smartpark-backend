<?php

namespace App\Modules\Usuarios\Models;

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
    ];

    protected $hidden = [
        'senha',
    ];

    /**
     * Relacionamento com Usuario
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_gestor', 'id_usuario');
    }

    /**
     * Verifica se o email já existe em outro gestor
     * 
     * @param string $email
     * @param int|null $idExcluir
     * @return bool
     */
    public static function emailJaExiste(string $email, ?int $idExcluir = null): bool
    {
        $query = self::where('email', $email);

        if ($idExcluir) {
            $query->where('id_gestor', '!=', $idExcluir);
        }

        return $query->exists();
    }

    /**
     * Busca gestor pelo email
     * 
     * @param string $email
     * @return Gestor|null
     */
    public static function buscarPorEmail(string $email): ?Gestor
    {
        return self::where('email', $email)->first();
    }

    /**
     * Busca gestor pelo CNPJ
     * 
     * @param string $cnpj
     * @return Gestor|null
     */
    public static function buscarPorCnpj(string $cnpj): ?Gestor
    {
        return self::where('cnpj', $cnpj)->first();
    }

    /**
     * Retorna o nome completo formatado
     * 
     * @return string
     */
    public function getNomeFormatado(): string
    {
        return ucwords(strtolower($this->nome));
    }

    /**
     * Retorna o CNPJ formatado
     * 
     * @return string
     */
    public function getCnpjFormatado(): string
    {
        $cnpj = preg_replace('/\D/', '', $this->cnpj);
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);
    }
}
