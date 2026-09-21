<?php namespace Pensoft\Results\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Categories Backend Controller - the audience toggles
 */
class Categories extends Controller
{
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\ReorderController::class,
    ];

    /**
     * @var string formConfig file
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string listConfig file
     */
    public $listConfig = 'config_list.yaml';

    /**
     * @var string reorderConfig file
     */
    public $reorderConfig = 'config_reorder.yaml';

    /**
     * @var array permissions required to view this page
     */
    public $requiredPermissions = ['pensoft.results.access'];

    /**
     * __construct the controller
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Pensoft.Results', 'results', 'side-menu-categories');
    }
}
