<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTenants extends Migration
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
            'rental_months'          => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'rental_price'          => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'owner_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true, 
            ],
            'collection_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true, 
                'after'      => 'location', 
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
        $this->forge->createTable('tenants');

        $this->db->query("
        ALTER TABLE `tenants`
        ADD CONSTRAINT `fk_tenants_owner_id`
        FOREIGN KEY (`owner_id`) REFERENCES `users`(`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
    ");

        $this->db->query("
        ALTER TABLE `tenants`
        ADD CONSTRAINT `fk_tenants_collection_id`
        FOREIGN KEY (`collection_id`) REFERENCES `collections`(`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
    ");

    }

    public function down()
    {
        //
    }
}