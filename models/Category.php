<?php namespace Pensoft\Results\Models;

use Model;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Sluggable;

/**
 * Category Model - the audience a result is relevant for
 * (Researchers, Climate modellers, Earth observation community, ...)
 */
class Category extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use Sortable;
    use Sluggable;

    /**
     * @var string table associated with the model
     */
    public $table = 'pensoft_results_categories';

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
    protected $slugs = ['slug' => 'name'];

    /**
     * @var array rules for validation
     */
    public $rules = [
        'name' => 'required',
    ];

    /**
     * @var array Translatable fields
     */
    public $translatable = [
        'name',
    ];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [
        'is_visible' => 'boolean',
    ];

    /**
     * @var array dates attributes that should be mutated to dates
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public $belongsToMany = [
        'results' => [
            'Pensoft\Results\Models\Result',
            'table' => 'pensoft_results_result_category',
            'key' => 'category_id',
            'otherKey' => 'result_id',
            'order' => 'pensoft_results_results.sort_order'
        ],
    ];

    /**
     * Scope for the audience toggles shown on the front end.
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
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
