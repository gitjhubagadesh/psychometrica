<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReferenceTables extends Migration
{
    public function up()
    {
        // psy_factor_mapping table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'main_factor_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'factor_id' => [
                'type' => 'INT',
            ],
            'report_id' => [
                'type' => 'INT',
            ],
            'status' => [
                'type' => 'TINYINT',
                'default' => 1,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('psy_factor_mapping');

        // psy_skill_statements table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'factor_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'report_for' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'experience_level' => [
                'type' => 'ENUM',
                'constraint' => ['Less than 10 years', 'More than 10 years'],
            ],
            'skill_level' => [
                'type' => 'ENUM',
                'constraint' => ['Low', 'Medium', 'High'],
            ],
            'statement' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('psy_skill_statements');
    }

    public function down()
    {
        $this->forge->dropTable('psy_skill_statements');
        $this->forge->dropTable('psy_factor_mapping');
    }
}
