<?php

namespace App\Services;

use App\Models\ConsumoToken;

class ResumenConsumo
{
    public function llamadasSinMedicion(int $usuario): int
    {
        return ConsumoToken::where('id_usuario', $usuario)->whereNull('tokens_reales')->count();
    }

    public function paraUsuario(int $usuario): array
    {
        $totals = ConsumoToken::where('id_usuario', $usuario)->selectRaw('categoria, SUM(tokens_reales) as total')->groupBy('categoria')->pluck('total', 'categoria');

        return ['peliculas' => (int) ($totals['peliculas'] ?? 0), 'videojuegos' => (int) ($totals['videojuegos'] ?? 0)];
    }
}
