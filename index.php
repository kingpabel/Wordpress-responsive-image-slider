<?php
/*
  Plugin Name: Image Slider
  Plugin URI: http://roxcoder.com
  Description: This plugin use responsive image slider.
  Author: Imtiaz Pabel
  Version: 1.0
  Author URI: http://roxcoder.com
 */

//Custom Post Type and Custom Taxonomy
add_action('init', 'slider_settings');

function slider_settings() {
    $labels = array(
        'name' => __('Slider'),
        'singular_name' => __('Slider'),
        'add_new' => __('Add slider image'),
        'all_items' => __('All slider image'),
        'add_new_item' => __('Add slider image'),
        'edit_item' => __('Edit slider image'),
        'new_item' => __('New slider image'),
        'view_item' => __('View slider image'),
        'search_items' => __('Search slider image'),
        'not_found' => __('No slider image found'),
        'not_found_in_trash' => __('No slider image found in trash'),
        'parent_item_colon' => __('Parent slider image')
    );
    $args = array(
        'labels' => $labels,
        'menu_icon' => plugins_url('icon.png', __FILE__),
        'public' => true,
        'has_archive' => true,
        'publicly_queryable' => true,
        'query_var' => true,
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'supports' => array(
            'title',
            'thumbnail',
            'revisions',
        ),
        'taxonomies' => array('slider_category'), // add default post categories and tags
        'menu_position' => 5,
    );
    register_post_type('slider', $args);

    $taxonomy_labels = array(
        'name' => __('Slider Category'),
        'singular_name' => __('Slider Category'),
        'add_new' => __('Add slider category'),
        'all_items' => __('All slider category'),
        'add_new_item' => __('Add slider category'),
        'edit_item' => __('Edit slider category'),
        'new_item' => __('New slider category'),
        'view_item' => __('View slider category'),
        'search_items' => __('Search slider category'),
        'not_found' => __('No slider category found'),
        'not_found_in_trash' => __('No slider category found in trash')
    );
    register_taxonomy('slider-category', // register custom taxonomy - quote category
            'slider', array('hierarchical' => true,
        'labels' => $taxonomy_labels,
        'show_ui' => true,
            )
    );
    add_image_size( 'slider_thumb', 50, 50, true );
}

function rename_category_description() {
    global $current_screen;
    if ($current_screen->id == 'edit-slider-category') {
        ?>
        <script type="text/javascript">
            jQuery('document').ready(function() {
            jQuery("label[for='tag-description']").parent().remove();
                    jQuery("label[for='tag-slug']").parent().remove();
                    jQuery("label[for='tag-name']").text("Slider Category Name");
            });</script>
        <?php
    }
}

add_action('admin_head', 'rename_category_description');
add_action('slider-category_edit_form_fields', 'extra_tax_fields', 10, 2);

//add extra fields to custom taxonomy edit form callback function
function extra_tax_fields($tag) {
    $t_id = $tag->term_id;
    $term_meta = get_option("taxonomy_$t_id");
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="max_width"><?php _e('Max Width'); ?></label></th>
        <td>
            <input type="number" name="term_meta[max_width]" id="term_meta[max_width]" size="3" style="width:60%;" value="<?php echo $term_meta['max_width'] ? $term_meta['max_width'] : ''; ?>"><br />
            <span class="description"><?php _e('Maximum Width of slider(optional)'); ?></span>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="speed"><?php _e('Speed'); ?></label></th>
        <td>
            <input type="number" name="term_meta[speed]" id="term_meta[speed]" size="3" style="width:60%;" value="<?php echo $term_meta['speed'] ? $term_meta['speed'] : ''; ?>"><br />
            <span class="description"><?php _e('Speed of slider(optional)'); ?></span>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="auto"><?php _e('Auto'); ?></label></th>
        <td>
            <input type="radio" name="term_meta[auto]" id="term_meta[auto]" size="3" style="width:3%;" <?php if ($term_meta['auto'] == 'true') echo 'checked' ?> value="true">True<br />
            <input type="radio" name="term_meta[auto]" id="term_meta[auto]" size="3" style="width:3%;" <?php if ($term_meta['auto'] == 'false') echo 'checked' ?> value="false">False<br />
            <span class="description"><?php _e('Auto moving slider imagee(optional)'); ?></span>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="effect"><?php _e('Effect'); ?></label></th>
        <td>
            <select name="term_meta[effect]" id="term_meta[effect]" size="3" style="width:60%;">
                <option value="normal" <?php if ($term_meta['effect'] == 'normal') echo 'selected' ?>>Normal</option>
                <option value="pager" <?php if ($term_meta['effect'] == 'pager') echo 'selected' ?>>Pager</option>
                <option value="thumbnail" <?php if ($term_meta['effect'] == 'thumbnail') echo 'selected' ?>>Thumbnail</option>
                <option value="caption"  <?php if ($term_meta['effect'] == 'caption') echo 'selected' ?>>Caption</option>
            </select>
            <span class="description"><?php _e('Show pager effect(optional)'); ?></span>
        </td>
    </tr>
    <?php
}

add_action('edited_slider-category', 'save_extra_taxonomy_fileds', 10, 2);

function save_extra_taxonomy_fileds($term_id) {
    if (isset($_POST['term_meta'])) {
        $t_id = $term_id;
        $term_meta = get_option("taxonomy_$t_id");
        $cat_keys = array_keys($_POST['term_meta']);
        foreach ($cat_keys as $key) {
            if (isset($_POST['term_meta'][$key])) {
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        //save the option array
        update_option("taxonomy_$t_id", $term_meta);
    }
}

add_shortcode('slider', 'sho_slider');

function theme_name_scripts() {
    wp_enqueue_script('script-name', plugins_url('/js/responsiveslides.min.js', __FILE__), array(), false, true);
    wp_enqueue_style('css-name', plugins_url('/css/responsiveslides.css', __FILE__));
    wp_enqueue_style('custom-design', plugins_url('/css/demo.css', __FILE__));
    wp_enqueue_script('namespaceformyscript', 'http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js', array('jquery'));
}

add_action('wp_enqueue_scripts', 'theme_name_scripts');

function sho_slider($atts) {
    $atts = shortcode_atts(
		array(
			'id' => 'Write Correctly Short Code',
		), $atts);

    $args = array(
        'include' =>$atts['id'],
        'taxonomy' => 'slider-category',
    );
    $categories = get_categories($args);
    $t_id = $categories[0]->term_id;
    $t_slug = $categories[0]->slug;
    $term_data = get_option("taxonomy_$t_id");
    ?>    
    <script>
                $(function() {
                $("#slider3").responsiveSlides({
    <?php
    if (isset($term_data['effect'])) {
        if ($term_data['effect'] == 'pager') {
            ?>
                        pager: true,
        <?php } elseif ($term_data['effect'] == 'thumbnail') { ?>
                        manualControls: '#slider3-pager',
        <?php } elseif ($term_data['effect'] == 'caption') { ?>
                        pager: false,
                                nav: true,
                                speed: <?php echo $term_data['speed'] ?>,
                                namespace: "callbacks",
                                before: function () {
                                $('.events').append("<li>before event fired.</li>");
                                },
                                after: function () {
                                $('.events').append("<li>after event fired.</li>");
                                }
            <?php
        } else {
            echo '';
        }
    } else {
        ?>       
                    auto: <?php if(isset($term_data['auto']) && !empty($term_data['auto'])){echo $term_data['auto'];}else { echo 'true';} ?>,
                    maxwidth: <?php if(isset($term_data['max_width']) && !empty($term_data['max_width'])){ echo $term_data['max_width'];}else { echo '500';} ?>,
                            speed: <?php if(isset($term_data['speed']) && !empty($term_data['speed'])){ echo $term_data['speed'];}else{ echo '500';} 
    } ?>


    <?php if (isset($term_data['effect']) && $term_data['effect'] != 'caption') {
        ?>

                    auto: <?php if(isset($term_data['auto']) && !empty($term_data['auto'])){echo $term_data['auto'];}else { echo 'true';} ?>,
                    maxwidthh: <?php if(isset($term_data['max_width']) && !empty($term_data['max_width'])){ echo $term_data['max_width'];}else { echo '500';} ?>,
                            speed: <?php if(isset($term_data['speed']) && !empty($term_data['speed'])){ echo $term_data['speed'];}else{ echo '500';} ?> <?php } ?>
                });
                });

    </script>
    <?php
    $return_string = "";
    $return_string2 = "";
    if (isset($term_data['effect']) && $term_data['effect'] == 'caption')
        $return_string .='<div class="callbacks_container">';
    
    if (isset($term_data['effect']) && $term_data['effect'] == 'thumbnail')
        $return_string2 .='<ul id="slider3-pager">';
    
    $return_string .='<ul class="rslides" id="slider3">';
    $args = array(
        'posts_per_page' => 9,
        'post_type' => 'slider',
        'tax_query' => array(
            array(
                'taxonomy' => 'slider-category',
                'field' => 'slug',
                'terms' => $t_slug,
            ),
        ),
    );
    $the_query = new WP_Query($args);
    while ($the_query->have_posts()) : $the_query->the_post();

        $return_string .='<li>' . get_the_post_thumbnail();
        if (isset($term_data['effect']) && $term_data['effect'] == 'caption')
            $return_string .= '<p class="caption">' . get_the_title() . '</p>';
        
        if (isset($term_data['effect']) && $term_data['effect'] == 'thumbnail'){
        $url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID),'slider_thumb');
        $return_string2 .='<li><a href="#"><img src="'.$url['0'].'" alt=" ">';

        $return_string2 .= '</a></li>';}

        $return_string .= '</li>';

    endwhile;
    $return_string .='</ul>';
    
    $return_string2 .='</ul>';
    if (isset($term_data['effect']) && $term_data['effect'] == 'caption')
        $return_string .='</div>';
    wp_reset_postdata();
    return $return_string.' '.$return_string2;
}
