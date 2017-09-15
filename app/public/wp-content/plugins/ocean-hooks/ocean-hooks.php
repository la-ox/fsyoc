<?php
/**
 * Plugin Name:			Ocean Hooks
 * Plugin URI:			https://oceanwp.org/extension/ocean-hooks/
 * Description:			Add your custom content throughout various areas of OceanWP without using child theme.
 * Version:				1.0.0
 * Author:				OceanWP
 * Author URI:			https://oceanwp.org/
 * Requires at least:	4.0.0
 * Tested up to:		4.7
 *
 * Text Domain: ocean-hooks
 * Domain Path: /languages/
 *
 * @package Ocean_Hooks
 * @category Core
 * @author OceanWP
 * @see https://github.com/pojome/pojo-sidebars/
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the main instance of Ocean_Hooks to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Ocean_Hooks
 */
function Ocean_Hooks() {
	return Ocean_Hooks::instance();
} // End Ocean_Hooks()

Ocean_Hooks();

/**
 * Main Ocean_Hooks Class
 *
 * @class Ocean_Hooks
 * @version	1.0.0
 * @since 1.0.0
 * @package	Ocean_Hooks
 */
final class Ocean_Hooks {

	/**
	 * Ocean_Hooks The single instance of Ocean_Hooks.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct( $widget_areas = array() ) {
		$this->token 			= 'ocean-hooks';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0.0';

		define( 'OH_ROOT', dirname( __FILE__ ) );

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'oh_load_plugin_textdomain' ) );

		add_action( 'init', array( $this, 'oh_setup' ) );

		add_action( 'init', array( $this, 'oh_updater' ), 1 );

		add_action( 'init', array( $this, 'oh_output' ) );
	}

	/**
	 * Initialize License Updater.
	 * Load Updater initialize.
	 * @return void
	 */
	public function oh_updater() {

		// Plugin Updater Code
		if( class_exists( 'OceanWP_Plugin_Updater' ) ) {
			$license	= new OceanWP_Plugin_Updater( __FILE__, 'Ocean Hooks', $this->version, 'OceanWP' );
		}
	}

	/**
	 * Main Ocean_Hooks Instance
	 *
	 * Ensures only one instance of Ocean_Hooks is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Ocean_Hooks()
	 * @return Main Ocean_Hooks instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function oh_load_plugin_textdomain() {
		load_plugin_textdomain( 'ocean-hooks', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Installation.
	 * Runs on activation. Logs the version number and assigns a notice message to a WordPress option.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();
	}

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	}

	/**
	 * Setup all the things.
	 * Only executes if OceanWP or a child theme using OceanWP as a parent is active and the extension specific filter returns true.
	 * @return void
	 */
	public function oh_setup() {
		$theme = wp_get_theme();

		if ( 'OceanWP' == $theme->name || 'oceanwp' == $theme->template || 'ocean' == $theme->template ) {
			add_action( 'admin_menu', array( $this, 'add_page' ), 60 );
			add_action( 'admin_print_styles-theme-panel_page_oceanwp-panel-hooks', array( $this, 'scripts' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
		} else {
			add_action( 'admin_notices', array( $this, 'oh_install_ocean_notice' ) );
		}
	}

	/**
	 * OceanWP install
	 * If the user activates the plugin while having a different parent theme active, prompt them to install OceanWP.
	 * @since   1.0.0
	 * @return  void
	 */
	public function oh_install_ocean_notice() {
		echo '<div class="notice is-dismissible updated">
				<p>' . esc_html__( 'Ocean Hooks requires that you use OceanWP as your parent theme.', 'ocean-hooks' ) . ' <a href="https://oceanwp.org/">' . esc_html__( 'Install OceanWP Now', 'ocean-hooks' ) . '</a></p>
			</div>';
	}

	/**
	 * Add sub menu page
	 */
	public function add_page() {
		add_submenu_page(
			'oceanwp-panel',
			esc_html__( 'Hooks', 'ocean-hooks' ),
			esc_html__( 'Hooks', 'ocean-hooks' ),
			'manage_options',
			'oceanwp-panel-hooks',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Load scripts
	 */
	public static function scripts() {

		// Enqueue the select2 script from theme.
		wp_enqueue_script( 'oceanwp-select2', OCEANWP_INC_DIR_URI . 'customizer/controls/typography/select2.min.js', array( 'jquery' ), false, true );

		// Enqueue the select2 style from theme.
		wp_enqueue_style( 'select2-css', OCEANWP_INC_DIR_URI . 'customizer/controls/typography/select2.min.css', null );

		// Enqueue the cookie script from theme.
		wp_enqueue_script( 'cookie', OCEANWP_JS_DIR_URI . 'devs/cookie.min.js', array( 'jquery' ), OCEANWP_THEME_VERSION, true );

		// Main script
		wp_enqueue_script( 'oh-main-script', plugins_url( '/assets/js/hooks.min.js', __FILE__ ), array( 'jquery' ), OCEANWP_THEME_VERSION, true );

		// Main CSS
		wp_enqueue_style( 'oh-main', plugins_url( '/assets/css/hooks.min.css', __FILE__ ) );

	}

	/**
	 * Return hooks
	 */
	private static function get_hooks() {
		
		$hooks = array(
			'oh_wp_head' => array(
				'label' => esc_html__( 'WP Head', 'ocean-hooks' ),
				'hook' 	=> 'wp_head',
			),
			'oh_before_top_bar' => array(
				'label' => esc_html__( 'Before Top Bar', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_top_bar',
			),
			'oh_before_top_bar_inner' => array(
				'label' => esc_html__( 'Before Top Bar Inner', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_top_bar_inner',
			),
			'oh_after_top_bar_inner' => array(
				'label' => esc_html__( 'After Top Bar Inner', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_top_bar_inner',
			),
			'oh_after_top_bar' => array(
				'label' => esc_html__( 'After Top Bar', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_top_bar',
			),
			'oh_before_header' => array(
				'label' => esc_html__( 'Before Header', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_header',
			),
			'oh_before_header_inner' => array(
				'label' => esc_html__( 'Before Header Inner', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_header_inner',
			),
			'oh_before_logo' => array(
				'label' => esc_html__( 'Before Logo', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_logo',
			),
			'oh_before_logo_inner' => array(
				'label' => esc_html__( 'Before Logo Inner', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_logo_inner',
			),
			'oh_after_logo_inner' => array(
				'label' => esc_html__( 'After Logo Inner', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_logo_inner',
			),
			'oh_after_logo' => array(
				'label' => esc_html__( 'After Logo', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_logo',
			),
			'oh_before_nav' => array(
				'label' => esc_html__( 'Before Navigation', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_nav',
			),
			'oh_before_nav_inner' => array(
				'label' => esc_html__( 'Before Navigation Inner', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_nav_inner',
			),
			'oh_after_nav_inner' => array(
				'label' => esc_html__( 'After Navigation Inner', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_nav_inner',
			),
			'oh_after_nav' => array(
				'label' => esc_html__( 'After Navigation', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_nav',
			),
			'oh_after_header_inner' => array(
				'label' => esc_html__( 'After Header Inner', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_header_inner',
			),
			'oh_after_header' => array(
				'label' => esc_html__( 'After Header', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_header',
			),
			'oh_before_page_header' => array(
				'label' => esc_html__( 'Before Page Header', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_page_header',
			),
			'oh_before_page_header_inner' => array(
				'label' => esc_html__( 'Before Page Header Inner', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_page_header_inner',
			),
			'oh_after_page_header_inner' => array(
				'label' => esc_html__( 'After Page Header Inner', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_page_header_inner',
			),
			'oh_after_page_header' => array(
				'label' => esc_html__( 'After Page Header', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_page_header',
			),
			'oh_before_outer_wrap' => array(
				'label' => esc_html__( 'Before Outer Wrap Content', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_outer_wrap',
			),
			'oh_before_wrap' => array(
				'label' => esc_html__( 'Before Wrap Content', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_wrap',
			),
			'oh_before_main' => array(
				'label' => esc_html__( 'Before Main Content', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_main',
			),
			'oh_before_content_wrap' => array(
				'label' => esc_html__( 'Before Content Wrap', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_content_wrap',
			),
			'oh_before_primary' => array(
				'label' => esc_html__( 'Before Primary Content', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_primary',
			),
			'oh_before_content' => array(
				'label' => esc_html__( 'Before Content', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_content',
			),
			'oh_before_content_inner' => array(
				'label' => esc_html__( 'Before Content Inner', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_content_inner',
			),
			'oh_after_content_inner' => array(
				'label' => esc_html__( 'After Content Inner', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_content_inner',
			),
			'oh_after_content' => array(
				'label' => esc_html__( 'After Content', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_content',
			),
			'oh_after_primary' => array(
				'label' => esc_html__( 'After Primary Content', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_primary',
			),
			'oh_before_sidebar' => array(
				'label' => esc_html__( 'Before Sidebar', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_sidebar',
			),
			'oh_before_sidebar_inner' => array(
				'label' => esc_html__( 'Before Sidebar Inner', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_sidebar_inner',
			),
			'oh_after_sidebar_inner' => array(
				'label' => esc_html__( 'After Sidebar Inner', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_sidebar_inner',
			),
			'oh_after_sidebar' => array(
				'label' => esc_html__( 'After Sidebar', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_sidebar',
			),
			'oh_after_content_wrap' => array(
				'label' => esc_html__( 'After Content Wrap', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_content_wrap',
			),
			'oh_after_main' => array(
				'label' => esc_html__( 'After Main Content', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_main',
			),
			'oh_after_wrap' => array(
				'label' => esc_html__( 'After Wrap Content', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_wrap',
			),
			'oh_after_outer_wrap' => array(
				'label' => esc_html__( 'After Outer Wrap Content', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_outer_wrap',
			),
			'oh_before_footer' => array(
				'label' => esc_html__( 'Before Footer', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_footer',
			),
			'oh_before_footer_inner' => array(
				'label' => esc_html__( 'Before Footer Inner', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_footer_inner',
			),
			'oh_before_footer_widgets' => array(
				'label' => esc_html__( 'Before Footer Widgets', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_footer_widgets',
			),
			'oh_before_footer_widgets_inner' => array(
				'label' => esc_html__( 'Before Footer Widgets Inner', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_footer_widgets_inner',
			),
			'oh_after_footer_widgets_inner' => array(
				'label' => esc_html__( 'After Footer Widgets Inner', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_footer_widgets_inner',
			),
			'oh_after_footer_widgets' => array(
				'label' => esc_html__( 'After Footer Widgets', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_footer_widgets',
			),
			'oh_before_footer_bottom' => array(
				'label' => esc_html__( 'Before Footer Bottom', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_footer_bottom',
			),
			'oh_before_footer_bottom_inner' => array(
				'label' => esc_html__( 'Before Footer Bottom Inner', 'ocean-hooks' ),
				'hook' 	=> 'ocean_before_footer_bottom_inner',
			),
			'oh_after_footer_bottom_inner' => array(
				'label' => esc_html__( 'After Footer Bottom Inner', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_footer_bottom_inner',
			),
			'oh_after_footer_bottom' => array(
				'label' => esc_html__( 'After Footer Bottom', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_footer_bottom',
			),
			'oh_after_footer_inner' => array(
				'label' => esc_html__( 'After Footer Inner', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_footer_inner',
			),
			'oh_after_footer' => array(
				'label' => esc_html__( 'After Footer', 'ocean-hooks' ),
				'hook' 	=> 'ocean_after_footer',
			),
			'oh_wp_footer' => array(
				'label' => esc_html__( 'WP Footer', 'ocean-hooks' ),
				'hook' 	=> 'wp_footer',
			),
		);

		// Apply filters and return
		return apply_filters( 'oh_hooks_fields', $hooks );

	}

	/**
	 * Register sanitization callback.
	 */
	public function register_settings() {
		register_setting( 'oh_hooks_settings', 'oh_hooks_settings', array( $this, 'admin_sanitize' ) );
	}

	/**
	 * Sanitization callback
	 */
	public function admin_sanitize( $options ) {

		if ( ! empty( $options ) ) {

			// Loop through options and save them
			foreach ( $options as $key => $val ) {

				// Delete data if empty
				if ( empty( $val['data'] ) ) {
					unset( $options[$key] );
				}

				// Validate settings
				else {

					if ( ! empty( $val['priority'] ) ) {
						$options[$key]['priority'] = intval( $val['priority'] );
					}

					if ( isset( $val['php'] ) ) {
						$options[$key]['php'] = true;
					}


				}

			}

			return $options;

		}

	}

	/**
	 * Settings page output
	 */
	public function create_admin_page() { ?>

		<div  id="oh-hooks" class="wrap">

			<div id="poststuff">

				<div id="post-body" class="metabox-holder columns-2">

					<form method="post" action="options.php">

						<?php settings_fields( 'oh_hooks_settings' ); ?>

						<?php $options = get_option( 'oh_hooks_settings' ); ?>

						<div id="poststuff" class="clr">

							<div id="post-body-content">

								<div id="post-body-content" class="postbox-container clr">

									<table class="form-table">

										<tbody>

											<?php
											// Get hooks
											$hooks = $this->get_hooks();

											// Loop through sections
											foreach ( $hooks as $section ) {

												$hook = $section['hook'];

												// Get data
												$data   	= ! empty( $options[$hook]['data'] ) ? $options[$hook]['data'] : '';
												$priority 	= isset( $options[$hook]['priority'] ) ? intval( $options[$hook]['priority'] ) : 10;
												$php 		= isset( $options[$hook]['php'] ) ? true : false; ?>

												<tr>

													<th scope="row"><?php echo esc_attr( $section['label'] ); ?></th>

													<td>

														<textarea name="oh_hooks_settings[<?php echo esc_attr( $hook ); ?>][data]" rows="10" cols="50"><?php echo esc_textarea( $data ); ?></textarea>

														<div class="priority">
															<label for="oh_hooks_settings[<?php echo esc_attr( $hook ); ?>][priority]"><?php esc_attr_e( 'Priority', 'ocean-hooks' ); ?></label>
															<input type="number" name="oh_hooks_settings[<?php echo esc_attr( $hook ); ?>][priority]" id="oh_hooks_settings[<?php echo esc_attr( $hook ); ?>][priority]" value="<?php echo esc_attr( $priority ); ?>" />
														</div>

														<div class="enable">
															<input id="oh_hooks_settings[<?php echo esc_attr( $hook ); ?>][php]" name="oh_hooks_settings[<?php echo esc_attr( $hook ); ?>][php]" type="checkbox" value="<?php echo esc_attr( $php ); ?>" <?php checked( $php, true ); ?>>
															<label for="oh_hooks_settings[<?php echo esc_attr( $hook ); ?>][php]"><?php esc_html_e( 'Enable PHP', 'ocean-hooks' ); ?></label>
														</div>

													</td>

												</tr>

											<?php
											} ?>

										</tbody>

									</table>

								</div><!-- #post-body-content -->

								<div id="postbox-container-1" class="clr">

									<div class="postbox hooks-box">

										<h3 class="hndle"><?php esc_html_e( 'OceanWP Hooks', 'ocean-hooks' ); ?></h3>

										<div class="inside">

											<p class="text"><?php esc_html_e( 'Use these fields to insert anything you like throughout OceanWP. Shortcodes are allowed, and PHP if you check the Enable PHP checkboxes.', 'ocean-hooks' ); ?></p>

											<select id="hook-select">
												<option value="all"><?php esc_html_e( 'Show all', 'ocean-hooks' ); ?></option>
												<?php
												// Get hooks
												$hooks = $this->get_hooks();

												$count = 0;

												// Loop through sections
												foreach ( $hooks as $section ) {

													$hook = $section['hook']; ?>

													<option id="<?php echo esc_attr( $count++ ); ?>"><?php echo esc_attr( $section['label'] ); ?></option>

												<?php
												} ?>
											</select>

											<p class="submit">
												<input name="submit" type="submit" class="button-primary" value="<?php esc_html_e( 'Save Hooks', 'ocean-hooks' ); ?>">
											</p>

										</div>

									</div>

								</div>

							</div>

						</div>

					</form>

				</div>

			</div>

		</div><!-- .wrap -->

	<?php
	}

	/**
	 * Outputs code on the front-end
	 */
	public function oh_output() {

		// Get hooks
		$hooks = get_option( 'oh_hooks_settings' );

		// Return if hooks are empty
		if ( is_admin()
			|| empty( $hooks ) ) {
			return;
		}

		// Loop through options
		foreach ( $hooks as $key => $val ) {
			if ( ! empty( $val['data'] ) ) {
				$priority = isset( $val['priority'] ) ? intval( $val['priority'] ) : 10;
				add_action( $key, array( $this, 'get_data' ), $priority );
			}
		}

	}

	/**
	 * Used to get the data
	 */
	public function get_data() {

		// Set main vars
		$hook    	= current_filter();
		$option 	= get_option( 'oh_hooks_settings' );
		$php 		= ! empty( $option[$hook]['php'] ) ? true : false;
		$output  	= $option[$hook]['data'];

		// Output
		if ( $output ) {
			if ( $php ) {
				eval( "?>$output<?php " );
			} else {
				echo do_shortcode( $output );
			}
		}

	}

} // End Class