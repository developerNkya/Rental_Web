<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserNameToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => '255', 
                'null'       => true,
            ],
        ]);

        echo "Username column added successfully.\n";
    }

    public function down()
    {
        //
    }
}
