<?php


namespace App\Repositories\Binary;


use App\Dto\Binary\CellDto;
use App\DB\DB;
use PDO;

class BinaryRepository
{
    private ?PDO $db;

    /**
     * BinaryRepository constructor.
     */
    public function __construct()
    {
        $this->db = DB::getDb();
    }

    /**
     * @param int $id
     * @return CellDto|bool
     */
    public function getById(int $id)
    {
        $data = [
            'id' => $id,
        ];
        $sql = 'SELECT * FROM `binary` WHERE id = :id';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return $stmt->fetchObject(CellDto::class);
    }

    /**
     * @param string $path
     * @return CellDto[]|bool
     */
    public function getAllByPath(string $path)
    {
        $data = [
            'path' => $path . '.%',
        ];
        $sql = 'SELECT * FROM `binary` WHERE path LIKE :path';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return $stmt->fetchAll(PDO::FETCH_CLASS, CellDto::class);
    }

    /**
     * @param CellDto $newCell
     * @return int
     */
    public function insertCell(CellDto $newCell): int
    {
        $data = [
            'parent_id' => $newCell->parent_id,
            'position'  => $newCell->position,
            'path'      => '',
            'level'     => $newCell->level,
        ];
        $sql = "INSERT INTO `binary` (parent_id, position, path, level) VALUES (:parent_id, :position, :path, :level)";

        $this->db->prepare($sql)->execute($data);

        return (int)$this->db->lastInsertId();
    }

    /**
     * @param int $id
     * @param string $path
     */
    public function updatePath(int $id, string $path): void
    {
        $data = [
            'id'   => $id,
            'path' => $path,
        ];
        $sql = "UPDATE `binary` SET path = :path WHERE id = :id";

        $this->db->prepare($sql)->execute($data);
    }

    /**
     * @return CellDto|bool
     */
    public function getFirst()
    {
        $sql = 'SELECT * FROM `binary` LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchObject(CellDto::class);
    }

    /**
     * @param int $parentId
     * @return CellDto[]|bool
     */
    public function getAllByParentId(int $parentId)
    {
        $data = [
            'parent_id' => $parentId,
        ];
        $sql = 'SELECT * FROM `binary` WHERE parent_id = :parent_id';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return $stmt->fetchAll(PDO::FETCH_CLASS, CellDto::class);
    }

    /**
     * @param array $parentIds
     * @return CellDto[]
     */
    public function getByIds(array $parentIds)
    {
        $sql = 'SELECT * FROM `binary` WHERE id IN ('.implode(', ', $parentIds).')';

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, CellDto::class);
    }

    /**
     * @return CellDto[]
     */
    public function getAll()
    {
        $sql = 'SELECT * FROM `binary`';

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, CellDto::class);
    }
}