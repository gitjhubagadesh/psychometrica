<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReportTables extends Migration
{
    public function up()
    {
        // psy_test_reports table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'test_report_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'report_type' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('psy_test_reports');

        // psy_report_top_skills table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'factor_id' => [
                'type' => 'INT',
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('psy_report_top_skills');

        // psy_report_metadata_definitions table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'desc_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'top_content' => [
                'type' => 'TEXT',
            ],
            'description_male_1' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'description_male_2' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'description_female_1' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'description_female_2' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('psy_report_metadata_definitions');
    }

    public function down()
    {
        $this->forge->dropTable('psy_report_metadata_definitions');
        $this->forge->dropTable('psy_report_top_skills');
        $this->forge->dropTable('psy_test_reports');
    }
}
