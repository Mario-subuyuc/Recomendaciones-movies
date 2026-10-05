<?php

namespace Tests\Unit;

use App\Exceptions\ErrorChat;
use App\Services\ConsultaCatalogo;
use PHPUnit\Framework\TestCase;

class FiltrosCatalogoTest extends TestCase
{
    private function consulta(array $filtros, mixed $cantidad = 3): string
    {
        return json_encode(['categoria' => 'videojuegos', 'estado' => 'consulta', 'cantidad' => $cantidad, 'filtros' => $filtros]);
    }

    public function test_greater_than_and_greater_or_equal_are_distinct(): void
    {
        $service = new ConsultaCatalogo;
        foreach (['>' => 'mayor a', '>=' => 'mayor o igual a'] as $operator => $words) {
            $data = $service->validar($this->consulta([['campo' => 'calificacion', 'operador' => $operator, 'valor' => 8.5]]), 'videojuegos', 'Juegos con calificación '.$words.' 8.5');
            $this->assertSame($operator, $data['filtros'][0]['operador']);
        }
        $this->expectException(ErrorChat::class);
        $service->validar($this->consulta([['campo' => 'calificacion', 'operador' => '>=', 'valor' => 8.5]]), 'videojuegos', 'Juegos con calificación mayor a 8.5');
    }

    public function test_unknown_keys_types_limits_and_operators_are_rejected(): void
    {
        $service = new ConsultaCatalogo;
        $invalid = [
            'not JSON',
            $this->consulta([], 11), $this->consulta([], '3'),
            $this->consulta([['campo' => 'titulo', 'operador' => 'OR 1=1', 'valor' => 'x']]),
            $this->consulta([['campo' => 'calificacion', 'operador' => '>', 'valor' => '8.5']]),
            $this->consulta([['campo' => 'users.password', 'operador' => '=', 'valor' => 'x']]),
            $this->consulta([['campo' => 'titulo', 'operador' => '=', 'valor' => 'x', 'sql' => 'DROP TABLE users']]),
            $this->consulta(array_fill(0, 9, ['campo' => 'titulo', 'operador' => '=', 'valor' => 'x'])),
        ];
        foreach ($invalid as $json) {
            try {
                $service->validar($json, 'videojuegos', 'Videojuegos');
                $this->fail('Se aceptó una estructura inválida.');
            } catch (ErrorChat $error) {
                $this->assertSame(422, $error->estado);
            }
        }
    }
}
