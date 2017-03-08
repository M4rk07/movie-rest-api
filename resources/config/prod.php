<?php
$app['log.level'] = Monolog\Logger::ERROR;
$app['api.version'] = "v1";
$app['api.endpoint'] = "/api";

/**
 * SQLite database file
 */
$app['db.options'] = array(
    'dbname' => 'mov_db',
    'host' => 'localhost',
    'user' => 'root',
    'password' => 'maka',
    'driver' => 'pdo_mysql',
    'path' => realpath(ROOT_PATH . '/app.db'),
);
