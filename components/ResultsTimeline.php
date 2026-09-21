<?php namespace Pensoft\Results\Components;

use Cms\Classes\ComponentBase;
use Illuminate\Support\Collection;
use Pensoft\Results\Models\Category;
use Pensoft\Results\Models\Phase;
use Pensoft\Results\Models\Result;
use Schema;

/**
 * ResultsTimeline Component
 *
 * Renders the "What CONCERTO Achieved" timeline: a horizontal axis carrying the
 * project phases, with the individual results pinned above and below it. The
 * audience toggles filter the visible results and each result opens a pop-up
 * with its details.
 */
class ResultsTimeline extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'Results Timeline',
            'description' => 'Interactive timeline of the project results, filterable by audience.'
        ];
    }

    public function defineProperties()
    {
        return [
            'title' => [
                'title' => 'Title',
                'description' => 'Heading shown above the timeline. Leave empty to hide it.',
                'default' => 'What CONCERTO Achieved',
                'type' => 'string',
            ],
            'audienceLabel' => [
                'title' => 'Audience label',
                'description' => 'Label in front of the audience toggles.',
                'default' => 'Who is it relevant for?',
                'type' => 'string',
            ],
            'showAllToggle' => [
                'title' => 'Show the "All" toggle',
                'description' => 'Adds a toggle that reveals every result.',
                'default' => true,
                'type' => 'checkbox',
            ],
        ];
    }

    /**
     * @var bool|null whether the plugin tables have been migrated yet
     */
    protected $installed = null;

    /**
     * The tables are missing until the plugin migrations have run. Without this
     * check a fresh deployment would take the whole page down with it.
     */
    protected function isInstalled()
    {
        if ($this->installed === null) {
            $this->installed = Schema::hasTable('pensoft_results_results');
        }

        return $this->installed;
    }

    /**
     * The audiences that get a toggle.
     */
    public function categories()
    {
        if (!$this->isInstalled()) {
            return new Collection;
        }

        return Category::visible()->orderBy('sort_order')->get();
    }

    /**
     * The large circles sitting on the axis.
     */
    public function phases()
    {
        if (!$this->isInstalled()) {
            return new Collection;
        }

        return Phase::orderBy('sort_order')->get();
    }

    /**
     * The published result nodes, with their audiences eager loaded.
     */
    public function results()
    {
        if (!$this->isInstalled()) {
            return new Collection;
        }

        return Result::published()
            ->with(['categories', 'phase'])
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Space separated list of category ids, consumed by the front end filter.
     */
    public function categoryIds($result)
    {
        return implode(' ', $result->categories->pluck('id')->all());
    }
}
