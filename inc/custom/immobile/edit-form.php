<?php
$args = [
    'role'    => 'author',
    'orderby' => 'display_name',
    'order'   => 'ASC'
];
$brokers_query =  new WP_User_Query($args);
$brokers = $brokers_query->get_results();

$locations = get_terms([
    'taxonomy' => 'locations',
    'hide_empty' => false,
]);
$options = ['Sim', 'Não'];
$property_types = ['Sobrado', 'Térreo'];
?>
<form id="edit-immobile" method="post" class="form">
    <?php if (isset($_GET['post'])) :
        $id = $_GET['post'];
        $gallery = get_post_meta($id, 'immobile_gallery', true);
    ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="form-wrapper">
            <label for="title">Nome:</label>
            <input type="text" name="title" id="title" required value="<?php echo get_the_title($id); ?>">
        </div>
        <div class="form-wrapper">
            <label for="facade">Tipo de Fachada:</label>
            <input type="text" name="facade" id="facade" required value="<?php echo get_post_meta($id, 'facade', true) ?>">
        </div>
        <div class="group-inputs">
            <div class="form-wrapper w-1/2">
                <label for="amount">Valor:</label>
                <input type="text" name="amount" id="amount" required value="<?php echo get_post_meta($id, 'amount', true) ?>">
            </div>
            <div class="form-wrapper w-1/2">
                <label for="bedrooms">Quantos Quartos:</label>
                <input type="number" name="bedrooms" id="bedrooms" required value="<?php echo get_post_meta($id, 'bedrooms', true) ?>">
            </div>
        </div>
        <div class="group-inputs">
            <div class="form-wrapper w-1/2">
                <label for="committee">Comissão:</label>
                <input type="text" name="committee" id="committee" required value="<?php echo get_post_meta($id, 'committee', true) ?>">
            </div>
            <div class="form-wrapper w-1/2">
                <label for="committee_socasatop">Comissão So Casa Top:</label>
                <input type="number" name="committee_socasatop" id="committee_socasatop" required value="<?php echo get_post_meta($id, 'committee_socasatop', true) ?>">
            </div>
        </div>
        <div class="group-inputs">
            <div class="form-wrapper w-1/2">
                <label for="size">Metragem:</label>
                <input type="number" name="size" id="size" required value="<?php echo get_post_meta($id, 'size', true) ?>">
            </div>
            <div class="form-wrapper w-1/2">
                <label for="location">Localidade:</label>
                <select id="location" name="location" class="select2">
                    <?php foreach ($locations as $location) : ?>
                        <?php if (get_post_meta($id, 'location', true) == $location->name) : ?>
                            <option selected value="<?php echo $location->name ?>"><?php echo $location->name; ?></option>
                        <?php else : ?>
                            <option value="<?php echo $location->name ?>"><?php echo $location->name; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="group-inputs">
            <div class="form-wrapper w-1/2">
                <label for="property_type">Tipo de Imóvel:</label>
                <select id="property_type" name="property_type" class="select2">
                    <?php foreach ($property_types as $property_type) : ?>
                        <?php if (get_post_meta($id, 'property_type', true) == $property_type) : ?>
                            <option selected value="<?php echo $property_type ?>"><?php echo $property_type; ?></option>
                        <?php else : ?>
                            <option value="<?php echo $property_type ?>"><?php echo $property_type; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-wrapper w-1/2">
                <label for="financing">Aceita Financiamento:</label>
                <select id="financing" name="financing" class="select2">
                    <?php foreach ($options as $option) : ?>
                        <?php if (get_post_meta($id, 'financing', true) == $option) : ?>
                            <option selected value="<?php echo $option ?>"><?php echo $option; ?></option>
                        <?php else : ?>
                            <option value="<?php echo $option ?>"><?php echo $option; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="group-inputs">
            <div class="form-wrapper w-1/2">
                <label for="condominium">Condomínio:</label>
                <select id="condominium" name="condominium" class="select2">
                    <?php foreach ($options as $option) : ?>
                        <?php if (get_post_meta($id, 'financing', true) == $option) : ?>
                            <option selected value="<?php echo $option ?>"><?php echo $option; ?></option>
                        <?php else : ?>
                            <option value="<?php echo $option ?>"><?php echo $option; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-wrapper w-1/2">
                <label for="broker">Corretor Responsável:</label>
                <select name="broker" id="broker" class="select2">
                    <?php foreach ($brokers as $broker) : ?>
                        <?php if (get_post_meta($id, 'broker', true) == $broker->ID) : ?>
                            <option value="<?php echo $broker->ID ?>" selected><?php echo $broker->display_name; ?></option>
                        <?php else : ?>
                            <option value="<?php echo $broker->ID ?>"><?php echo $broker->display_name; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-wrapper">
            <label for="details">Detalhes:</label>
            <textarea name="details" id="details"><?php echo get_post_meta($id, 'details', true) ?></textarea>
        </div>
        <div class="form-wrapper">
            <label for="link">Link:</label>
            <input type="url" name="link" id="link" value="<?php echo get_post_meta($id, 'link', true) ?>">
        </div>
        <div class="form-wrapper">
            <div class="form-wrapper">
                <label for="immobile_gallery" class="pb-2">Galeria de Imagens</label><br>
                <input type="hidden" id="immobile_gallery" name="immobile_gallery" value="<?php echo $gallery; ?>" />
                <button type="button" id="upload_gallery_button" class="btn btn-info">Adicionar Imagens</button>
            </div>
            <div id="gallery_preview" class="rounded-md border-2 border-dotted border-[#3858e9] p-2">
                <?php
                if ($gallery) {
                    $gallery_ids = explode(',', $gallery);
                    foreach ($gallery_ids as $id) {
                        $image = wp_get_attachment_image_src($id, 'thumbnail');
                        if ($image) {
                            echo '<img src="' . esc_url($image[0]) . '" style="width: 60px;" />';
                        }
                    }
                }
                ?>
            </div>
        </div>
        <button type="submit" class="btn btn-info">
            Editar Imóvel
        </button>
    <?php else : ?>
        <p>Imóvel não encontrado.</p>
    <?php endif; ?>
</form>