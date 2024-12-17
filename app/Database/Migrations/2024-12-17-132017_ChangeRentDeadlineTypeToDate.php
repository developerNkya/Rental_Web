<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeRentDeadlineTypeToDate extends Migration
{
    public function up()
    {
        // Modify the 'rent_deadline' column to change its type to DATE
        $fields = [
            'rent_deadline' => [
                'type' => 'DATE',
                'null' => true,
            ],
        ];

        $this->forge->modifyColumn('tenants', $fields);
    }

    public function down()
    {
        $fields = [
            'rent_deadline' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
        ];

        $this->forge->modifyColumn('tenants', $fields);
    }
}
