<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Knowledge_Hub
 * @author     Md. Zahurul Islam <hi@zahurul.com>
 */

defined( 'ABSPATH' ) || exit;

/**
 * Islamic_Donation_Admin class.
 *
 * @since 1.0.0
 */
class ID_Knowledge_Hub {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Custom post type knowledge-hub.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $post_type    custom post type knowledge-hub.
	 */
	public $post_type;

    /**
	 * Custom taxonomies array for knowledge hub.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $custom_taxonomies    custom taxonomy array.
	 */
	public $custom_taxonomies;

	/**
	 * The single instance of the class
	 *
	 * @var Islamic_Donation_Admin
	 */
	protected static $_instance = null;

	/**
	 * Main Islamic_Donation_Admin Instance.
	 *
	 * Ensures only one instance of Islamic_Donation_Admin is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return Islamic_Donation_Admin Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = KNOWLEDGE_HUB_NAME;
		$this->version = KNOWLEDGE_HUB_VERSION;

		$this->post_type = 'knowledge-hub';
		$this->custom_taxonomies = array(
			'knowledge-category' => array(
				'singular'              => _x( 'Category', 'taxonomy singular name', $this->plugin_name ),
				'plural'                => _x( 'Categories', 'taxonomy plural name', $this->plugin_name ),
				'support'               => array( $this->post_type ),
				'hierarchical'          => true,
				'slug'                  => 'knowledge-hub-category'
			),
			'knowledge-tag'    => array(
				'singular'              => _x( 'Tag', 'taxonomy singular name', $this->plugin_name ),
				'plural'                => _x( 'Tags', 'taxonomy plural name', $this->plugin_name ),
				'support'               => array( $this->post_type ),
				'hierarchical'          => false,
				'slug'                  => 'knowledge-hub-tag'
			)
		);
	}

    /**
	 * Register custom post project.
	 * For more: https://developer.wordpress.org/reference/functions/register_post_type/
	 * Action Hook: 'init'
	 *
	 * @since   1.0.0
	 */
	public function register_custom_post_knowledge_hub() {

		$singular_label     = _x( 'Knowledge Hub', 'post type singular name', $this->plugin_name );
		$plural_label       = _x( 'Knowledge Hub', 'post type general name', $this->plugin_name );

		$post_args = array(
			'labels' => array(
				'name'                  => $plural_label,
				'singular_name'         => $singular_label,
				'add_new_item'          => sprintf( __( 'Add New %1$s', $this->plugin_name ), $singular_label ),
				'edit_item'             => sprintf( __( 'Edit %1$s', $this->plugin_name ), $singular_label ),
				'new_item'              => sprintf( __( 'New %1$s', $this->plugin_name ), $singular_label ),
				'view_item'             => sprintf( __( 'View %1$s', $this->plugin_name ), $singular_label ),
				'search_items'          => sprintf( __( 'Search %1$s', $this->plugin_name ), $plural_label ),
				'not_found'             => sprintf( __( 'No %1$s found', $this->plugin_name ), $plural_label ),
				'not_found_in_trash'    => sprintf( __( 'No %1$s found in trash', $this->plugin_name ), $plural_label ),
				'parent_item_colon'     => sprintf( __( 'Parent %1$s', $this->plugin_name ), $singular_label ),
				'menu_name'             => $plural_label,
				),
			'public'                => true,
			'publicly_queryable'    => true,
			'show_ui'               => true,
			'query_var'             => true,
			'hierarchical'          => false,
			'capability_type'       => 'post',
			'has_archive'           => $this->post_type,
			'menu_icon'             => 'dashicons-welcome-learn-more',
			'menu_position'         => 30,
			'taxonomies'            => array(),
			/*Add Gutenberg Support to WordPress Custom Post Types*/
			'show_in_rest'          => true,
			'supports'              => array(
				'title',
				'editor',
				'excerpt',
				'thumbnail',
				'custom-fields',
				'author',
				'page-attributes',
				// 'comments',
				// 'post-formats',
				),
			'rewrite'               => array(
				'slug'          => sanitize_title_with_dashes($plural_label),
				'with_front'    => false,
				'pages'         => true
				)
			);

		register_post_type( $this->post_type, $post_args );
	}

	/**
	 * Register custom taxonomy
	 * For more: https://developer.wordpress.org/reference/functions/register_taxonomy/
	 * Action Hook: 'init'
	 *
	 * @since   1.0.0
	 */
	public function register_custom_taxonomies() {

		if( empty( $this->custom_taxonomies ) ) {
			return false;
		}

		foreach ( $this->custom_taxonomies as $taxonomy => $args ) {

			if( ! isset( $args['singular'] ) || empty( $args['singular'] ) ){
				$args['singular'] = $taxonomy;
			}

			if( ! isset( $args['plural'] ) || empty( $args['plural'] ) ){
				$args['plural'] = $taxonomy;
			}

			$taxonomy_labels = array(
				'name'              => $args['plural'],
				'singular_name'     => $args['singular'],
				'search_items'      => sprintf( __( 'Search in %1$s', $this->plugin_name ), strtolower( $args['plural'] ) ),
				'all_items'         => sprintf( __( 'All %1$s', $this->plugin_name ), $args['plural'] ),
				'most_used_items'   => null,
				'parent_item'       => sprintf( __( 'Parent %1$s', $this->plugin_name ), $args['singular'] ),
				'parent_item_colon' => sprintf( __( 'Parent %1$s:', $this->plugin_name ), $args['singular'] ),
				'edit_item'         => sprintf( __( 'Edit %1$s', $this->plugin_name ), $args['singular'] ),
				'update_item'       => sprintf( __( 'Update %1$s', $this->plugin_name ), $args['singular'] ),
				'add_new_item'      => sprintf( __( 'Add new %1$s', $this->plugin_name ), $args['singular'] ),
				'new_item_name'     => sprintf( __( 'New %1$s', $this->plugin_name ), $args['singular'] ),
				'not_found'         => sprintf( __( 'No %1$s found.', $this->plugin_name ), $args['plural'] ),
				'popular_items'     => sprintf( __( 'Popular %1$s', $this->plugin_name ), $args['plural'] ),
				'menu_name'         => $args['plural'],
				'separate_items_with_commas' => sprintf( __( 'Separate %1$s with commas.', $this->plugin_name ), $args['plural'] )
			);

			if( ! isset( $args["support"] ) ) {
				$args["support"] = array( $this->post_type );
			}

			if( ! isset( $args["slug"] ) ) {
				$args["slug"] = $taxonomy;
			}

			if( ! isset( $args["hierarchical"] ) ) {
				$args["hierarchical"] = true;
			}

			if( ! isset( $args["show_admin_column"] ) ) {
				$args["show_admin_column"] = true;
			}

			if( ! isset( $args["show_tagcloud"] ) ) {
				$args["show_tagcloud"] = true;
			}

			if( ! isset( $args["query_var"] ) ) {
				$args["query_var"] = true;
			}

			if( ! isset( $args["public"] ) ) {
				$args["public"] = true;
			}

			if( ! isset( $args["show_in_nav_menus"] ) ) {
				$args["show_in_nav_menus"] = true;
			}

			if( ! isset( $args["show_ui"] ) ) {
				$args["show_ui"] = true;
			}

			register_taxonomy(
				$taxonomy,
				$args["support"],
				array(
					'hierarchical'      => $args["hierarchical"],
					'labels'            => $taxonomy_labels,
					'show_admin_column' => $args["show_admin_column"],
					'show_ui'           => $args["show_ui"],
					'show_tagcloud'     => $args["show_tagcloud"],
					'query_var'         => $args["query_var"],
					'public'            => $args["public"],
					'show_in_nav_menus' => $args["show_in_nav_menus"],
					/*Add Gutenberg Support to WordPress Custom Post Types*/
					'show_in_rest'      => true,
					'rewrite'           => array(
						'slug' => $args["slug"],
						'with_front' => false, //removing slug 'news' from blog permalink settings in WP
						'hierarchical' => $args["hierarchical"]
					)
				)
			);
		}
	}

}
