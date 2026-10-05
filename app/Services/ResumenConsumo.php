<?php

namespace App\Services;

use App\Models\ConsumoToken;

class ResumenConsumo
{
    public function paraUsuario(int $usuario): array
    {
        $totals = ConsumoToken::where('id_usuario', $usuario)->selectRaw('categoria, SUM(tokens) as total')->groupBy('categoria')->pluck('total', 'categoria');

        return ['peliculas' => (int) ($totals['peliculas'] ?? 0), 'videojuegos' => (int) ($totals['videojuegos'] ?? 0)];
    }
}
