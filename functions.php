<?php

function child_enqueue__parent_scripts()
{
    wp_enqueue_style('parent', get_template_directory_uri() . '/style.css');
}
add_action('wp_enqueue_scripts', 'child_enqueue__parent_scripts');

function scripts_so_casa_top()
{
    wp_register_style('so-casa-top', get_stylesheet_directory_uri() . '/assets/socasatop.css', array(), '1.0.0', 'all');
    wp_enqueue_style('so-casa-top');

    wp_register_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), '2.4.1', 'all');
    wp_enqueue_style('select2');

    wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'), '2.4.1', true);
    wp_enqueue_script('so-casa-top', get_stylesheet_directory_uri() . '/assets/socasatop.js', array('jquery'), '1.0.0', true);

    $options = [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ajax_nonce')
    ];

    wp_localize_script('so-casa-top', 'site', $options);
}
add_action('wp_enqueue_scripts', 'scripts_so_casa_top', 5);

function enqueue_sweetalert2_script()
{
    wp_enqueue_script('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', array(), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_sweetalert2_script');

function media_load_scripts()
{
    wp_enqueue_media();
}
add_action('wp_enqueue_scripts', 'media_load_scripts');

include_once "inc/custom/immobile/post.php";
include_once "inc/custom/lead/post.php";
include_once "inc/custom/broker/post.php";
include_once "inc/custom/location/taxonomy.php";
include_once "inc/custom/view-immobile/post.php";

include_once "inc/filter/settings.php";
include_once "inc/ajax.php";

function display_amount()
{
    $amount = floatval(get_post_meta(get_the_ID(), 'amount', true));
    return "R$" . number_format($amount, 0, ',', '.');
}
add_shortcode('field_amount', 'display_amount');

function display_page_link($atts)
{
    $atts = shortcode_atts(array(
        'page' => '',
    ), $atts);
    extract($atts);
    $post_ID = get_the_ID();

    switch ($page) {
        case 'lead':
            $link = home_url("/editar-lead/?post=$post_ID");
            break;
        default:
            $link = "";
            break;
    }

    return $link;
}
add_shortcode('page_link', 'display_page_link');

function display_post_title()
{
    $name = '';
    if (isset($_GET['post'])) {
        $name = get_the_title($_GET['post']);
    }
    return $name;
}
add_shortcode('field_title', 'display_post_title');

function send_message_broker()
{
?>
    <script>
        jQuery(document).ready(function($) {
            let post_id = <?php echo get_the_ID(); ?>;
            $.ajax({
                url: site.ajax_url,
                method: 'POST',
                data: {
                    action: 'send_message_broker',
                    post_id: post_id,
                },
                success: function(response) {
                    contacts = response.data.json;
                    $($('.e-con-inner')[0]).append(`<pre>${contacts}</pre>`);
                   /*  if (response.data.json) {
                        $.ajax({
                            url: 'https://zion.digitalestudio.com.br/webhook/8d3a837a-dadc-40e0-aa96-a95a039fdc66',
                            method: 'POST',
                            data: {
                                contacts: response.data.json
                            }
                        });
                    } */
                },
            });
        });
    </script>
<?php
    return "";
}
add_shortcode('send_message_broker', 'send_message_broker');

function display_must_show_meta($atts)
{
    $roles_user = wp_get_current_user()->roles;
    $attributes = shortcode_atts(array(
        'key' => ''
    ), $atts);

    if (!in_array('administrator', $roles_user)) {
        $meta = "";
    } else {
        $meta = get_post_meta(get_the_ID(), $attributes['key'], true);
    }

    return $meta;
}
add_shortcode('must_show_meta', 'display_must_show_meta');

function display_hidden_user_not_admin()
{
    $roles_user = wp_get_current_user()->roles;

    if (!in_array('administrator', $roles_user)) {
        $class = "d-none";
    }
    return $class ?? "";
}
add_shortcode('hidden_user_not_admin', 'display_hidden_user_not_admin');

function redirect_login()
{
    $loginID = get_page_by_path('login')->ID;
    $login_url = get_permalink($loginID);

    wp_redirect($login_url);
    exit;
}

function redirect_if_not_logged_in()
{
    $current_page = get_queried_object();
    $loginID = get_page_by_path('login')->ID;
    $page_imovel_ID = 1481;
    $page_register_ID = 332;

    if ($current_page->post_type != 'listaimoveis' && $current_page->ID != $page_imovel_ID) {
        if (!is_user_logged_in() && $current_page->ID != $loginID) {
            redirect_login();
        }

        $roles_user = wp_get_current_user()->roles;

        if (!in_array('administrator', $roles_user) && $current_page->post_type != 'lead' && $current_page->ID != $loginID && $current_page->ID != $page_register_ID) {
            redirect_login();
        }
    }
}
add_action('template_redirect', 'redirect_if_not_logged_in');
