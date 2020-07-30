<?php


namespace App\Services\Binary;


use App\Dto\Binary\CellDto;
use App\Repositories\Binary\BinaryRepository;
use LogicException;

class BinaryCellFactory
{
    /**
     * @var BinaryRepository
     */
    private BinaryRepository $repository;

    /**
     * BinaryCell constructor.
     * @param BinaryRepository $repository
     */
    public function __construct(BinaryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $parentId
     * @param int $position
     * @return CellDto
     */
    public function create(int $parentId, int $position)
    {
        $parent = $this->repository->getById($parentId);

        $this->checkParent($parent, $parentId);

        $children = $this->repository->getAllByParentId($parent->id);

        $this->checkPosition($children, $position);

        $newCell = $this->prepareCell($parent, $position);

        $this->insertCell($newCell, $parent);

        return $newCell;
    }


    /**
     * @param $parent
     * @param int $parentId
     */
    protected function checkParent($parent, int $parentId): void
    {
        if (!$parent) {
            throw new LogicException("Родителя с данным id не найдено [{$parentId}]");
        }
    }

    /**
     * @param CellDto[]|bool $children
     * @param int $position
     */
    protected function checkPosition(array $children, int $position): void
    {
        if (!in_array($position, [1, 2])) {
            throw new LogicException("Есть возможность использовать для позиции только 1 или 2 [{$position}]");
        }

        if ($children) {
            foreach ($children as $child) {
                if ($child->position == $position) {
                    throw new LogicException("Эта позиция уже занята [родитель = {$child->parent_id}, позиция = {$position}]");
                }
            }
        }
    }

    /**
     * @param int $position
     * @param $parent
     * @return CellDto
     */
    protected function prepareCell($parent, int $position): CellDto
    {
        $newCell = new CellDto();
        $newCell->position = $position;
        $newCell->path = '';
        $newCell->level = $parent->level + 1;
        $newCell->parent_id = $parent->id;

        return $newCell;
    }

    /**
     * @param CellDto $newCell
     * @param $parent
     */
    protected function insertCell(CellDto $newCell, $parent): void
    {
        $id = $this->repository->insertCell($newCell);

        $newCell->id = $id;
        $newCell->parent_id = $parent->path . '.' . $id;

        $this->repository->updatePath($id, $newCell->parent_id);
    }
}