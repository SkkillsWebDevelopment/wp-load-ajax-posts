<?php
/**
 Plugin Name: Load Ajax Posts
 Plugin URI: http://www.wordpress.com
 Description: Load Ajax Posts
 Version: 1.0.1
 Author: Ciocoiu Ionut Marius | Skkills.com
 Author URI: http://wordpress.com
 */
define('CIMLAP_DEBUGGING', 1);
define('CIMLAP_VERSION', CIMLAP_DEBUGGING ? rand(100000, 999999) : "1.0.1");
define('CIMLAP_REQUIRED_WP_VERSION', '4.7');
define('CIMLAP_PLUGIN', __FILE__);
define('CIMLAP_PLUGIN_BASENAME', plugin_basename(CIMLAP_PLUGIN));
define('CIMLAP_PLUGIN_NAME', trim(dirname(CIMLAP_PLUGIN_BASENAME), '/'));
define('CIMLAP_PLUGIN_DIR', untrailingslashit(dirname(CIMLAP_PLUGIN)));
define('CIMLAP_PLUGIN_URI', plugins_url(CIMLAP_PLUGIN_NAME));

class load_ajax_posts_plugin
{
	function __construct()
	{
		add_action('wp_ajax_nopriv_cimlap_load_posts', array(
			$this,
			'load_posts'
		), 1000);

		add_action('wp_ajax_cimlap_load_posts', array(
			$this,
			'load_posts'
		), 1000);

		add_action('init', array(
			$this,
			'create_post_type'
		), 1000);

		add_action('add_meta_boxes', array(
			$this,
			'post_meta_box'
		), 1000);

		add_action('save_post', array(
			$this,
			'save_meta_box_data'
		), 1000);

		add_filter('manage_cimlap-ajax-list_posts_columns', array(
			$this,
			'cimlap_ajax_list_posts_table_head'
		));

		add_action('manage_cimlap-ajax-list_posts_custom_column', array(
			$this,
			'cimlap_ajax_list_posts_table_content'
		), 10, 2);

		add_shortcode('cimlap', array(
			$this,
			'cimlap_shortcode'
		));
		
		add_action( 'wp_enqueue_scripts', array(
			$this,'cimlap_scripts'));
		
		add_action('wp_enqueue_scripts', array(
			$this,'cimlap_stylesheet'));
	}

	function load_ajax_posts_plugin()
	{
		$this -> __construct();
	}

    function cimlap_stylesheet() 
    { 
        wp_enqueue_style( 'cimlap-styles', CIMLAP_PLUGIN_URI . '/assets/css/styles.css', array(), CIMACF_VERSION );
    }

    

    function cimlap_scripts() {

        wp_enqueue_script('cimlap-scripts', CIMLAP_PLUGIN_URI . '/assets/js/scripts.js',array("jquery"), CIMACF_VERSION, true );
    }
 
	function create_post_type()
	{
		register_post_type('cimlap-ajax-list', array(
			'labels' => array(
				'name' => __('Ajax List'),
				'singular_name' => __('Ajax List'),
				'add_new' => __('New Ajax List'),
				'add_new_item' => __('Add New Ajax List'),
			),
			'public' => false,
			'has_archive' => false,
			'menu_icon' => 'dashicons-editor-justify',
			'menu_position' => 20,
			'supports' => array('title'),
			'exclude_from_search' => true,
			'show_in_admin_bar' => true,
			'show_ui' => true,
			'show_in_nav_menus' => false,
			'show_in_menu' => "tools.php",
			'publicly_queryable' => true,
			'query_var' => true,
			'rewrite' => false,
		));
	}

	function post_meta_box()
	{

		$screens = array('cimlap-ajax-list');

		foreach ($screens as $screen)
		{

			add_meta_box('cimlap_container_meta_sectionid', __('Form Settings', 'loat_ajax_posts'), array(
				$this,
				'container_meta_box_render'
			), $screen, 'normal', 'high');
		}
	}

	function container_meta_box_render($post)
	{

		wp_nonce_field('cimlap_container_meta_box', 'cimlap_container_meta_box_nonce');

		require_once CIMLAP_PLUGIN_DIR . '/views/view_post_form.php';

	}

	function save_meta_box_data($post_id)
	{
		$is_autosave = wp_is_post_autosave($post_id);
		$is_revision = wp_is_post_revision($post_id);
		$is_valid_nonce = (isset($_POST['cimlap_container_meta_box_nonce']) && wp_verify_nonce($_POST['cimlap_container_meta_box'], basename(__FILE__))) ? 'true' : 'false';

		if ($is_autosave || $is_revision || !$is_valid_nonce)
		{
			return;
		}

		if (isset($_POST['_cimlap_no_posts']))
		{
			echo $_POST['_cimlap_no_posts'];
			update_post_meta($post_id, '_cimlap_no_posts', $_POST['_cimlap_no_posts']);
		}
		if (isset($_POST['_cimlap_post_type']))
		{
			update_post_meta($post_id, '_cimlap_post_type', $_POST['_cimlap_post_type']);
		}

		if (isset($_POST['_cimlap_row_structure']))
		{
			update_post_meta($post_id, '_cimlap_row_structure', $_POST['_cimlap_row_structure']);
		}
	}

	function load_posts()
	{
  		require_once CIMLAP_PLUGIN_DIR . '/inc/generate_shortcode.php';
		
		$load_content = new GenerateShortcode($_REQUEST['cimlap_shortcode_id'], $_REQUEST['cimlap_page']);
		$load_content -> generate_content();
		
		exit();
	}

	function cimlap_ajax_list_posts_table_head($columns)
	{

		$new = array();
		foreach ($columns as $key => $title)
		{
			if ($key == 'date')
			{
				$new['shortcode'] = 'Shortcode';
			}
			$new[$key] = $title;
		}
		return $new;

	}

	function cimlap_ajax_list_posts_table_content($column_name, $post_id)
	{

		if ($column_name == 'shortcode')
		{
			echo '[cimlap id="' . $post_id . '"]';
		}
	}

	function cimlap_shortcode($atts)
	{
		$vars = shortcode_atts(array('id' => 0), $atts);
		
		require_once CIMLAP_PLUGIN_DIR . '/inc/generate_shortcode.php';
		
		echo '<div id="cimlap-ajax-container">';
		
			$load_content = new GenerateShortcode($vars['id']);
			$load_content -> generate_content();
		
		echo '</div>';
		
		echo '<script>';
		echo 'var cimlap_ajax_url = "'.admin_url('admin-ajax.php').'";';
		echo 'var cimlap_shortcode_id = '.$vars['id'].';'; 
		echo '</script>';
		echo '<a href="javascript:void(0);" class="btn-cimlap-more-items"><span class="down">&darr;</span></a>';
		
	}

}

$load_ajax_posts_plugin = new load_ajax_posts_plugin();

?>
