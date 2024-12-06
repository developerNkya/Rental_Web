<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPasswordToUsersTable extends Migration
{
    public function up()
    {
        
        $this->forge->addColumn('users', [
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255', 
                'null'       => false,
            ],
        ]);

        echo "Password column added successfully.\n";
    }

    public function down()
    {
        
        $this->forge->dropColumn('users', 'password');
        echo "Password column dropped.\n";
    }
}
