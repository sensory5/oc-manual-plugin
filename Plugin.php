<?php namespace Sensory5\Manual;

use Backend;
use System\Classes\PluginBase;

/**
 * Manual Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'sensory5.manual::lang.plugin.name',
            'description' => 'sensory5.manual::lang.plugin.description',
            'author'      => 'Sensory5',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'sensory5.manual.access' => [
                'tab' => 'sensory5.manual::lang.permissions.access.tab',
                'label' => 'sensory5.manual::lang.permissions.access.label',
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [
            'manual' => [
                'label'       => 'sensory5.manual::lang.navigation.label',
                'url'         => Backend::url('sensory5/manual/manual'),
                'icon'        => 'icon-book',
                'permissions' => ['sensory5.manual.*'],
                'order'       => 500,
            ],
        ];
    }

}
