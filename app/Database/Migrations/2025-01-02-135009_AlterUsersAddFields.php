<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterUsersAddFields extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'deficity' => [
                'type'       => 'VARCHAR',
                'constraint' => '255', 
                'null'       => true,
            ],

            'status' => [
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
