<?php

namespace App\Models;

use CodeIgniter\Model;

class Collection extends Model
{
    protected $table = 'collections';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name','location','owner_id'];
    protected $returnType = 'array';
}
