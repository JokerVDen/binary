<?php

use App\Controllers\Binary\BinaryController;
use App\Repositories\Binary\BinaryRepository;
use App\Services\Binary\BinaryCellFactory;
use App\Services\Binary\BinaryService;

require_once './vendor/autoload.php';
require_once './helpers/dump.php';

$result = false;

try {
    if (!empty($_REQUEST['dispatch'])) {
        $dispatch = explode('.', $_REQUEST['dispatch']);
        $controller = $dispatch[0];
        $action = $dispatch[1] ?? null;

        if ($controller == 'binary') {
            $actions = [
                'index'    => 'index',
                'seeding'  => 'seeding',
                'parents'  => 'getParents',
                'children' => 'getChildren',
            ];

            if (!empty($actions[$action])) {
                $repository = new BinaryRepository();
                $factory = new BinaryCellFactory($repository);
                $binaryService = new BinaryService($factory, $repository);
                $controller = new BinaryController($binaryService);
                $result = $controller->{$actions[$action]}();
            }
        }
    }
} catch (LogicException $exception) {
    http_response_code(409);
    $result = json_encode([
        'error'   => true,
        'message' => $exception->getMessage(),
    ]);
} catch (Throwable $exception) {
    http_response_code(500);
    $result = json_encode([
        'error'   => true,
        'message' => $exception->getMessage(),
        'file'    => $exception->getFile(),
        'line'    => $exception->getLine(),
        'trace'   => $exception->getTrace(),
    ]);
}

if (!$result) {
    http_response_code(404);
    $result = json_encode([
        'error'   => true,
        'message' => 'Not found!',
    ]);
}

header('Content-Type: application/json');
echo $result;