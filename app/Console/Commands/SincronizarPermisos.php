<?php

namespace App\Console\Commands;

use App\Services\CatalogoPermisos;
use Illuminate\Console\Command;

class SincronizarPermisos extends Command
{
    protected $signature = 'permisos:sincronizar';

    protected $description = 'Registrar permisos de módulos sin borrar permisos ni reemplazar los accesos configurados';

    public function handle(CatalogoPermisos $catalogo): int
    {
        $count = $catalogo->sincronizar();
        $this->info("Permisos sincronizados: {$count} nuevos. Se conservaron los accesos de los roles editables.");

        return self::SUCCESS;
    }
}
