<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOwnerIdToCollections extends Migration
{
    public function up()
    {
        $this->forge->addColumn('collections', [
            'owner_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true, 
                'after'      => 'location', 
            ],
        ]);

        
        $this->db->query("
            ALTER TABLE `collections`
            ADD CONSTRAINT `fk_collections_owner_id`
            FOREIGN KEY (`owner_id`) REFERENCES `users`(`id`)
            ON DELETE SET NULL ON UPDATE CASCADE
        ");
    }

    public function down()
    {
        //
    }
}
