<?php

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Career\Repositories\Interfaces\CareerInterface;
use Botble\Location\Models\City;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\ProjectStatusEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\Theme\Events\RenderingSiteMapEvent;
use Theme\FlexHome\Http\Requests\CityRequest;

app()->booted(function () {
    if (is_plugin_active('location')) {
        app('migrator')->path(__DIR__ . '/../database/migrations');
    }
});

register_page_template([
    'homepage' => __('Homepage'),
]);

register_sidebar([
    'id'          => 'footer_sidebar',
    'name'        => __('Footer sidebar'),
    'description' => __('Footer sidebar for Flex Home theme'),
]);

if (is_plugin_active('real-estate')) {
    add_shortcode('featured-projects', __('Featured projects'), __('Featured projects'), function () {
        return Theme::partial('short-codes.featured-projects');
    });

    add_shortcode('projects-by-locations', __('Projects by locations'), __('Projects by locations'), function () {
        return Theme::partial('short-codes.projects-by-locations');
    });

    add_shortcode('properties-by-locations', __('Properties by locations'), __('Properties by locations'), function () {
        return Theme::partial('short-codes.properties-by-locations');
    });

    add_shortcode('properties-for-sale', __('Properties for sale'), __('Properties for sale'), function () {
        return Theme::partial('short-codes.properties-for-sale');
    });

    add_shortcode('properties-for-rent', __('Properties for rent'), __('Properties for rent'), function () {
        return Theme::partial('short-codes.properties-for-rent');
    });
}

if (is_plugin_active('blog')) {
    add_shortcode('latest-news', __('Latest news'), __('Latest news'), function () {
        return Theme::partial('short-codes.latest-news');
    });
}

theme_option()
    ->setField([
        'id'         => 'copyright',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'text',
        'label'      => __('Copyright'),
        'attributes' => [
            'name'    => 'copyright',
            'value'   => '© 2020 Botble Technologies. All right reserved.',
            'options' => [
                'class'        => 'form-control',
                'placeholder'  => __('Change copyright'),
                'data-counter' => 250,
            ],
        ],
        'helper'     => __('Copyright on footer of site'),
    ])
    ->setField([
        'id'         => 'primary_font',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'googleFonts',
        'label'      => __('Primary font'),
        'attributes' => [
            'name'  => 'primary_font',
            'value' => 'Nunito Sans',
        ],
    ])
    ->setField([
        'id'         => 'primary_color',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'customColor',
        'label'      => __('Primary color'),
        'attributes' => [
            'name'  => 'primary_color',
            'value' => '#1d5f6f',
        ],
    ])
    ->setField([
        'id'         => 'primary_color_hover',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'customColor',
        'label'      => __('Hover primary color'),
        'attributes' => [
            'name'  => 'primary_color_hover',
            'value' => '#063a5d',
        ],
    ])
    ->setField([
        'id'         => 'about-us',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'textarea',
        'label'      => __('About us'),
        'attributes' => [
            'name'    => 'about-us',
            'value'   => null,
            'options' => [
                'class' => 'form-control',
            ],
        ],
    ])
    ->setField([
        'id'         => 'hotline',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'text',
        'label'      => __('Hotline'),
        'attributes' => [
            'name'    => 'hotline',
            'value'   => null,
            'options' => [
                'class'        => 'form-control',
                'placeholder'  => 'Hotline',
                'data-counter' => 30,
            ],
        ],
    ])
    ->setField([
        'id'         => 'address',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'text',
        'label'      => __('Address'),
        'attributes' => [
            'name'    => 'address',
            'value'   => null,
            'options' => [
                'class'        => 'form-control',
                'placeholder'  => 'Address',
                'data-counter' => 120,
            ],
        ],
    ])
    ->setField([
        'id'         => 'email',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'email',
        'label'      => __('Email'),
        'attributes' => [
            'name'    => 'email',
            'value'   => null,
            'options' => [
                'class'        => 'form-control',
                'placeholder'  => 'Email',
                'data-counter' => 120,
            ],
        ],
    ])
    ->setField([
        'id'         => 'enable_sticky_header',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'select',
        'label'      => __('Enable sticky header?'),
        'attributes' => [
            'name'    => 'enable_sticky_header',
            'list'    => [
                'yes' => trans('core/base::base.yes'),
                'no'  => trans('core/base::base.no'),
            ],
            'value'   => 'yes',
            'options' => [
                'class' => 'form-control',
            ],
        ],
    ])
    ->setSection([
        'title'      => __('Social'),
        'desc'       => __('Social links'),
        'id'         => 'opt-text-subsection-social',
        'subsection' => true,
        'icon'       => 'fa fa-share-alt',
    ])
    ->setField([
        'id'         => 'facebook',
        'section_id' => 'opt-text-subsection-social',
        'type'       => 'text',
        'label'      => 'Facebook',
        'attributes' => [
            'name'    => 'facebook',
            'value'   => null,
            'options' => [
                'class' => 'form-control',
            ],
        ],
    ])
    ->setField([
        'id'         => 'twitter',
        'section_id' => 'opt-text-subsection-social',
        'type'       => 'text',
        'label'      => 'Twitter',
        'attributes' => [
            'name'    => 'twitter',
            'value'   => null,
            'options' => [
                'class' => 'form-control',
            ],
        ],
    ])
    ->setField([
        'id'         => 'youtube',
        'section_id' => 'opt-text-subsection-social',
        'type'       => 'text',
        'label'      => 'Youtube',
        'attributes' => [
            'name'    => 'youtube',
            'value'   => null,
            'options' => [
                'class' => 'form-control',
            ],
        ],
    ])
    ->setSection([
        'title'      => __('Content'),
        'desc'       => __('Theme options for content'),
        'id'         => 'opt-text-subsection-homepage',
        'subsection' => true,
        'icon'       => 'fa fa-edit',
        'fields'     => [
            [
                'id'         => 'number_of_featured_projects',
                'type'       => 'number',
                'label'      => __('Number of featured projects on homepage'),
                'attributes' => [
                    'name'    => 'number_of_featured_projects',
                    'value'   => 4,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'id'         => 'number_of_featured_cities',
                'type'       => 'number',
                'label'      => __('Number of featured cities on homepage'),
                'attributes' => [
                    'name'    => 'number_of_featured_cities',
                    'value'   => 10,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'id'         => 'number_of_properties_for_sale',
                'type'       => 'number',
                'label'      => __('Number of properties for sale on homepage'),
                'attributes' => [
                    'name'    => 'number_of_properties_for_sale',
                    'value'   => 8,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'id'         => 'number_of_properties_for_rent',
                'type'       => 'number',
                'label'      => __('Number of properties for rent on homepage'),
                'attributes' => [
                    'name'    => 'number_of_properties_for_rent',
                    'value'   => 8,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'id'         => 'number_of_projects_per_page',
                'type'       => 'number',
                'label'      => __('Number of projects per page'),
                'attributes' => [
                    'name'    => 'number_of_projects_per_page',
                    'value'   => 12,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'id'         => 'number_of_properties_per_page',
                'type'       => 'number',
                'label'      => __('Number of properties per page'),
                'attributes' => [
                    'name'    => 'number_of_properties_per_page',
                    'value'   => 12,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'id'         => 'number_of_related_projects',
                'type'       => 'number',
                'label'      => __('Number of related projects'),
                'attributes' => [
                    'name'    => 'number_of_related_projects',
                    'value'   => 8,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'id'         => 'number_of_related_properties',
                'type'       => 'number',
                'label'      => __('Number of related properties'),
                'attributes' => [
                    'name'    => 'number_of_related_properties',
                    'value'   => 8,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'id'         => 'home_banner_description',
                'type'       => 'text',
                'label'      => __('The description for banner search block'),
                'attributes' => [
                    'name'    => 'home_banner_description',
                    'value'   => null,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'id'         => 'home_banner',
                'type'       => 'mediaImage',
                'label'      => __('Top banner homepage'),
                'attributes' => [
                    'name'  => 'home_banner',
                    'value' => null,
                ],
            ],
            [
                'id'         => 'home_project_description',
                'type'       => 'textarea',
                'label'      => __('The description for projects block'),
                'attributes' => [
                    'name'    => 'home_project_description',
                    'value'   => null,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'id'         => 'properties_description',
                'type'       => 'textarea',
                'label'      => __('The description for properties block'),
                'attributes' => [
                    'name'    => 'properties_description',
                    'value'   => null,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'id'         => 'home_description_for_projects_by_locations',
                'type'       => 'textarea',
                'label'      => __('The description for projects by locations block'),
                'attributes' => [
                    'name'    => 'home_description_for_projects_by_locations',
                    'value'   => null,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'id'         => 'home_description_for_properties_by_locations',
                'type'       => 'textarea',
                'label'      => __('The description for properties by locations block'),
                'attributes' => [
                    'name'    => 'home_description_for_properties_by_locations',
                    'value'   => null,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'id'         => 'home_description_for_properties_for_sale',
                'type'       => 'textarea',
                'label'      => __('The description for properties for sale block'),
                'attributes' => [
                    'name'    => 'home_description_for_properties_for_sale',
                    'value'   => null,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'id'         => 'home_description_for_properties_for_rent',
                'type'       => 'textarea',
                'label'      => __('The description for properties for rent block'),
                'attributes' => [
                    'name'    => 'home_description_for_properties_for_rent',
                    'value'   => null,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'id'         => 'home_description_for_news',
                'type'       => 'textarea',
                'label'      => __('The description for news block'),
                'attributes' => [
                    'name'    => 'home_description_for_news',
                    'value'   => null,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
        ],
    ]);

Event::listen(RenderingSiteMapEvent::class, function () {

    if (is_plugin_active('real-estate')) {
        $projects = app(ProjectInterface::class)->advancedGet([
            'condition' => [
                ['re_projects.status', 'NOT_IN', [ProjectStatusEnum::NOT_AVAILABLE]],
            ],
            'with'      => ['slugable'],
        ]);

        SiteMapManager::add(route('public.projects'), '2019-12-09 00:00:00', '0.4', 'monthly');

        foreach ($projects as $project) {
            SiteMapManager::add($project->url, $project->updated_at, '0.8');
        }

        $properties = app(PropertyInterface::class)->advancedGet([
            'condition' => [
                ['re_properties.status', 'NOT_IN', [PropertyStatusEnum::NOT_AVAILABLE]],
                're_properties.moderation_status' => ModerationStatusEnum::APPROVED,
            ],
            'with'      => ['slugable'],
        ]);

        SiteMapManager::add(route('public.properties'), '2010-11-25 00:00:00', '0.4', 'monthly');

        foreach ($properties as $property) {
            SiteMapManager::add($property->url, $property->updated_at, '0.8');
        }
    }

    if (is_plugin_active('career')) {
        $careers = app(CareerInterface::class)->allBy(['status' => BaseStatusEnum::PUBLISHED]);

        SiteMapManager::add(route('public.careers'), '2010-11-25 00:00:00', '0.4', 'monthly');

        foreach ($careers as $career) {
            SiteMapManager::add($career->url, $career->updated_at, '0.6');
        }
    }

});

add_action('init', function () {
    config([
        'filesystems.disks.public.root' => public_path('storage'),
        'filesystems.disks.public.url'  => str_replace('/index.php', '', url('storage')),
    ]);
}, 124);

RvMedia::addSize('small', 410, 270);

add_filter(BASE_FILTER_BEFORE_RENDER_FORM, 'add_addition_fields_into_form', 127, 2);

/**
 * @param \Botble\Base\Forms\FormAbstract $form
 * @param $data
 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
 */
function add_addition_fields_into_form($form, $data)
{
    if (get_class($data) == City::class) {
        $form
            ->setValidatorClass(CityRequest::class)
            ->addAfter('name', 'slug', 'text', [
                'label'      => __('Slug'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => __('Slug'),
                    'data-counter' => 120,
                ],
            ])
            ->addAfter('country_id', 'is_featured', 'onOff', [
                'label'         => trans('core/base::forms.is_featured'),
                'label_attr'    => ['class' => 'control-label'],
                'default_value' => false,
            ])
            ->addAfter('status', 'image', 'mediaImage', [
                'label'      => trans('core/base::forms.image'),
                'label_attr' => ['class' => 'control-label'],
            ]);
    }
}

add_action(BASE_ACTION_AFTER_CREATE_CONTENT, 'save_addition_city_fields', 230, 3);
add_action(BASE_ACTION_AFTER_UPDATE_CONTENT, 'save_addition_city_fields', 230, 3);

/**
 * @param string $type
 * @param Request $request
 * @param City $object
 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
 */
function save_addition_city_fields($type, $request, $object)
{
    if (is_plugin_active('location') && in_array($type, [CITY_MODULE_SCREEN_NAME])) {
        $object->slug = $request->input('slug');
        $object->is_featured = $request->input('is_featured');
        $object->image = $request->input('image');
        $object->save();
    }
}

if (is_plugin_active('contact')) {
    add_filter(CONTACT_FORM_TEMPLATE_VIEW, function () {
        return Theme::getThemeNamespace() . '::partials.short-codes.contact-form';
    }, 120);
}

add_shortcode('google-map', __('Google map'), __('Custom map'), function ($shortCode) {
    return Theme::partial('short-codes.google-map', ['address' => $shortCode->content]);
});

add_shortcode('youtube-video', __('Youtube video'), __('Add youtube video'), function ($shortCode) {
    return Theme::partial('short-codes.youtube', ['url' => $shortCode->content]);
});

shortcode()->setAdminConfig('google-map', Theme::partial('short-codes.google-map-admin-config'));
shortcode()->setAdminConfig('youtube-video', Theme::partial('short-codes.youtube-admin-config'));

function hex_to_rgba(string $color, $opacity = 1) {
    list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
    return 'rgba(' . $r . ',' . $g . ',' . ($b === null ? 0 : $b) . ', ' . $opacity . ')';
}
