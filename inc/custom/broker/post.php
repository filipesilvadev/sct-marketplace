<?php
function add_phone_field($user)
{
    if (in_array('author', (array) $user->roles)) {
?>
        <h3>Informações Adicionais</h3>
        <table class="form-table">
            <tr>
                <th><label for="phone">Telefone:</label></th>
                <td>
                    <input type="text" name="phone" id="phone" value="<?php echo esc_attr(get_the_author_meta('phone', $user->ID)); ?>" class="regular-text" /><br />
                </td>
            </tr>
        </table>
<?php
    }
}
add_action('show_user_profile', 'add_phone_field');
add_action('edit_user_profile', 'add_phone_field');

function save_phone_field($user_id)
{
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    if (isset($_POST['phone'])) {
        update_user_meta($user_id, 'phone', sanitize_text_field($_POST['phone']));
    }
}

add_action('personal_options_update', 'save_phone_field');
add_action('edit_user_profile_update', 'save_phone_field');

function display_form_broker()
{
    ob_start();
    require_once(__DIR__ . "/form.php");
    $content = ob_get_clean();
    return $content;
}
add_shortcode('form_broker', 'display_form_broker');

function display_edit_form_broker()
{
    ob_start();
    require_once(__DIR__ . "/edit-form.php");
    $content = ob_get_clean();
    return $content;
}
add_shortcode('edit_form_broker', 'display_edit_form_broker');

function display_list_broker()
{
    ob_start();
    require_once(__DIR__ . "/list.php");
    $content = ob_get_clean();
    return $content;
}
add_shortcode('list_brokers', 'display_list_broker');

function display_broker_name()
{
    $user_id = get_post_meta(get_the_ID(), 'broker', true);
    $user_name = get_user_by('ID', $user_id)->display_name;
    return $user_name;
}
add_shortcode('broker_name', 'display_broker_name');
