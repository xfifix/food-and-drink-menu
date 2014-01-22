<?php
/**
 * Class to handle all custom post type definitions for Food and Drink Menu
 */

if ( !defined( 'ABSPATH' ) )
	exit;

class fdmCustomPostTypes {

	public function __construct() {

		// Call when plugin is initialized on every page load
		add_action( 'init', array( $this, 'load_cpts' ) );

		// Handle metaboxes
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta' ) );

	}

	/**
	 * Initialize custom post types
	 * @since 1.1
	 */
	public function load_cpts() {

		// Define the menu taxonomies
		$menu_taxonomies = array();

		// Create filter so addons can modify the taxonomies
		$menu_taxonomies = apply_filters( 'fdm_menu_taxonomies', $menu_taxonomies );

		// Define the menu custom post type
		$args = array(
			'exclude_from_search' => true,
			'labels' => array(
				'name' => _x( 'Menus', FDM_TEXTDOMAIN ),
				'singular_name' => _x( 'Menu', FDM_TEXTDOMAIN ),
				'add_new' => __( 'Add Menu', FDM_TEXTDOMAIN ),
				'add_new_item' => __( 'Add New Menu', FDM_TEXTDOMAIN ),
				'edit' => __( 'Edit', FDM_TEXTDOMAIN ),
				'edit_item' => __( 'Edit Menu', FDM_TEXTDOMAIN ),
				'new_item' => __( 'New Menu', FDM_TEXTDOMAIN ),
				'view' => __( 'View', FDM_TEXTDOMAIN ),
				'view_item' => __( 'View Menu', FDM_TEXTDOMAIN ),
				'search_items' => __( 'Search Menus', FDM_TEXTDOMAIN ),
				'not_found' => __( 'No Menu found', FDM_TEXTDOMAIN ),
				'not_found_in_trash' => __( 'No Menu found in Trash', FDM_TEXTDOMAIN ),
				'parent' => __( 'Parent Menu', FDM_TEXTDOMAIN )
			),
			'menu_position' => 15,
			'public' => true,
			'rewrite' => array( 'slug' => 'menu' ),
			'supports' => array(
				'title',
				'editor',
				'revisions'
			),
			'taxonomies' => array_keys( $menu_taxonomies )
		);

		// Create filter so addons can modify the arguments
		$args = apply_filters( 'fdm_menu_args', $args );

		// Add an action so addons can hook in before the menu is registered
		do_action( 'fdm_menu_pre_register' );

		// Register the menu
		register_post_type( 'fdm-menu', $args );

		// Add an action so addons can hook in after the menu is registered
		do_action( 'fdm_menu_post_register' );

		// Define the menu item taxonomies
		$menu_item_taxonomies = array(

			// Create menu sections (desserts, entrees, etc)
			'fdm-menu-section'	=> array(
				'hierarchy'	=> true,
				'labels' 	=> array(
					'name' => _x( 'Menu Sections', 'taxonomy general name', FDM_TEXTDOMAIN ),
					'singular_name' => _x( 'Menu Section', 'taxonomy singular name', FDM_TEXTDOMAIN ),
					'search_items' => __( 'Search Menu Sections', FDM_TEXTDOMAIN ),
					'all_items' => __( 'All Menu Sections', FDM_TEXTDOMAIN ),
					'parent_item' => __( 'Menu Section', FDM_TEXTDOMAIN ),
					'parent_item_colon' => __( 'Menu Section:', FDM_TEXTDOMAIN ),
					'edit_item' => __( 'Edit Menu Section', FDM_TEXTDOMAIN ),
					'update_item' => __( 'Update Menu Section', FDM_TEXTDOMAIN ),
					'add_new_item' => __( 'Add New Menu Section', FDM_TEXTDOMAIN ),
					'new_item_name' => __( 'Menu Section', FDM_TEXTDOMAIN ),
				)
			)

		);

		// Create filter so addons can modify the taxonomies
		$menu_item_taxonomies = apply_filters( 'fdm_menu_item_taxonomies', $menu_item_taxonomies );

		// Register taxonomies
		foreach( $menu_item_taxonomies as $id => $taxonomy ) {
			register_taxonomy(
				$id,
				'',
				$taxonomy
			);
		}

		// Define the Menu Item custom post type
		$args = array(
			'exclude_from_search' => true,
			'labels' => array(
				'name' => _x( 'Menu Items', FDM_TEXTDOMAIN ),
				'singular_name' => _x( 'Menu Item', FDM_TEXTDOMAIN ),
				'add_new' => __( 'Add Menu Item', FDM_TEXTDOMAIN ),
				'add_new_item' => __( 'Add New Menu Item', FDM_TEXTDOMAIN ),
				'edit' => __( 'Edit', FDM_TEXTDOMAIN ),
				'edit_item' => __( 'Edit Menu Item', FDM_TEXTDOMAIN ),
				'new_item' => __( 'New Menu Item', FDM_TEXTDOMAIN ),
				'view' => __( 'View', FDM_TEXTDOMAIN ),
				'view_item' => __( 'View Menu Item', FDM_TEXTDOMAIN ),
				'search_items' => __( 'Search Menu Items', FDM_TEXTDOMAIN ),
				'not_found' => __( 'No Menu Item found', FDM_TEXTDOMAIN ),
				'not_found_in_trash' => __( 'No Menu Item found in Trash', FDM_TEXTDOMAIN ),
				'parent' => __( 'Parent Menu Item', FDM_TEXTDOMAIN )
			),
			'menu_position' => 15,
			'public' => true,
			'rewrite' => array( 'slug' => 'menu-item' ),
			'supports' => array(
				'title',
				'editor',
				'thumbnail',
				'revisions',
				'page-attributes'
			),
			'taxonomies' => array_keys( $menu_item_taxonomies )
		);

		// Add an action so addons can hook in before the menu is registered
		do_action( 'fdm_menu_item_pre_register' );

		// Register the menu item post type
		register_post_type( 'fdm-menu-item', $args );

		// Add an action so addons can hook in after the menu is registered
		do_action( 'fdm_menu_item_post_register' );

	}

	/**
	 * Add metaboxes to specify custom post type data
	 * @since 1.0
	 */
	function add_meta_boxes() {

		$meta_boxes = array(

			// Add a menu organizer
			'fdm_menu_layout' => array (
				'id'		=>	'fdm_menu_layout',
				'title'		=> __( 'Menu Layout', FDM_TEXTDOMAIN ),
				'callback'	=> array( $this, 'show_menu_organizer' ),
				'post_type'	=> 'fdm-menu',
				'context'	=> 'normal',
				'priority'	=> 'default'
			),

			// Add a box that shows menu shortcode
			'fdm_menu_shortcode' => array (
				'id'		=>	'fdm_menu_shortcode',
				'title'		=> __( 'Menu Shortcode', FDM_TEXTDOMAIN ),
				'callback'	=> array( $this, 'show_menu_shortcode' ),
				'post_type'	=> 'fdm-menu',
				'context'	=> 'side',
				'priority'	=> 'default'
			)

		);

		// Add menu item price metabox
		if ( !get_option( 'fdm-disable-price' ) ) {
			$meta_boxes['fdm_menu_item_price'] = array (
				'id'		=>	'fdm_item_price',
				'title'		=> __( 'Price', FDM_TEXTDOMAIN ),
				'callback'	=> array( $this, 'show_item_price' ),
				'post_type'	=> 'fdm-menu-item',
				'context'	=> 'side',
				'priority'	=> 'default'
			);
		}

		// Create filter so addons can modify the metaboxes
		$meta_boxes = apply_filters( 'fdm_meta_boxes', $meta_boxes );

		// Create the metaboxes
		foreach ( $meta_boxes as $meta_box ) {
			add_meta_box(
				$meta_box['id'],
				$meta_box['title'],
				$meta_box['callback'],
				$meta_box['post_type'],
				$meta_box['context'],
				$meta_box['priority']
			);
		}

		// Remove Attributes metabox from menu organizer
		remove_meta_box( 'pageparentdiv', 'fdm-menu', 'side' );
	}


	/**
	 * Print the Menu Item price metabox HTML
	 * @since 1.0
	 */
	function show_item_price() {

		// Retrieve values for this if it exists
		global $post;
		$price = get_post_meta( $post->ID, 'fdm_item_price', true );

		?>

			<input type="hidden" name="fdm_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>">

			<div class="fdm-input-controls fdm-input-side-panel">
				<div class="fdm-input-control">
					<label for="fdm_item_price"><?php echo __( 'Price', FDM_TEXTDOMAIN ); ?></label>
					<input type="text" name="fdm_item_price" id="fdm_item_price" value="<?php echo esc_attr( $price ); ?>">
				</div>

				<?php do_action( 'fdm_show_item_price' ); ?>

			</div>

		<?php
	}

	/**
	 * Print the Menu organizer HTML
	 * @since 1.0
	 */
	function show_menu_organizer() {

		// Retrieve existing settings
		global $post;
		$column_one = get_post_meta( $post->ID, 'fdm_menu_column_one', true );
		$column_two = get_post_meta( $post->ID, 'fdm_menu_column_two', true );

		// Retrieve sections and store in HTML lists
		$terms = get_terms( 'fdm-menu-section', array( 'hide_empty' => false ) );
		$sections_list = '';
		foreach ( $terms as $term ) {
			$sections_list .= '<li><a href="#" data-termid="' . $term->term_id . '">' . $term->name . ' (' . $term->count . ')</a></li>';
		}

		?>

			<input type="hidden" name="fdm_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>">
			<input type="hidden" id="fdm_menu_column_one" name="fdm_menu_column_one" value="<?php echo $column_one; ?>">
			<input type="hidden" id="fdm_menu_column_two" name="fdm_menu_column_two" value="<?php echo $column_two; ?>">

			<p><?php echo __( 'Click on a Menu Section to add it to this menu.', FDM_TEXTDOMAIN ); ?></p>

			<div id="fdm-menu-organizer">

				<div id="fdm-menu-column-one">
					<div class="fdm-column fdm-options">
						<h4><?php echo __( 'Menu Sections', FDM_TEXTDOMAIN ); ?></h4>
						<ul>
							<?php echo $sections_list; ?>
						</ul>
					</div>
					<div class="fdm-column fdm-added">
						<h4><?php echo __( 'First Column', FDM_TEXTDOMAIN ); ?></h4>
						<ul>
						<!-- List filled with values on page load. see admin.js -->
						</ul>
					</div>
				</div>

				<div id="fdm-menu-column-two">
					<div class="fdm-column fdm-added">
						<h4><?php echo __( 'Second Column', FDM_TEXTDOMAIN ); ?></h4>
						<ul>
						<!-- List filled with values on page load. see admin.js -->
						</ul>
					</div>
					<div class="fdm-column fdm-options">
						<h4><?php echo __( 'Menu Sections', FDM_TEXTDOMAIN ); ?></h4>
						<ul>
							<?php echo $sections_list; ?>
						</ul>
					</div>
				</div>

				<div class="clearfix"></div>

			</div>

			<p><?php echo __( 'Hint: Leave the second column empty to display the menu in a single column.', FDM_TEXTDOMAIN ); ?></p>

		<?php

	}

	/**
	 * Print the menu shortcode HTML on the edit page for easy reference
	 * @since 1.0
	 */
	function show_menu_shortcode() {

		// Retrieve post ID
		global $post;

		// Check if menu is ready to be displayed
		$status = get_post_status( $post->ID );

		// Show advisory note when shortcode isn't ready
		if ( $status != 'publish' ) {

			?>

			<p><?php echo __( 'Once this menu is published, look here to find the shortcode you will use to display this menu in any post or page.', FDM_TEXTDOMAIN ); ?></p>

			<?php

		// Show the shortcode
		} else {

			?>

				<p><?php echo __( 'Copy and paste the snippet below into any post or page in order to display this menu.', FDM_TEXTDOMAIN ); ?></p>
				<div class="fdm-menu-shortcode">[fdm-menu id=<?php echo $post->ID; ?>]</div>

			<?php

		}

	}

	/**
	 * Save the metabox data from menu items and menus
	 * @since 1.0
	 */
	function save_meta( $post_id ) {

		// Verify nonce
		if ( !isset( $_POST['fdm_nonce'] ) || !wp_verify_nonce( $_POST['fdm_nonce'], basename( __FILE__ ) ) ) {
			return $post_id;
		}

		// Check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check permissions
		if ( !current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		} elseif ( !current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// Array of values to fetch and store
		$meta_ids = array();
		
		global $post;

		// Define Menu Item data
		if ( FDM_MENUITEM_POST_TYPE == $post->post_type ) {

			$meta_ids['fdm_item_price'] = 'sanitize_text_field';

		}

		// Define Menu organizer metadata
		if ( FDM_MENU_POST_TYPE == $post->post_type ) {

			$meta_ids['fdm_menu_column_one'] = 'sanitize_text_field';
			$meta_ids['fdm_menu_column_two'] = 'sanitize_text_field';

		}

		// Create filter so addons can add new data
		$meta_ids = apply_filters( 'fdm_save_meta', $meta_ids );

		// Save the metadata
		foreach ($meta_ids as $meta_id => $sanitize_callback) {
			$cur = get_post_meta( $post_id, $meta_id, true );
			$new = call_user_func( $sanitize_callback, $_POST[$meta_id] );
			if ( $new && $new != $cur ) {
				update_post_meta( $post_id, $meta_id, $new );
			} elseif ( $new == '' && $cur ) {
				delete_post_meta( $post_id, $meta_id, $cur );
			}
		}

	}

}