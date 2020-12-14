<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use PDO;
use PDOException;

class ConfigDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'configura la base de datos del proyecto, ejecuta las migraciones y seeders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // * Aumenta el tiempo de ejecucion de php (no se usa en esta parte, pero si ejecuto este script desde un controlador)
        set_time_limit(300);

        $this->info(' >>> Configurando base da datos <<< ');

        $this->info(' >>> Ejecutando php artisan optimize <<< ');

        Artisan::call('optimize');

        $debug = env('APP_DEBUG');
        $database = env('DB_DATABASE', false);
        $host = env('DB_HOST');
        $port = env('DB_PORT');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        // $password = 'admin';

        $this->info(' >>> Conectando con el servidor de base de datos ' . $host . ':' . $port . ' <<< ');

        // Creo la conexión PDO
        $pdo = new PDO(sprintf('mysql:host=%s;port=%d;', $host, $port), $username, $password);

        $this->info(' >>> Eliminando base de datos ' . $database . ' <<< ');

        // Elimino la base de datos si existe
        $drop = $pdo->exec(sprintf(
            'drop database if exists %s;',
            $database
        ));

        $this->info(' >>> Creando base de datos ' . $database . ' <<< ');

        // Creo la base de datos
        $create = $pdo->exec(sprintf(
            'CREATE DATABASE IF NOT EXISTS %s CHARACTER SET %s COLLATE %s;',
            $database,
            'utf8mb4',
            'utf8mb4_unicode_ci'
        ));

        $this->info(' >>> Ejecutando php artisan migrate --seed <<< ');

        Artisan::call('migrate');
        Artisan::call('db:seed');

        $this->info(' >>> Configuración terminada <<< ');
    }
}
