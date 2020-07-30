<?php


namespace App\Services\Binary;


use App\Dto\Binary\CellDto;
use App\Repositories\Binary\BinaryRepository;
use LogicException;

class BinaryService
{
    /**
     * @var BinaryCellFactory
     */
    private BinaryCellFactory $cell;
    /**
     * @var BinaryRepository
     */
    private BinaryRepository $repository;

    /**
     * BinaryController constructor.
     * @param BinaryCellFactory $factory
     * @param BinaryRepository $repository
     */
    public function __construct(BinaryCellFactory $factory, BinaryRepository $repository)
    {
        $this->cell = $factory;
        $this->repository = $repository;
    }

    /**
     * @param $maxLevel
     */
    public function fillByLevel($maxLevel)
    {
        $parent = $this->repository->getFirst();

        $this->fillDip($parent, $maxLevel);
    }

    /**
     * @param int $id
     * @return CellDto[]
     */
    public function getParents(int $id)
    {
        $child = $this->repository->getById($id);
        if (!$child)
            throw new LogicException("По этому id ничего не найдено! [{$id}]");

        if (!$parentIds = $this->getParentIds($child))
            throw new LogicException('Это корневой элемент! У него нет родителей!');

        return $this->repository->getByIds($parentIds);
    }

    /**
     * @param int $id
     * @return CellDto[]|bool
     */
    public function getChildren(int $id)
    {
        $parent = $this->repository->getById($id);
        if (!$parent)
            throw new LogicException("По этому id ничего не найдено! [{$id}]");

        return $this->repository->getAllByPath($parent->path);
    }

    /**
     * @param CellDto $parent
     * @param int $maxLevel
     * @param int $position
     */
    private function fillDip(CellDto $parent, int $maxLevel = 5, $position = 0)
    {
        if ($parent->level + 1 > $maxLevel)
            return;

        $this->fillWidth($parent, $maxLevel, $position);
    }

    /**
     * @param CellDto $parent
     * @param int $maxLevel
     * @param int $position
     * @return void
     */
    private function fillWidth(CellDto $parent, int $maxLevel, int $position = 0)
    {
        if (++$position > 2)
            return;

        $newCell = $this->cell->create($parent->id, $position);
        $this->fillDip($newCell, $maxLevel);
        $this->fillWidth($parent, $maxLevel, $position);
    }

    /**
     * @param $child
     * @return false|string[]
     */
    private function getParentIds($child)
    {
        $pathArray = explode('.', $child->path);
        array_pop($pathArray);
        return $pathArray;
    }

    /**
     * @return CellDto[]
     */
    public function getAll()
    {
        return $this->repository->getAll();
    }
}