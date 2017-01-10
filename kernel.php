<?php

require_once __DIR__ . "/vendor/autoload.php";
$capsuleConfig = include __DIR__ . "/config.php";

$capsule = new \Illuminate\Database\Capsule\Manager();
$capsule->addConnection($capsuleConfig);
$capsule->setFetchMode(PDO::FETCH_ASSOC);
$capsule->setAsGlobal();