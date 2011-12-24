<?php
/*
  Plugin Name: WP-Popup-Maker
  Plugin URI: http://sabirul-mostofa.blogspot.com
  Description: Popup for pages
  Version: 1.0
  Author: Sabirul Mostofa
  Author URI: http://sabirul-mostofa.blogspot.com
 */


$wpMiscTools = new wpPopupTools();

class wpPopupTools {

    public $meta_box = array();
    public $prefix = 'wp_pop';

    function __construct() {
        $this->set_meta();
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'front_scripts'));
        add_action('wp_print_styles', array($this, 'front_css'));
        add_action('add_meta_boxes', array($this, 'add_custom_box'));
        add_action('save_post', array($this, 'save_postdata'));
        add_filter('the_content', array($this, 'add_popup'));
    }

    function front_scripts() {
        global $post;
        if (is_page() || is_single()) {
            wp_enqueue_script('jquery');
            if (!(is_admin())) {
                wp_enqueue_script('wpmsc_front_script', plugins_url('/', __FILE__) . 'js/script_front.js');
                wp_localize_script('wpmsc_front_script', 'wpmscSettings', array(
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'pluginurl' => plugins_url('/', __FILE__),
                    'site_url' => site_url(),
                    'post_id' => $post->ID
                ));
            }
        }
    }

    function admin_scripts() {
        wp_enqueue_script('wp_pop_admin_script', plugins_url('/', __FILE__) . 'js/script_admin.js');
        wp_register_style('wp_pop_admin_css', plugins_url('/', __FILE__) . 'css/style_admin.css', false, '1.0.0');
        wp_enqueue_style('wp_pop_admin_css');
    }

    function front_css() {
        if (!(is_admin())):
            wp_enqueue_style('wpmsc_front_css', plugins_url('/', __FILE__) . 'css/style_front.css');
        endif;
    }
    
    //adding poupt
    
    function add_popup($content){ 
        global $post;
        if(!get_post_meta($post->ID, 'popup_checkbox', true))return $content;        
       $info_array = array('popup_logo', 'popup_category', 'popup_rating', 'popup_info', 'popup_ad_image_link', 'popup_ad_image_target');
      
       foreach ($info_array as $single) {
            if ($val = get_post_meta($post->ID, $single, true))
                $$single = $val;
            else
                $$single = '';
        }
        
        $xtra=<<<ST
        <div id="popup_back">
            
            </div>
            <div id="popup_div">
            <div id="popup_close">
            X
            </div>
            <div class="clear"></div>
            <div id="popup_logo"><img src="$popup_logo"> </div>
                <div id="popup_category">Rank:$popup_category</div>
                <div id="popup_rating">Rating:$popup_rating</div>
                <div id="popup_info">$popup_info</div>
                <div id="popup_ad"><a href="$popup_ad_image_target"><img src="$popup_ad_image_link"/></a></div>
            </div>
ST;
        $content.=$xtra;
        return $content;
    }

    function add_custom_box() {
        $post_id = -3;
        if (isset($_REQUEST['post']))
            $post_id = $_REQUEST['post'];
        if (in_category(1, $post_id))
            return;
        $meta_box = $this->meta_box;

        add_meta_box($meta_box['id'], $meta_box['title'], array($this, 'show_box'), $meta_box['page'], $meta_box['context'], $meta_box['priority']);
        //  add_meta_box($meta_box['id'], $meta_box['title'], array($this, 'show_box'), 'post', $meta_box['context'], $meta_box['priority']);
    }

    //saving postdata
    function save_postdata($post_id) {
        $info_array = array('popup_logo', 'popup_category', 'popup_rating', 'popup_info', 'popup_ad_image_link', 'popup_ad_image_target');

        global $wpdb;
//        var_dump($_POST);
//        exit;

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        if (isset($_POST['post_ID']))
            $post_id = $_POST['post_ID'];
        $val = (isset($_POST['popup_checkbox'])) ? 1 : 0;
        update_post_meta($post_id, 'popup_checkbox', $val);
        
        if(!$val)return;

        foreach ($info_array as $single) {
            if (isset($_POST["$single"]))
                update_post_meta($post_id, $single, trim($_POST["$single"]));
        }
    }

    //show box

    function show_box() {
        global $post;
        $info_array = array('popup_logo', 'popup_category', 'popup_rating', 'popup_info', 'popup_ad_image_link', 'popup_ad_image_target');

        foreach ($info_array as $single) {
            if ($val = get_post_meta($post->ID, $single, true))
                $$single = $val;
            else
                $$single = '';
        }

        $meta_box = $this->meta_box;

        $to_show = get_post_meta($post->ID, 'popup_checkbox', true);
        if ($to_show)
            echo '<input type="checkbox" id="wp_popup_show" name="popup_checkbox" value="checked" checked="checked"/>';
        else
            echo '<input type="checkbox" id="wp_popup_show" name="popup_checkbox" value="checked"/>';
        ?>

        <div id="wp_popup_div" style="<?php echo $abs = $to_show ? '' : 'display:none' ?>">
            Logo Link:
            <a href="media-upload.php?post_id=216&amp;TB_iframe=1&amp;width=640&amp;height=384"  class="thickbox" title="Add Media"><img src="<?php echo plugins_url('images/media-button-other.gif', __FILE__) ?>" alt="Add Media" onclick="return false;"></a>

            <br/>
            <input  class="wp_popup_inputs"  type="text" id="popup_logo" name="popup_logo" value="<?php echo $popup_logo ?>"/>
            <br/>
            Category:
            <br/>
            <input class="wp_popup_inputs" type="text" name="popup_category" value="<?php echo $popup_category ?>" />
            <br/>
            Rating:
            <br/>
            <input class="wp_popup_inputs" type="text" name="popup_rating" value="<?php echo $popup_rating ?>"/>
            <br/>
            Information 
            <br/>
            <textarea class="wp_popup_inputs" type="text" name="popup_info" rows="10" ><?php echo $popup_info ?></textarea>
            <br/>
            Ad Image Link:
            <a href="media-upload.php?post_id=216&amp;TB_iframe=1&amp;width=640&amp;height=384"  class="thickbox" title="Add Media"><img src="<?php echo plugins_url('images/media-button-other.gif', __FILE__) ?>" alt="Add Media" onclick="return false;"></a>
            <br/>
            <input class="wp_popup_inputs" type="text" id="popup_ad_image_link" name="popup_ad_image_link" value="<?php echo $popup_ad_image_link ?>"/>
            <br/>
            Ad Target Link:
            <br/>
            <input class="wp_popup_inputs" type="text" id="popup_ad_image_target" name="popup_ad_image_target" value="<?php echo $popup_ad_image_target ?>"/>

        </div>
        <?php
    }

    function set_meta() {
        $this->meta_box = array(
            'id' => 'vote-meta-box',
            'title' => "Show Popup div for this Page",
            'page' => 'page',
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                array(
                    'name' => 'Show Popup div for this page',
                    'desc' => ' Check To enable Popup',
                    'id' => $this->prefix . 'checkbox',
                    'type' => 'checkbox',
                    'std' => ''
                )
            )
        );
    }

}
