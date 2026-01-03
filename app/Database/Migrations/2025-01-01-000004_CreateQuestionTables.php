<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuestionTables extends Migration
{
    public function up()
    {
        // psy_memory_main_image table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'question_type' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 5,
            ],
            'memory_main_image' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '5',
            ],
            'test_factor_id' => [
                'type' => 'TINYINT',
            ],
            'language_id' => [
                'type' => 'TINYINT',
                'constraint' => 1,
            ],
            'question_mark' => [
                'type' => 'TINYINT',
            ],
            'is_demo' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'comment' => '0 for demo 1 for regular',
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
            ],
            'disapearing_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('psy_memory_main_image');

        // psy_paragraph_questions table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'question_type' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 6,
            ],
            'paragraph_text' => [
                'type' => 'TEXT',
            ],
            'test_factor_id' => [
                'type' => 'TINYINT',
            ],
            'language_id' => [
                'type' => 'TINYINT',
                'constraint' => 1,
            ],
            'question_mark' => [
                'type' => 'TINYINT',
            ],
            'is_demo' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'comment' => '0 for demo 1 for regular',
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
            ],
            'disapearing_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('psy_paragraph_questions');

        // psy_questions table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'question_type' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'comment' => '1 for text question 2 for image question 3 for Image Question with Image Option 4 for Image Question with Text Option 5 for memory question 6 for paragraph question',
            ],
            'question_text' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'question_image' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'test_factor_id' => [
                'type' => 'INT',
            ],
            'language_id' => [
                'type' => 'INT',
            ],
            'question_mark' => [
                'type' => 'TINYINT',
                'default' => 1,
                'null' => true,
            ],
            'is_demo' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'comment' => '0 for demo 1 for regular',
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'memory_main_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'paragraph_question_id' => [
                'type' => 'TINYINT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('psy_questions');

        // psy_question_options table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'question_id' => [
                'type' => 'INT',
            ],
            'option_text' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'option_image' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'is_correct' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'option_mark' => [
                'type' => 'DECIMAL',
                'constraint' => '3,2',
                'default' => 0.00,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('question_id', 'psy_questions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('psy_question_options');
    }

    public function down()
    {
        $this->forge->dropTable('psy_question_options');
        $this->forge->dropTable('psy_questions');
        $this->forge->dropTable('psy_paragraph_questions');
        $this->forge->dropTable('psy_memory_main_image');
    }
}
