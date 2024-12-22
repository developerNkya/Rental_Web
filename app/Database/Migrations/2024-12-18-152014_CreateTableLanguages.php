<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableLanguages extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'   => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
        ]);

        $this->forge->addKey('id', true); 
        $this->forge->createTable('languages');
    }

    public function down()
    {
        //
    }
}