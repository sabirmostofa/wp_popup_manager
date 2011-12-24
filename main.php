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
    public $prefix='wp_pop';

    function __construct() {
        $this->set_meta();
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'front_scripts'));
        add_action('wp_print_styles', array($this, 'front_css'));
        add_action('add_meta_boxes', array($this, 'add_custom_box'));
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

    function show_box() {
        $info_array = array('popup_logo', 'popup_category', 'popup_rating', 'popup_info', 'popup_ad_image_link', 'popup_ad_image_target');
        $meta_box = $this->meta_box;
        global $post;
        $to_show =  get_post_meta($post->ID, 'popup_checkbox', true);
        if ($to_show)
            echo '<input type="checkbox" id="wp_popup_show" name="popup_checkbox" value="checked" checked="checked"/>';
        else
            echo '<input type="checkbox" id="wp_popup_show" name="popup_checkbox" value="checked"/>';
        ?>

        <div id="wp_popup_div" style="<?php echo  $abs = $to_show?'' : 'display:none' ?>">
           Logo Link:
            <a href="media-upload.php?post_id=216&amp;TB_iframe=1&amp;width=640&amp;height=384"  class="thickbox" title="Add Media"><img src="<?php echo plugins_url('images/media-button-other.gif',__FILE__) ?>" alt="Add Media" onclick="return false;"></a>
            
            <br/>
          <input class="wp_popup_inputs"  type="text" id="popup_logo" name="popup_logo" value="<?php ?>"/>
          <br/>
        Category:
          <br/>
          <input class="wp_popup_inputs" type="text" name="popup_category" value="<?php ?>" />
          <br/>
         Rating:
          <br/>
          <input class="wp_popup_inputs" type="text" name="popup_rating" value="<?php ?>"/>
          <br/>
          Information 
          <br/>
          <textarea class="wp_popup_inputs" type="text" name="popup_info" rows="10">
          </textarea>
          <br/>
           Ad Image Link:
            <a href="media-upload.php?post_id=216&amp;TB_iframe=1&amp;width=640&amp;height=384"  class="thickbox" title="Add Media"><img src="<?php echo plugins_url('images/media-button-other.gif',__FILE__) ?>" alt="Add Media" onclick="return false;"></a>
          <br/>
          <input class="wp_popup_inputs" type="text" id="popup_ad_image_link" name="popup_ad_image_link"/>
          <br/>
          Ad Target Link:
          <br/>
          <input class="wp_popup_inputs" type="text" id="popup_ad_image_target" name="popup_ad_image_target"/>
       
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
