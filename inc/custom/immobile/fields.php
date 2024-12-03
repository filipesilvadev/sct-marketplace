<?php
$brokers = new WP_Query([
    'post_type' => 'broker'
]);

$locations = get_terms(array(
    'taxonomy' => 'locations',
    'hide_empty' => false,
));

$gallery = get_post_meta($post->ID, 'immobile_gallery', true);
$options = ['Sim', 'Não'];
$property_types = ['Sobrado', 'Térreo'];
?>
<div class="wrap">
    <div>
        <label for="broker">Corretor Responsável:</label><br>
        <select name="broker" id="broker">
            <?php if ($brokers->have_posts()) : ?>
                <?php while ($brokers->have_posts()) :
                    $brokers->the_post();
                ?>
                    <?php if (get_post_meta($post->ID, 'broker', true) == get_the_title()) : ?>
                        <option selected value="<?php echo get_the_title(); ?>"><?php the_title(); ?></option>
                    <?php else : ?>
                        <option value="<?php echo get_the_title(); ?>"><?php the_title(); ?></option>
                    <?php endif; ?>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>
    </div>
    <div>
        <label for="location">Localidade:</label><br>
        <select name="location" id="location">
            <?php foreach ($locations as $location) : ?>
                <?php if (get_post_meta($post->ID, 'location', true) == $location->name) : ?>
                    <option selected value="<?php echo $location->name ?>"><?php echo $location->name; ?></option>
                <?php else : ?>
                    <option value="<?php echo $location->name ?>"><?php echo $location->name; ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label for="property_type">Tipo de Imóvel:</label><br>
        <select name="property_type" id="property_type">
            <?php foreach ($property_types as $property_type) : ?>
                <?php if (get_post_meta($post->ID, 'property_type', true) == $property_type) : ?>
                    <option selected value="<?php echo $property_type ?>"><?php echo $property_type; ?></option>
                <?php else : ?>
                    <option value="<?php echo $property_type ?>"><?php echo $property_type; ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label for="condominium">Condomínio:</label><br>
        <select name="condominium" id="condominium">
            <?php foreach ($options as $option) : ?>
                <?php if (get_post_meta($post->ID, 'condominium', true) == $option) : ?>
                    <option selected value="<?php echo $option ?>"><?php echo $option; ?></option>
                <?php else : ?>
                    <option value="<?php echo $option ?>"><?php echo $option; ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label for="financing">Aceita Financiamento:</label><br>
        <select name="financing" id="financing">
            <?php foreach ($options as $option) : ?>
                <?php if (get_post_meta($post->ID, 'financing', true) == $option) : ?>
                    <option selected value="<?php echo $option ?>"><?php echo $option; ?></option>
                <?php else : ?>
                    <option value="<?php echo $option ?>"><?php echo $option; ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label for="committee">Comissão:</label><br>
        <input type="number" name="committee" id="committee" value="<?php echo get_post_meta($post->ID, 'committee', true); ?>">
    </div>
    <div>
        <label for="size">Comissão So Casa Top:</label><br>
        <input type="committee_socasatop" name="committee_socasatop" id="committee_socasatop" value="<?php echo get_post_meta($post->ID, 'committee_socasatop', true); ?>">
    </div>
    <div>
        <label for="bedrooms">Quartos:</label><br>
        <input type="number" name="bedrooms" id="bedrooms" value="<?php echo get_post_meta($post->ID, 'bedrooms', true); ?>">
    </div>
    <div>
        <label for="size">Metragem:</label><br>
        <input type="number" name="size" id="size" value="<?php echo get_post_meta($post->ID, 'size', true); ?>">
    </div>
    <div>
        <label for="amount">Valor:</label><br>
        <input type="number" name="amount" id="amount" value="<?php echo get_post_meta($post->ID, 'amount', true); ?>">
    </div>
    <div>
        <label for="details">Detalhes:</label><br>
        <textarea name="details" id="details"><?php echo get_post_meta($post->ID, 'details', true); ?></textarea>
    </div>
    <div>
        <label for="link">Detalhes:</label><br>
        <input type="url" name="link" id="link" value="<?php echo get_post_meta($post->ID, 'link', true); ?>">
    </div>
    <div>
        <label for="facade">Tipo de Fachada:</label><br>
        <input type="text" name="facade" id="facade" value="<?php echo get_post_meta($post->ID, 'facade', true); ?>">
    </div>
    <div>
        <label for="immobile_gallery">Galeria de Imagens</label><br>
        <input type="hidden" id="immobile_gallery" name="immobile_gallery" value="<?php echo esc_attr($gallery); ?>" />
        <button type="button" class="button" id="upload_gallery_button">Adicionar Imagens</button>
        <div id="gallery_preview">
            <?php
            if ($gallery) {
                $gallery_ids = explode(',', $gallery);
                foreach ($gallery_ids as $id) {
                    $image = wp_get_attachment_image_src($id, 'thumbnail');
                    if ($image) {
                        echo '<img src="' . esc_url($image[0]) . '" style="margin: 5px;" />';
                    }
                }
            }
            ?>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function($) {
        var file_frame;
        $('#upload_gallery_button').on('click', function(event) {
            event.preventDefault();

            if (file_frame) {
                file_frame.open();
                return;
            }

            file_frame = wp.media.frames.file_frame = wp.media({
                title: 'Select or Upload Images',
                button: {
                    text: 'Use these images'
                },
                multiple: true
            });

            file_frame.on('select', function() {
                var attachments = file_frame.state().get('selection').map(function(attachment) {
                    attachment = attachment.toJSON();
                    return attachment.id;
                });

                var ids = attachments.join(',');
                $('#immobile_gallery').val(ids);

                var gallery_preview = $('#gallery_preview');
                gallery_preview.empty();
                attachments.forEach(function(id) {
                    wp.media.attachment(id).fetch().then(function(attachment) {
                        gallery_preview.append('<img src="' + attachment.sizes.thumbnail.url + '" style="margin: 5px;" />');
                    });
                });
            });

            file_frame.open();
        });
    });
</script>