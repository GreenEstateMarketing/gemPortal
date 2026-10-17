<?php

namespace Botble\RealEstate\Supports;

/**
 * Registry of admin-togglable feature flags, each backed by a row in the
 * core settings table (key "feature_flag_<key>") rather than a dedicated
 * migration/model. Adding a new flag later is just adding one entry to
 * all() - the Feature Flags admin screen and this helper both read from it.
 */
class FeatureFlag
{
    const PROPERTY_WIZARD_EDIT_AFTER_APPROVAL = 'property_wizard_edit_after_approval';

    public static function all(): array
    {
        return [
            self::PROPERTY_WIZARD_EDIT_AFTER_APPROVAL => [
                'name' => 'Allow agents & admins to edit property details after approval',
                'description' => 'When enabled, agents and admins can keep editing an approved property listing. Members remain locked out regardless of this setting.',
            ],
        ];
    }

    public static function enabled(string $key): bool
    {
        return (bool) setting('feature_flag_' . $key, false);
    }
}
