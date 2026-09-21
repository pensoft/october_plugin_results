<?php namespace Pensoft\Results\Updates;

use Pensoft\Results\Models\Result;

require_once __DIR__ . '/seed_results_data.php';

/**
 * Applies the per-result vertical offsets to installs whose results were seeded
 * before the offset_y column existed.
 *
 * Only rows still sitting on the column default are touched, so an offset that
 * has already been adjusted in the backend is left alone.
 */
class BackfillResultOffsets extends SeedResultsData
{
    const COLUMN_DEFAULT = 86;

    public function run()
    {
        foreach ($this->results() as $data) {
            $result = Result::where('slug', $data['slug'])->first();

            if (!$result || (int) $result->offset_y !== self::COLUMN_DEFAULT) {
                continue;
            }

            $result->offset_y = $data['offset_y'];
            $result->save();
        }
    }
}
