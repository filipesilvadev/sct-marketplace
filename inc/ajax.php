<?php
function delete_post_ajax_handler()
{
    if (!current_user_can('administrator')) {
        wp_send_json_error('You do not have permission to delete this location.', 401);
        wp_die();
    }

    check_ajax_referer('ajax_nonce', 'nonce');

    if (isset($_POST['id'])) {
        $post_id = intval($_POST['id']);
    } elseif (isset($_POST['queried_id'])) {
        $post_id = intval($_POST['queried_id']);
    } else {
        $post_id = 0;
    }

    if ($post_id <= 0) {
        wp_send_json_error('ID inválido.', 400);
        wp_die();
    }

    $post_id = (int) isset($request['id']) ? $_POST['id'] : $_POST['queried_id'];

    if (wp_delete_post($post_id, true)) {
        wp_send_json_success('Deletado com sucesso', 200);
    } else {
        wp_send_json_error('Erro ao deletar', 500);
    }
    wp_die();
}

add_action('wp_ajax_delete_post', 'delete_post_ajax_handler');

//# Broker Endpoints
function sanitize_string($string)
{
    $string = strtr(
        $string,
        'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ',
        'AAAAAAACEEEEIIIIDNOOOOOOUUUUYbsaaaaaaaceeeeiiiidnoooooouuuuyby'
    );

    $string = strtolower($string);

    $string = str_replace(' ', '', $string);

    return $string;
}
function create_broker_ajax_handler()
{
    if (!current_user_can('administrator')) {
        wp_send_json_error('You do not have permission this in location.', 401);
        wp_die();
    }

    check_ajax_referer('ajax_nonce', 'nonce');

    $username = sanitize_string($_POST['name']);
    $email = $_POST['email'];

    if (username_exists($username) || email_exists($email)) {
        wp_send_json_error(array('message' => 'Username or email already exists.'));
    } else {
        $user_id = wp_create_user($username, $_POST['password'], $email);

        if (is_wp_error($user_id)) {
            wp_send_json_error('Erro ao cadastrar corretor: ' . $user_id->get_error_message(), 500);
        } else {
            $user_data = array(
                'ID' => $user_id,
                'first_name' => $_POST['name'],
                'role' => 'author'
            );
            $user_id = wp_update_user($user_data);
            update_user_meta($user_id, 'phone', sanitize_text_field($_POST['phone']));
            wp_send_json_success('Corretor', 200);
        }
    }
}
add_action('wp_ajax_create_broker', 'create_broker_ajax_handler');

function update_broker_ajax_handler()
{
    if (!current_user_can('administrator')) {
        wp_send_json_error('You do not have permission this in location.', 401);
        wp_die();
    }

    check_ajax_referer('ajax_nonce', 'nonce');

    $user_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($user_id <= 0) {
        wp_send_json_error('ID do corretor inválido.', 400);
        wp_die();
    }

    $user_data = array(
        'ID' => $user_id,
        'display_name' => $_POST['name'],
        'user_email' => $_POST['email'],
    );

    if (isset($_POST['password']) && !empty($_POST['password'])) {
        $user_data['user_pass'] = $_POST['password'];
    }

    $user_result = wp_update_user($user_data);

    if (is_wp_error($user_result)) {
        wp_send_json_error('Erro ao atualizar o corretor: ' . $user_result->get_error_message(), 500);
        wp_die();
    }
    update_user_meta($user_id, 'phone', sanitize_text_field($_POST['phone']));

    wp_send_json_success('Corretor atualizado com sucesso.', 200);
    wp_die();
}
add_action('wp_ajax_update_broker', 'update_broker_ajax_handler');

//# Immobile Endpoints
function create_immobile_ajax_handler()
{
    if (!current_user_can('administrator') && !current_user_can('author')) {
        wp_send_json_error('You do not have permission in this location.', 401);
        wp_die();
    }

    check_ajax_referer('ajax_nonce', 'nonce');

    $post_id = wp_insert_post(array(
        'post_type' => 'immobile',
        'post_title' => sanitize_text_field($_POST['title']),
        'post_status' => 'publish'
    ));


    if (is_wp_error($post_id)) {
        wp_send_json_error('Erro ao cadastrar imóvel: ' . $post_id->get_error_message(), 500);
    } else {
        wp_send_json_success('Imóvel', 200);
    }

    wp_die();
}
add_action('wp_ajax_create_immobile', 'create_immobile_ajax_handler');

function update_immobile_ajax_handler()
{
    if (!current_user_can('administrator')) {
        wp_send_json_error('You do not have permission in this location.', 401);
        wp_die();
    }

    check_ajax_referer('ajax_nonce', 'nonce');

    $post_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($post_id <= 0) {
        wp_send_json_error('ID do imóvel inválido.', 400);
        wp_die();
    }

    $post_data = array('ID' => $post_id);

    if (isset($_POST['title'])) {
        $post_data['post_title'] = sanitize_text_field($_POST['title']);
    }

    $update_result = wp_update_post($post_data, true);

    if (is_wp_error($update_result)) {
        wp_send_json_error('Erro ao atualizar o imóvel: ' . $update_result->get_error_message(), 500);
        wp_die();
    }

    wp_send_json_success('Imóvel atualizado com sucesso.', 200);
    wp_die();
}
add_action('wp_ajax_update_immobile', 'update_immobile_ajax_handler');

//# Lead Endpoints
function create_lead_ajax_handler()
{
    if (!current_user_can('administrator')) {
        wp_send_json_error('You do not have permission in this location.', 401);
        wp_die();
    }

    check_ajax_referer('ajax_nonce', 'nonce');

    $post_id = wp_insert_post(array(
        'post_type' => 'lead',
        'post_title' => sanitize_text_field($_POST['title']),
        'post_status' => 'publish',
    ));

    if (is_wp_error($post_id)) {
        wp_send_json_error('Erro ao cadastrar lead: ' . $post_id->get_error_message(), 500);
    } else {
        wp_send_json_success('Lead', 200);
    }

    wp_die();
}
add_action('wp_ajax_create_lead', 'create_lead_ajax_handler');

function update_lead_ajax_handler()
{
    if (!current_user_can('administrator')) {
        wp_send_json_error('You do not have permission in this location.', 401);
        wp_die();
    }

    check_ajax_referer('ajax_nonce', 'nonce');

    $post_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($post_id <= 0) {
        wp_send_json_error('ID do imóvel inválido.', 400);
        wp_die();
    }

    $post_data = array('ID' => $post_id);

    if (isset($_POST['title'])) {
        $post_data['post_title'] = sanitize_text_field($_POST['title']);
    }

    $update_result = wp_update_post($post_data, true);

    if (is_wp_error($update_result)) {
        wp_send_json_error('Erro ao atualizar o lead: ' . $update_result->get_error_message(), 500);
        wp_die();
    }

    wp_send_json_success('Lead atualizado com sucesso.', 200);
    wp_die();
}
add_action('wp_ajax_update_lead', 'update_lead_ajax_handler');

//# Location Endpoints
function create_location_ajax_handler()
{
    if (!current_user_can('administrator')) {
        wp_send_json_error('You do not have permission in this location.', 401);
        wp_die();
    }

    check_ajax_referer('ajax_nonce', 'nonce');

    $location_name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';

    $term = wp_insert_term($location_name, 'locations');

    if (is_wp_error($term)) {
        wp_send_json_error('Erro ao cadastrar localização: ' . $term->get_error_message(), 500);
    } else {
        wp_send_json_success('Localização', 200);
    }

    wp_die();
}
add_action('wp_ajax_create_location', 'create_location_ajax_handler');

function delete_location_ajax_handler()
{
    if (!current_user_can('administrator')) {
        wp_send_json_error('You do not have permission in this location.', 401);
        wp_die();
    }

    check_ajax_referer('ajax_nonce', 'nonce');

    $term_id = isset($_POST['queried_id']) ? intval($_POST['queried_id']) : 0;

    if ($term_id <= 0) {
        wp_send_json_error('ID inválido.', 400);
        wp_die();
    }

    $deleted = wp_delete_term($term_id, 'locations');

    if ($deleted instanceof WP_Error) {
        wp_send_json_error('Erro ao deletar a localização: ' . $deleted->get_error_message(), 500);
    } elseif ($deleted === false) {
        wp_send_json_error('Erro ao deletar a localização.', 500);
    } else {
        wp_send_json_success('Localização deletada com sucesso.', 200);
    }

    wp_die();
}
add_action('wp_ajax_delete_location', 'delete_location_ajax_handler');

//# View Endpoints
function create_link_listaimoveis_ajax_handler()
{
    if (!current_user_can('administrator')) {
        wp_send_json_error('You do not have permission in this location.', 401);
        wp_die();
    }

    check_ajax_referer('ajax_nonce', 'nonce');

    $post_id = wp_insert_post(array(
        'post_type' => 'listaimoveis',
        'post_status' => 'publish',
        'post_title' => sanitize_text_field($_POST['name']),
        'meta_input' => array(
            'immobile_ids' => sanitize_text_field($_POST['immobile_ids']),
        ),
    ));

    if (is_wp_error($post_id)) {
        wp_send_json_error('Erro ao gerar link: ' . $post_id->get_error_message(), 500);
    } else {
        $link = get_permalink($post_id);
        wp_send_json_success(array('link' => $link), 200);
    }

    wp_die();
}
add_action('wp_ajax_create_link_listaimoveis', 'create_link_listaimoveis_ajax_handler');

//# Send Message
function send_message_broker_ajax_handler()
{

    $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : 0;
    $title = get_post_meta($post_id, 'name', true);

    $views = intval(get_post_meta($post_id, 'views', true));
    if ($views > 0) {
        wp_send_json_success(array('hasView' => $views), 200);
        wp_die();
    }

    $args = [
        'post_type' => 'immobile',
        'posts_per_page' => -1,
    ];

    $meta_keys = [
        'location',
        'condominium',
        'financing',
        'bedrooms',
        'amount_min',
        'amount_max',
        'facade'
    ];

    if (!empty($title)) {
        $args['s'] = $title;
    }


    foreach ($meta_keys as $meta_key) {
        $meta_value = get_post_meta($post_id, $meta_key, true);

        if (!empty($meta_value)) {
            $args['meta_query'][] = [
                'key' => $meta_key,
                'value' => $meta_value,
                'compare' => "="
            ];
        }
    }

    $immobile = new WP_Query($args);
    $brokers = [];
    while ($immobile->have_posts()) {
        $immobile->the_post();
        $broker = get_post_meta(get_the_ID(), 'broker', true);
        $brokers[$broker] = get_the_title();
    }
    wp_reset_postdata();

    $list = [];
    foreach ($brokers as $user_id => $immobile) {
        $user = get_userdata($user_id);

        $list[] = [
            "phone" => get_user_meta($user_id, 'phone', true),
            "email" => $user->user_email,
            "name" => $user->display_name,
            "title" => $immobile,
        ];
    }
    $listJson = json_encode($list);

    if (!is_user_logged_in()) {
        update_post_meta($post_id, 'views', $views + 1);
        wp_send_json_success(array('json' => $listJson), 200);
        wp_die();
    }

    wp_send_json_success("", 200);
}

add_action('wp_ajax_send_message_broker', 'send_message_broker_ajax_handler');
add_action('wp_ajax_nopriv_send_message_broker', 'send_message_broker_ajax_handler');
