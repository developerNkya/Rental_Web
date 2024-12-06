<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterUsersAddRoleId extends Migration
{
    public function up()
    {
        
        $this->forge->addColumn('users', [
            'role_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true, 
                'after'      => 'role', 
            ],
        ]);

        
        $this->db->query("
            ALTER TABLE `users`
            ADD CONSTRAINT `fk_users_roles`
            FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`)
            ON DELETE SET NULL ON UPDATE CASCADE
        ");
    }

    public function down()
    {
        
        $this->db->query("ALTER TABLE `users` DROP FOREIGN KEY `fk_users_roles`");
        $this->forge->dropColumn('users', 'role_id');
    }
}
