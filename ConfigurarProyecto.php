<?php

if(file_exists('vendor')){
    echo 'Proyecto instalado';
    shell_exec('composer dump-autoload');
}else{
    echo 'Instalando proyecto';
    shell_exec('composer install');
    shell_exec('npm install');
}
    echo shell_exec('@php artisan optimize');

    if(file_exists('app/Console/Commands/ConfigDB.php')){
        echo shell_exec('@php artisan db:config');
    }

    shell_exec('npm run dev');
    echo shell_exec('npm run watch');
?>