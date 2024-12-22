<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTenantUserI extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tenants', [
            'user_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true, 
                'after'      => 'last_name', 
            ],
        ]);

        
        $this->db->query("
            ALTER TABLE `tenants`
            ADD CONSTRAINT `fk_tenants_user_id`
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
            ON DELETE SET NULL ON UPDATE CASCADE
        ");
    }

    public function down()
    {
        //
    }
}
