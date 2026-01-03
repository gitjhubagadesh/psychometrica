<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuizTrackingTables extends Migration
{
    public function up()
    {
        // psy_quiz_attempts table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'started_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'ended_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'total_time_taken' => [
                'type' => 'TIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('psy_quiz_attempts');

        // psy_quiz_user_timer table
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
            'elapsed_time' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'last_updated' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'test_id'], false, true); // Unique composite key
        $this->forge->createTable('psy_quiz_user_timer');
    }

    public function down()
    {
        $this->forge->dropTable('psy_quiz_user_timer');
        $this->forge->dropTable('psy_quiz_attempts');
    }
}
