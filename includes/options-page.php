<?php

use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('after_setup_theme', 'load_carbon_fields');
add_action('carbon_fields_register_fields', 'create_options_page');

/**
 * We boot Carbon_Fields after the `after_setup_theme` WordPress hook
 */
function load_carbon_fields()
{
  Carbon_Fields::boot();
}


function create_options_page()
{
  Container::make('theme_options', __('PW Core', 'pwcore'))
    ->add_fields([
      Field::make('checkbox', 'pwcore_is_active', __('Is Active', 'pwcore'))
        ->set_option_value('yes')
    ]);
}
