<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SurveyResponseModel;
use App\Services\SurveyService;
use Config\TicketingSurvey;

/**
 * Results of the committee ticketing questionnaire: totals per question, the
 * feature list ranked by priority, every comment, each response in full,
 * and a CSV export. ADMIN and WEBADMIN only, because answers carry names.
 */
class TicketingSurveyController extends BaseController
{
    private const BASE = 'admin/surveys/ticketing';

    protected TicketingSurvey $config;
    protected SurveyResponseModel $responses;
    protected SurveyService $service;

    public function __construct()
    {
        $this->config    = config('TicketingSurvey');
        $this->responses = new SurveyResponseModel();
        $this->service   = new SurveyService();
    }

    public function index()
    {
        $questions = $this->config->questions();
        $rows      = $this->responses->forSurvey($this->config->key);

        // Respondent details are shown with each response, not as charts.
        $summary = $this->service->summarise(
            array_diff_key($questions, array_flip(['name', 'email'])),
            $rows
        );

        return view('admin/survey/ticketing', [
            'title'     => 'Ticketing questionnaire results',
            'config'    => $this->config,
            'sections'  => $this->config->sections(),
            'questions' => $questions,
            'summary'   => $summary,
            'responses' => array_reverse($rows),
            'publicUrl' => site_url($this->config->slug),
            'base'      => self::BASE,
        ]);
    }

    /** Remove a response, e.g. a test entry. Soft-deleted, so it can be recovered from the database. */
    public function delete(int $id)
    {
        $row = $this->responses->where('survey', $this->config->key)->find($id);

        if (! $row) {
            return redirect()->to(self::BASE)->with('error', 'That response was not found.');
        }

        $this->responses->delete($id);

        log_message('info', 'Ticketing survey: response {id} ({name}) removed by {admin}', [
            'id'    => $id,
            'name'  => $row['respondent_name'],
            'admin' => (string) session('admin_name'),
        ]);

        return redirect()->to(self::BASE)->with('success', 'Removed the response from ' . $row['respondent_name'] . '.');
    }

    /** One row per response, one column per question (a grid gets a column per row). */
    public function export()
    {
        $questions = $this->config->questions();
        $rows      = $this->responses->forSurvey($this->config->key);

        $header = ['Response', 'Submitted', 'Name', 'Email', 'May quote by name'];
        $cols   = [];

        foreach ($questions as $id => $q) {
            if (in_array($id, ['name', 'email', 'quote'], true)) {
                continue;
            }

            if ($q['type'] === 'grid') {
                foreach ($q['rows'] as $rowId => $label) {
                    $header[] = $label;
                    $cols[]   = static fn(array $a): string => (string) ($q['columns'][$a[$id][$rowId] ?? '']['label'] ?? '');
                }

                continue;
            }

            $header[] = $q['label'];
            $cols[]   = static function (array $a) use ($id, $q): string {
                $v = $a[$id] ?? '';

                return match ($q['type']) {
                    'single', 'scale' => (string) ($q['options'][$v] ?? $v),
                    'multi'           => implode('; ', array_map(static fn($k) => $q['options'][$k] ?? $k, (array) $v)),
                    default           => (string) $v,
                };
            };

            if (! empty($q['other'])) {
                $header[] = $q['label'] . ' (other)';
                $cols[]   = static fn(array $a): string => (string) ($a[$id . '_other'] ?? '');
            }
        }

        $out = fopen('php://temp', 'w+');
        fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel
        fputcsv($out, $header);

        foreach ($rows as $r) {
            $line = [
                $r['id'], $r['created_at'], $r['respondent_name'], $r['email'],
                $r['quote_preference'] === TicketingSurvey::QUOTE_NAMED ? 'Yes' : 'No',
            ];
            foreach ($cols as $col) {
                $line[] = $col($r['answers']);
            }
            // Neutralise spreadsheet formulas in respondent-supplied text.
            fputcsv($out, array_map(static fn($v) => is_string($v) && preg_match('/^[=+\-@]/', $v) ? "'" . $v : $v, $line));
        }

        rewind($out);
        $body = stream_get_contents($out);
        fclose($out);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="ticketing_survey_' . date('Ymd') . '.csv"')
            ->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->setBody($body);
    }
}
