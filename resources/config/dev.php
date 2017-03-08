<?php
require __DIR__ . '/prod.php';
$app['debug'] = true;
$app['log.level'] = Monolog\Logger::DEBUG;

/*
 * Definišemo promenljivu koja sadrži podatke
 * potrebne za konekciju sa bazom podataka.
 */
$app['db.options'] = array(
    'dbname' => 'mov_db',
    'host' => 'localhost',
    'user' => 'root',
    'password' => 'maka',
    'driver' => 'pdo_mysql',
    'path' => realpath(ROOT_PATH . '/app.db'),
);
