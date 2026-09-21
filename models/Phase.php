<?php namespace Pensoft\Results\Models;

use Model;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Sluggable;

/**
 * Phase Model - the large circles sitting on the timeline axis
 * (Data Foundation, New Earth Observation, Model Integration, Climate Demonstration)
 */
class Phase extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use Sortable;
    use Sluggable;

    /**
     * @var string table associated with the model
     */
    public $table = 'pensoft_results_phases';

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
        'position_x' => 'numeric|min:0|max:100',
    ];

    /**
     * @var array Translatable fields
     */
    public $translatable = [
        'name',
    ];

    /**
     * @var array dates attributes that should be mutated to dates
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public $hasMany = [
        'results' => [
            'Pensoft\Results\Models\Result',
            'key' => 'phase_id',
            'order' => 'sort_order'
        ],
    ];

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
