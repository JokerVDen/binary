<?php


namespace App\Controllers\Binary;


use App\Services\Binary\BinaryService;
use InvalidArgumentException;

class BinaryController
{
    /**
     * @var BinaryService
     */
    private BinaryService $service;

    /**
     * @return false|string
     */
    public function index()
    {
        $cells = $this->service->getAll();

        if (!$cells){
            http_response_code(404);
            return json_encode([
                'error' => true,
                'message' => 'Бинар не заполнен'
            ]);
        }

        return json_encode([
            'data' => $cells,
        ]);
    }

    /**
     * BinaryController constructor.
     * @param BinaryService $service
     */
    public function __construct(BinaryService $service)
    {
        $this->service = $service;
    }

    /**
     * @return false|string
     */
    public function seeding()
    {
        $this->service->fillByLevel(5);

        return json_encode([
            'message' => 'Бинар до 5 уровня заполнен'
        ]);
    }

    /**
     * @return false|string
     */
    public function getParents()
    {
        if (!$_REQUEST['id'])
            throw new InvalidArgumentException('Требуемый аргумент не найден [id]');

        $parents = $this->service->getParents((int) $_REQUEST['id']);

        if (!$parents){
            http_response_code(400);
            return json_encode([
                'error' => true,
                'message' => 'Родители не найдены'
            ]);
        }

        return json_encode([
            'data' => $parents,
        ]);
    }

    /**
     * @return false|string
     */
    public function getChildren()
    {
        if (!$_REQUEST['id'])
            throw new InvalidArgumentException('Требуемый аргумент не найден [id]');

        $children = $this->service->getChildren((int) $_REQUEST['id']);

        if (!$children){
            http_response_code(404);
            return json_encode([
                'error' => true,
                'message' => 'Потомков не найдено'
            ]);
        }

        return json_encode([
            'data' => $children,
        ]);
    }
}