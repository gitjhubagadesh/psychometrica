<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserTables extends Migration
{
    public function up()
    {
        // psy_user_groups table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'group_code' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
            ],
            'created_on' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('psy_user_groups');

        // psy_users table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],
            'company_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'user_type' => [
                'type' => 'INT',
            ],
            'group_id' => [
                'type' => 'INT',
            ],
            'is_master_test' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => true,
            ],
            'test_id' => [
                'type' => 'INT',
            ],
            'validity_from' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'validity_to' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'extend' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'created_by' => [
                'type' => 'INT',
                'null' => true,
            ],
            'created_on' => [
                'type' => 'TIMESTAMP',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('psy_users');

        // psy_user_registration table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
            ],
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'middle_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'designation' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'gender' => [
                'type' => 'ENUM',
                'constraint' => ['Male', 'Female', 'Transgender'],
            ],
            'dob' => [
                'type' => 'DATE',
            ],
            'city' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'company_name' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'experience' => [
                'type' => 'TINYINT',
            ],
            'country_id' => [
                'type' => 'TINYINT',
            ],
            'identification_type' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'identification_no' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ],
            'created_on' => [
                'type' => 'TIMESTAMP',
                'null' => false,
            ],
            'status' => [
                'type' => 'INT',
                'comment' => '1 for active 0 for inactive',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('psy_user_registration');

        // psy_user_answers table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
            ],
            'question_id' => [
                'type' => 'INT',
            ],
            'selected_option_id' => [
                'type' => 'INT',
            ],
            'attempt_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'answered_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('psy_user_answers');

        // psy_user_test_progress table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
            ],
            'test_id' => [
                'type' => 'INT',
            ],
            'factor_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'question_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'elapsed_time' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'total_duration' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('psy_user_test_progress');
    }

    public function down()
    {
        $this->forge->dropTable('psy_user_test_progress');
        $this->forge->dropTable('psy_user_answers');
        $this->forge->dropTable('psy_user_registration');
        $this->forge->dropTable('psy_users');
        $this->forge->dropTable('psy_user_groups');
    }
}
