<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCoreTables extends Migration
{
    public function up()
    {
        // ci_sessions table (required by CodeIgniter)
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => '128',
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
            ],
            'timestamp' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'data' => [
                'type' => 'BLOB',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('timestamp');
        $this->forge->createTable('ci_sessions');

        // psy_languages table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'language' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('psy_languages');

        // psy_countries table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'code' => [
                'type' => 'CHAR',
                'constraint' => '2',
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('psy_countries');

        // psy_user_type table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'section_name' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('psy_user_type');
    }

    public function down()
    {
        $this->forge->dropTable('psy_user_type');
        $this->forge->dropTable('psy_countries');
        $this->forge->dropTable('psy_languages');
        $this->forge->dropTable('ci_sessions');
    }
}
