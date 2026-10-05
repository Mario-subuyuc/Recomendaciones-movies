<?php

namespace App\Services;

class ContadorPalabras
{
    public function contar(string $texto): int
    {
        return count(preg_split('/[\s\p{Z}]+/u', $texto, -1, PREG_SPLIT_NO_EMPTY) ?: []);
    }
}
