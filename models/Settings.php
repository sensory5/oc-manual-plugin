<?php namespace Sensory5\Manual\Models;

use Model;

/**
 * Settings Model
 */
class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'sensory5_manual_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';
}
