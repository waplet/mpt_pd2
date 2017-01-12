<?php
header("Content-Type: text/html; charset=UTF-8");

require_once __DIR__ . "/kernel.php";


// $file = 'JSON_TestData/JSONFirstRound/futbols2.json';
// $file = 'JSON_TestData/JSONSecondRound/futbols2.json';
//
// $loader = new \BigF\Managers\Loaders\JsonLoader($file);
// $importer = new \BigF\Managers\Importer($loader->load());

// echo "Worked";

echo "<pre>";
var_dump(\BigF\Managers\Report::mainTable());