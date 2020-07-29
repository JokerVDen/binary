<?php


namespace App\Components\Binary\Cells;


use App\Components\Binary\Dto\CellDto;
use App\Components\Binary\Repositories\BinaryRepository;
use LogicException;

class BinaryCell
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
     */
    public function create(int $parentId, int $position)
    {
        $parent = $this->repository->getById($parentId);

        $this->checkParent($parent, $parentId);

        $children = $this->repository->getAllByPath($parent->path);

        $this->checkPosition($children, $position);

        $newCell = $this->prepareCell($parent, $position);

        $this->insertCell($newCell, $parent);
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
                    throw new LogicException("Эта позиция уже занята [{$position}]");
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

        $this->repository->updatePath($id, $parent->path . '.' . $id);
    }
}