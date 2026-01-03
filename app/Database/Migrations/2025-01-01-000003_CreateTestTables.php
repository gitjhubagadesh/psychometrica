<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTestTables extends Migration
{
    public function up()
    {
        // psy_test_name table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'test_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'parent_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'position' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'created_by' => [
                'type' => 'TINYINT',
            ],
            'created_on' => [
                'type' => 'TIMESTAMP',
                'null' => false,
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('psy_test_name');

        // psy_test_factor table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'factor_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'factor_description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'prefix' => [
                'type' => 'VARCHAR',
                'constraint' => '5',
            ],
            'status' => [
                'type' => 'TINYINT',
                'default' => 1,
            ],
            'timer' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'is_mandatory' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'created_by' => [
                'type' => 'TINYINT',
            ],
            'created_on' => [
                'type' => 'TIMESTAMP',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('psy_test_factor');

        // psy_tests table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'test_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'test_description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'creator_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'factor_ids' => [
                'type' => 'JSON',
            ],
            'user_prefix' => [
                'type' => 'VARCHAR',
                'constraint' => '5',
                'null' => true,
            ],
            'test_report_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'TINYINT',
            ],
            'created_on' => [
                'type' => 'TIMESTAMP',
                'null' => false,
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('psy_tests');

        // psy_master_tests table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'test_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'creator_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'test_ids' => [
                'type' => 'JSON',
            ],
            'user_prefix' => [
                'type' => 'VARCHAR',
                'constraint' => '5',
                'null' => true,
            ],
            'test_report_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'TINYINT',
            ],
            'created_on' => [
                'type' => 'TIMESTAMP',
                'null' => false,
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('psy_master_tests');
    }

    public function down()
    {
        $this->forge->dropTable('psy_master_tests');
        $this->forge->dropTable('psy_tests');
        $this->forge->dropTable('psy_test_factor');
        $this->forge->dropTable('psy_test_name');
    }
}
