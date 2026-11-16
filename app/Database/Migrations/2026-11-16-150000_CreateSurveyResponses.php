<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Answers to the site's questionnaires (first: the committee ticketing
 * survey). The questions live in a config class and the answers are kept as
 * JSON, so the questions can change without a migration.
 */
class CreateSurveyResponses extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            // Which questionnaire, e.g. ticketing-2026.
            'survey' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'respondent_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            // named | anonymous: how the respondent's comments may be quoted.
            'quote_preference' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            // {"question_id": "value" | ["values"] | {"row": "column"}, ...}
            'answers' => [
                'type' => 'MEDIUMTEXT',
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('survey');
        $this->forge->createTable('survey_responses');
    }

    public function down()
    {
        $this->forge->dropTable('survey_responses', true);
    }
}
