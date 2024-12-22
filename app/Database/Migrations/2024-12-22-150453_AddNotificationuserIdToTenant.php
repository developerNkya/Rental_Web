<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNotificationuserIdToTenant extends Migration
{
    public function up()
    {
        // Add columns to tenants table
        $fields = [
            'user_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => false,
                'after'      => 'id',
            ],
            'notified_at' => [
                'type'       => 'DATE',
                'null'       => true,
                'after'      => 'rent_deadline',
            ],
        ];

        $this->forge->addColumn('tenants', $fields);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->db->query('CREATE INDEX idx_rent_deadline ON tenants (rent_deadline)');
        $this->db->query('CREATE INDEX idx_notified_at ON tenants (notified_at)');
    }

    public function down()
    {
        // Drop columns and indexes
        $this->forge->dropForeignKey('tenants', 'tenants_user_id_foreign');
        $this->forge->dropColumn('tenants', 'user_id');
        $this->forge->dropColumn('tenants', 'notified_at');
        $this->db->query('DROP INDEX IF EXISTS idx_rent_deadline ON tenants');
        $this->db->query('DROP INDEX IF EXISTS idx_notified_at ON tenants');
    }
}
