<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterTenantsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tenants', [
            'rent_contract' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true, 
                'after'      => 'collection_id', 
            ],
        ]);

    }

    public function down()
    {
        //
    }
}
