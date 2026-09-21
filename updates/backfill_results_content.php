<?php namespace Pensoft\Results\Updates;

use Pensoft\Results\Models\Result;

require_once __DIR__ . '/seed_results_data.php';

/**
 * SeedResultsData skips results that already exist, so installs that ran it
 * before the editorial copy was available are left with empty pop-ups. This
 * fills those in from the same source.
 *
 * Only blank fields are written, so anything already edited in the backend is
 * left untouched.
 */
class BackfillResultsContent extends SeedResultsData
{
    public function run()
    {
        foreach ($this->results() as $data) {
            $result = Result::where('slug', $data['slug'])->first();

            if (!$result) {
                continue;
            }

            $values = [
                'description' => $data['description'],
                'relevant_for' => $data['relevant_for'],
                'how_used' => $data['how_used'],
                'materials' => $this->materials($data['materials']),
            ];

            $changed = false;

            foreach ($values as $field => $value) {
                if ($value !== '' && !$this->hasContent($result->{$field})) {
                    $result->{$field} = $value;
                    $changed = true;
                }
            }

            if ($changed) {
                $result->save();
            }
        }
    }

    /**
     * An empty richeditor field can still hold markup such as <p></p>.
     */
    protected function hasContent($value)
    {
        return trim(strip_tags((string) $value)) !== '';
    }
}
