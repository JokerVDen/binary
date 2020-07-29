<?php

use App\Components\Binary\Cells\BinaryCell;
use App\Components\Binary\Repositories\BinaryRepository;

require_once './vendor/autoload.php';
require_once './helpers/dump.php';

$repository = new BinaryRepository();
$binaryCell = new BinaryCell($repository);

$binaryCell->create(1, 2);

system('clear');

echo PHP_EOL;