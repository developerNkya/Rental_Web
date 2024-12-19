<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLanguageIdToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'language_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true, 
                'after'      => 'last_name', 
            ],
        ]);

        
        $this->db->query("
            ALTER TABLE `users`
            ADD CONSTRAINT `fk_users_language_id`
            FOREIGN KEY (`language_id`) REFERENCES `languages`(`id`)
            ON DELETE SET NULL ON UPDATE CASCADE
        ");
    }

    public function down()
    {
        //
    }
}
