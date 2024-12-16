<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleIdToTenants extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tenants', [
            'role_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true, 
                'after'      => 'last_name', 
            ],
        ]);

        
        $this->db->query("
            ALTER TABLE `tenants`
            ADD CONSTRAINT `fk_tenants_role_id`
            FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`)
            ON DELETE SET NULL ON UPDATE CASCADE
        ");
    }

    public function down()
    {
        //
    }
}
