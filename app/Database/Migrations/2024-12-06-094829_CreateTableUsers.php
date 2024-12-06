<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableUsers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'first_name'    => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'middle_name'   => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'last_name'     => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'phone_number'  => [
                'type'       => 'VARCHAR',
                'constraint' => 15,
                'null'       => true, 
            ],
            'role'          => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'created_at'    => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at'    => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
