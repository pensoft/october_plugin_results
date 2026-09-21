<?php namespace Pensoft\Results;

use Pensoft\Results\Components\ResultsTimeline;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'        => 'Results',
            'description' => 'Project results displayed as an interactive timeline.',
            'icon'        => 'oc-icon-sitemap',
            'author'      => 'Pensoft'
        ];
    }

    public function registerComponents()
    {
        return [
            ResultsTimeline::class => 'results_timeline',
        ];
    }

    public function registerPermissions()
    {
        return [
            'pensoft.results.access' => [
                'tab' => 'Results',
                'label' => 'Manage project results'
            ],
        ];
    }

    public function registerNavigation()
    {
        return [
            'results' => [
                'label'       => 'Results',
                'url'         => \Backend::url('pensoft/results/results'),
                'icon'        => 'icon-sitemap',
                'permissions' => ['pensoft.results.*'],
                'sideMenu' => [
                    'side-menu-results' => [
                        'label'       => 'Results',
                        'url'         => \Backend::url('pensoft/results/results'),
                        'icon'        => 'icon-sitemap',
                        'permissions' => ['pensoft.results.*'],
                    ],
                    'side-menu-phases' => [
                        'label'       => 'Timeline phases',
                        'url'         => \Backend::url('pensoft/results/phases'),
                        'icon'        => 'icon-circle-o',
                        'permissions' => ['pensoft.results.*'],
                    ],
                    'side-menu-categories' => [
                        'label'       => 'Audiences',
                        'url'         => \Backend::url('pensoft/results/categories'),
                        'icon'        => 'icon-users',
                        'permissions' => ['pensoft.results.*'],
                    ],
                ]
            ],
        ];
    }
}
