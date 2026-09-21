<?php namespace Pensoft\Results\Updates;

use Cms\Classes\Theme;
use October\Rain\Database\Updates\Seeder;
use Pensoft\Results\Models\Result;
use System\Models\File;

/**
 * Attaches the timeline icons drawn for the design.
 *
 * The artwork ships with the theme, so it is read from the active theme rather
 * than from a hard-coded path, and copied into the plugin's own file storage.
 * Results that already carry an icon are left alone, so this will not undo an
 * icon replaced from the backend.
 */
class SeedResultIcons extends Seeder
{
    /**
     * @var string source directory, relative to the active theme
     */
    const SOURCE_DIR = '/assets/images/timeline/';

    /**
     * result slug => file name. Every name matches its result title except the
     * assimilation icon, which came out of the icon set under its own name.
     */
    protected $icons = [
        'land-use-change-dataset' => 'Land-use Change Dataset.svg',
        'concerto-data-sharing-platform' => 'CONCERTO Data Sharing Platform.svg',
        'top-down-isoprene-flux-datasets' => 'Top-down Isoprene Flux Datasets.svg',
        'global-land-cover-and-leaf-area-index-datasets' => 'Global land-cover and Leaf Area Index Datasets.svg',
        'vegetation-observation-operators' => 'Vegetation Observation Operators.svg',
        'p-model-implementation-and-user-guidance' => 'P-model Implementation and User Guidance.svg',
        'multivariate-vegetation-data-assimilation' => 'material-symbols_arrows-input.svg',
        'seasonal-forecast-demonstrators' => 'Seasonal Forecast Demonstrators.svg',
        'improved-land-carbon-cycle-models' => 'Improved Land Carbon-cycle Models.svg',
        'integrated-climate-model-demonstration' => 'Integrated Climate-model Demonstration.svg',
    ];

    public function run()
    {
        $theme = Theme::getActiveTheme();

        if (!$theme) {
            return;
        }

        $directory = $theme->getPath() . self::SOURCE_DIR;

        foreach ($this->icons as $slug => $fileName) {
            $result = Result::where('slug', $slug)->first();

            if (!$result || $result->icon) {
                continue;
            }

            $path = $directory . $fileName;

            if (!file_exists($path)) {
                continue;
            }

            $file = new File;
            $file->fromFile($path);
            $file->is_public = true;

            $result->icon()->add($file);
        }
    }
}
