<?php

require __DIR__ . '/../var/bootstrap.php';

$app = new Silex\Application(['env' => 'dev']);

require __DIR__ . '/../app/AppKernel.php';

$app->run();
