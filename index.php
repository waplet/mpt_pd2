<?php
header("Content-Type: text/html; charset=UTF-8");

require_once __DIR__ . "/kernel.php";


$file = 'JSON_TestData/JSONFirstRound/futbols0.json';


$data = json_decode(file_get_contents($file), true);


$importer = new \BigF\Managers\Importer($data);