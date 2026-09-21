<?php namespace Pensoft\Results\Models;

use Model;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Sluggable;

/**
 * Result Model - a single achievement node on the homepage timeline
 */
class Result extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use Sortable;
    use Sluggable;

    /**
     * @var string table associated with the model
     */
    public $table = 'pensoft_results_results';

    /**
     * @var array guarded attributes aren't mass assignable
     */
    protected $guarded = ['*'];

    /**
     * @var array fillable attributes are mass assignable
     */
    protected $fillable = [];

    /**
     * @var array Generate slugs for these attributes.
     */
    protected $slugs = ['slug' => 'title'];

    /**
     * @var array rules for validation
     */
    public $rules = [
        'title' => 'required',
        // Not editable in the backend, so they may well be absent on save and
        // fall back to the column defaults. Only checked when a value is given.
        'position_x' => 'nullable|numeric|min:0|max:100',
        'offset_y' => 'nullable|integer|min:0|max:400',
    ];

    /**
     * @var array Translatable fields
     */
    public $translatable = [
        'title',
        'month_label',
        'description',
        'relevant_for',
        'how_used',
        'materials',
    ];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * @var array dates attributes that should be mutated to dates
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public $belongsTo = [
        'phase' => [
            'Pensoft\Results\Models\Phase',
            'key' => 'phase_id'
        ],
    ];

    public $belongsToMany = [
        'categories' => [
            'Pensoft\Results\Models\Category',
            'table' => 'pensoft_results_result_category',
            'key' => 'result_id',
            'otherKey' => 'category_id',
            'order' => 'pensoft_results_categories.sort_order'
        ],
    ];

    public $attachOne = [
        'icon' => ['System\Models\File'],
    ];

    /**
     * Options for the "position" dropdown - which side of the axis the node sits on.
     */
    public function getPositionOptions()
    {
        return [
            'above' => 'Above the timeline',
            'below' => 'Below the timeline',
        ];
    }

    /**
     * Only results that should appear on the front end.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * The category ids this result belongs to, used by the front end filter.
     */
    public function getCategoryIdsAttribute()
    {
        return $this->categories->pluck('id')->all();
    }

    /**
     * True when at least one of the three modal sections has content.
     */
    public function getHasDetailsAttribute()
    {
        return (bool) (trim(strip_tags((string) $this->relevant_for))
            || trim(strip_tags((string) $this->how_used))
            || trim(strip_tags((string) $this->materials)));
    }

    /**
     * Add translation support to this model, if available.
     *
     * @return void
     */
    public static function boot()
    {
        // Call default functionality (required)
        parent::boot();

        // Check the translate plugin is installed
        if (!class_exists('RainLab\Translate\Behaviors\TranslatableModel')) {
            return;
        }

        // Extend the constructor of the model
        self::extend(
            function ($model) {
                // Implement the translatable behavior
                $model->implement[] = 'RainLab.Translate.Behaviors.TranslatableModel';
            }
        );
    }
}
