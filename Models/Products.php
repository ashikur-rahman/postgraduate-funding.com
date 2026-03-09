<?php

namespace App\Models;

use CodeIgniter\Model;

class Product extends Model
{
    protected $table      = 'products';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name',
        'image',
        'description',
        'duration_in_days',
        'price',
        'currency',
        'status'
    ];

    /**
     * ---------------------------------------------------------
     * Get Product Rows
     * ---------------------------------------------------------
     * Returns all products or a specific product by ID.
     *
     * @param int|bool $id
     * @return array
     */
    public function getRows($id = false)
    {
        if ($id === false) {
            return $this->findAll();
        }

        return $this->getWhere(['id' => $id])->getResultArray();
    }


    /**
     * ---------------------------------------------------------
     * Get Single Active Product
     * ---------------------------------------------------------
     * Retrieves a product by ID where status is active.
     *
     * @param int $id
     * @return array
     */
    public function getSingleRow($id)
    {
        $db = \Config\Database::connect();

        $builder = $db->table($this->table);

        return $builder
            ->getWhere([
                'id'     => $id,
                'status' => '1'
            ])
            ->getResultArray();
    }
}