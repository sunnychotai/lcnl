<?php

namespace App\Models;

use CodeIgniter\Model;

class SurveyResponseModel extends Model
{
    protected $table          = 'survey_responses';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'survey', 'respondent_name', 'email', 'quote_preference', 'answers', 'ip_address',
    ];

    /**
     * Every response to one survey, oldest first, with `answers` decoded.
     *
     * @return list<array<string, mixed>>
     */
    public function forSurvey(string $survey): array
    {
        $rows = $this->where('survey', $survey)->orderBy('created_at', 'ASC')->orderBy('id', 'ASC')->findAll();

        foreach ($rows as &$row) {
            $decoded       = json_decode((string) $row['answers'], true);
            $row['answers'] = is_array($decoded) ? $decoded : [];
        }
        unset($row);

        return $rows;
    }
}
