<?php


namespace App\Components\Binary\Repositories;


use App\Components\Binary\Dto\CellDto;
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
}