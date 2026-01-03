<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdminAndCompanyTables extends Migration
{
    public function up()
    {
        // psy_admin_users table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'unique' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'unique' => true,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'role_level' => [
                'type' => 'INT',
                'default' => 1,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'role_id' => [
                'type' => 'INT',
                'default' => 1,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('psy_admin_users');

        // psy_companies table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'company_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'website' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'contact_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'contact_email' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'branding' => [
                'type' => 'ENUM',
                'constraint' => ['white label report', 'co branding report'],
                'null' => true,
            ],
            'company_code' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],
            'contact_phone' => [
                'type' => 'VARCHAR',
                'constraint' => '12',
                'null' => true,
            ],
            'logo_image_path' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_by' => [
                'type' => 'INT',
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('created_by');
        $this->forge->createTable('psy_companies');
    }

    public function down()
    {
        $this->forge->dropTable('psy_companies');
        $this->forge->dropTable('psy_admin_users');
    }
}
