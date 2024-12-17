<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddElectricityFieldsToTenant extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tenants', [
            'electricity_units_from' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'electricity_units_to' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
        ]);
    }

    public function down()
    {
        //
    }
}
