<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRentDeadLineToTenants extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tenants', [
            'rent_deadline' => [
                'type'       => 'VARCHAR',
                'constraint' => '255', 
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        //
    }
}
