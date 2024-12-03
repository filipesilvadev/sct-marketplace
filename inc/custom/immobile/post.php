<?php
function immobile_post()
{
    register_post_type(
        'immobile',
        array(
            'labels' => array(
                'name' => "Imóveis",
                'singular_name' => "Imóvel"
            ),
            'public' => true,
            'supports' => array('title'),
            'menu_icon' => 'dashicons-admin-home',
        )
    );
}
add_action('init', 'immobile_post');

function immobile_fields_meta_box()
{
    add_meta_box(
        'immobile_fields_meta_box',
        'Dados Imóveis',
        'immobile_fields_meta_box_callback',
        'immobile',
        'normal',
        'high'
    );
}

function immobile_fields_meta_box_callback($post)
{
    include_once __DIR__ . "/fields.php";
}
add_action('add_meta_boxes', 'immobile_fields_meta_box');

function save_immobile_field_meta($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields_to_save = array(
        'broker',
        'location',
        'property_type',
        'condominium',
        'financing',
        'bedrooms',
        'committee',
        'committee_socasatop',
        'size',
        'amount',
        'link',
        'details',
        'facade',
        'immobile_gallery'
    );

    foreach ($fields_to_save as $field_name) {
        if (isset($_POST[$field_name])) {
            $value =  $_POST[$field_name];

            if ($field_name == "amount") {
                $value = str_replace('.', '', $value);
            }

            if ($field_name == "facade") {
                $value = strtolower($value);
                $value = ucwords($value);
            }

            update_post_meta(
                $post_id,
                "$field_name",
                $value
            );
        }
    }
}
add_action('save_post_immobile', 'save_immobile_field_meta');

function display_form_immobile()
{
    ob_start();
    require_once(__DIR__ . "/form.php");
    $content = ob_get_clean();
    return $content;
}

add_shortcode('form_immobile', 'display_form_immobile');

function display_immobile_condominium()
{
    $is_condominium = get_post_meta(get_the_ID(), 'condominium', true) == "Sim";
    $text = ($is_condominium) ? "Em Condomínio" : "Não fica em condomínio";
?>
    <div style="display: flex;">
        <svg width="32px" height="32px" viewBox="0,0,256,256" style="margin-right: 1rem;">
            <g fill="#3858e9" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal">
                <g transform="scale(5.12,5.12)">
                    <path d="M41,0h-32c-1.65234,0 -3,1.34766 -3,3v47h38v-47c0,-1.65234 -1.34766,-3 -3,-3zM28,23v-6h9v6zM37,27v6h-9v-6zM28,13v-6h9v6zM13,23v-6h9v6zM22,27v6h-9v-6zM13,13v-6h9v6zM13,37h9v6h-9zM28,37h9v11h-9z"></path>
                </g>
            </g>
        </svg>
        <h3 style="font-family: 'Ubuntu', Sans-serif;font-size: 20px;font-weight: 500;margin: 0;">
            <span><?php echo $text; ?></span>
        </h3>
    </div>
<?php
}
add_shortcode('immobile_condominium', 'display_immobile_condominium');

function display_immobile_financing()
{
    $is_condominium = get_post_meta(get_the_ID(), 'financing', true) == "Sim";
    $text = ($is_condominium) ? "Aceita Financiamento" : "Não aceira Financiamento";
?>
    <div style="display: flex;">
        <svg width="32px" height="32px" viewBox="0,0,256,256" style="margin-right: 1rem;">
            <g fill="#3756e4" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal">
                <g transform="scale(5.12,5.12)">
                    <path d="M50,27.24219c0,1.82031 -5.33594,4.36328 -14.02344,4.36328c-2.12109,0 -4.14844,-0.16016 -6.03906,-0.46875c0.03125,-0.17187 0.06641,-0.33984 0.06641,-0.51562v-4.96875c1.89063,0.28906 3.89063,0.44141 5.97266,0.44141c5.4375,0 11.10938,-1.09766 14.02344,-3.17187zM28.00391,30.62109c0,1.8125 -5.31641,4.34766 -13.97266,4.34766c-8.65625,0 -13.96875,-2.53125 -13.96875,-4.34766v-4.29687c2.90625,2.06641 8.55078,3.15625 13.96875,3.15625c5.41797,0 11.0625,-1.08984 13.97266,-3.15625zM0.0625,33.80859c2.91016,2.06641 8.55469,3.16016 13.96875,3.16016c5.41797,0 11.0625,-1.09375 13.97266,-3.16016v4.29688c0,1.81641 -5.31641,4.34766 -13.97266,4.34766c-8.65234,0 -13.96875,-2.53125 -13.96875,-4.34766zM50,12.21875c0,1.82031 -5.33594,4.36328 -14.02344,4.36328c-8.6875,0 -14.02344,-2.54297 -14.02344,-4.36328v-4.32031c2.91406,2.07422 8.58203,3.17188 14.02344,3.17188c5.4375,0 11.10938,-1.09766 14.02344,-3.17188zM35.97656,0.33984c8.6875,0 14.02344,2.54297 14.02344,4.36328c0,1.82422 -5.33594,4.36719 -14.02344,4.36719c-8.6875,0 -14.02344,-2.54297 -14.02344,-4.36719c0,-1.82031 5.33203,-4.36328 14.02344,-4.36328zM21.95313,15.40625c2.91406,2.07813 8.58203,3.17578 14.02344,3.17578c5.4375,0 11.10938,-1.09766 14.02344,-3.17578v4.32422c0,1.82031 -5.33594,4.36328 -14.02344,4.36328c-2.08203,0 -4.14844,-0.16797 -6.03516,-0.47266c0.02734,-0.16016 0.0625,-0.31641 0.0625,-0.48437c0,-2.67969 -3.48828,-4.55078 -8.05078,-5.53906zM14.03125,18.78906c8.65625,0 13.97266,2.53125 13.97266,4.34766c0,1.8125 -5.31641,4.34375 -13.97266,4.34375c-8.65625,0 -13.96875,-2.53125 -13.96875,-4.34375c0,-1.81641 5.31641,-4.34766 13.96875,-4.34766zM14.03125,49.9375c-8.65234,0 -13.96875,-2.53125 -13.96875,-4.34766v-4.29687c2.90625,2.06641 8.55078,3.16016 13.96875,3.16016c5.41797,0 11.0625,-1.09375 13.97266,-3.16016v4.29688c0,1.81641 -5.31641,4.34766 -13.97266,4.34766zM35.97656,39.11719c-2.10937,0 -4.13672,-0.16016 -6.04297,-0.46875c0.03516,-0.17969 0.07031,-0.35547 0.07031,-0.54297v-4.9375c1.88281,0.28516 3.88672,0.4375 5.97266,0.4375c5.4375,0 11.10938,-1.09766 14.02344,-3.17187v4.32031c0,1.82031 -5.33594,4.36328 -14.02344,4.36328z"></path>
                </g>
            </g>
        </svg>
        <h3 style="font-family: 'Ubuntu', Sans-serif;font-size: 20px;font-weight: 500;margin: 0;">
            <span style=""><?php echo $text; ?></span>
        </h3>
    </div>
<?php
}
add_shortcode('immobile_financing', 'display_immobile_financing');

function display_edit_form_immobile()
{
    ob_start();
    require_once(__DIR__ . "/edit-form.php");
    $content = ob_get_clean();
    return $content;
}
add_shortcode('edit_form_immobile', 'display_edit_form_immobile');

function display_link_edit_immobile()
{
    return home_url('/editar-imovel/') . "?post=" . get_the_ID();
}
add_shortcode('link_edit_immobile', 'display_link_edit_immobile');

function display_class_link()
{
    return (empty(get_post_meta(get_the_ID(), 'link', true))) ? 'd-none' : '';
}
add_shortcode('class_link', 'display_class_link');


function display_btn_class()
{
    return (!is_user_logged_in()) ? 'd-none' : '';
}
add_shortcode('btn_class', 'display_btn_class');
