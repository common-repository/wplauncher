<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.wplauncher.com
 * @since      1.0.0
 *
 * @package    Wplauncher
 * @subpackage Wplauncher/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wplauncher
 * @subpackage Wplauncher/admin
 * @author     Ben Shadle <benshadle@gmail.com>
 */
class Wplauncher_Admin {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action('init', array( $this, 'wpl_register_custom_post_types' ));
		add_action('add_meta_boxes_wplauncher_requests', array( $this, 'wpl_setup_request_metaboxes' ));
		add_action( 'save_post_wplauncher_requests',  array( $this, 'wpl_requests_save_meta_box_data') );
		// Add Settings Page to Sidebar
			// setting the priority to 9 or less allows us to move the post types to the bottom of the submenu
		add_action('admin_menu', array( $this, 'wpl_add_plugin_admin_menu' ), 9);
	    // Register Settings
	    add_action('admin_init', array( $this, 'wpl_register_and_build_fields' ));
		add_filter( 'post_updated_messages', array($this,'wpl_updated_messages') );
		
		// UPdate the columns shown on hte products edit.php file - so we also have cost, inventory and product id
		add_filter('manage_wplauncher_requests_posts_columns' , array($this,'wpl_requests_columns'));
		// this fills in the columns that were created with each individual post's value
		add_action( 'manage_wplauncher_requests_posts_custom_column' , array($this,'wpl_fill_requests_columns'), 10, 2 );
		
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wplauncher_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wplauncher_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wplauncher-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wplauncher_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wplauncher_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wplauncher-admin.js', array( 'jquery' ), $this->version, false );

	}
	/**
	 * Register Custom Post Types
	 *
	 * @since    1.0.0
	 */
	public function wpl_register_custom_post_types(){
		$requestArgs = array(
			'label'=>'WPLauncher Requests',
			'labels'=>
				array(
					'name'=>'Requests',
					'singular_name'=>'Request',
					'add_new'=>'Add Request',
					'add_new_item'=>'Add New Request',
					'edit_item'=>'Edit Request',
					'new_item'=>'New Request',
					'view_item'=>'View Request',
					'search_items'=>'Search Requests',
					'not_found'=>'No Requests Found',
					'not_found_in_trash'=>'No Requests Found in Trash'
				), 
			'public'=>false, 
			'description'=>'WPLauncher Requests',
			'exclude_from_search'=>true,
			'show_ui'=>true,
			'show_in_menu'=>$this->plugin_name,
			'supports'=>array('title', 'thumbnail', 'custom_fields'),
			'taxonomies'=>array('category','post_tag'));
		// Post type, $args - the Post Type string can be MAX 20 characters
		register_post_type( 'wplauncher_requests', $requestArgs );
	}
    public function wpl_add_plugin_admin_menu() {
      /*
       * Add a settings page for this plugin to the Settings menu.
       */
      /*add_options_page(
        __( 'WPLauncher Stripe Payments Settings', $this->plugin_name ),
        __( 'WPLauncher Stripe Payments', $this->plugin_name ),
        'manage_options',
        $this->plugin_name,
        array( $this, 'display_plugin_admin_page' )
      );*/
	  /**
add_menu_page('My Custom Page', 'My Custom Page', 'manage_options', 'my-top-level-slug');

add_submenu_page( 'my-top-level-slug', 'My Custom Page', 'My Custom Page', 'manage_options', 'my-top-level-slug');

add_submenu_page( 'my-top-level-slug', 'My Custom Submenu Page', 'My Custom Submenu Page', 'manage_options', 'my-secondary-slug');
	  **/
	  //add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
	  add_menu_page( 'WPLauncher', 'WPLauncher', 'administrator', $this->plugin_name, array( $this, 'wpl_display_plugin_admin_dashboard' ), plugin_dir_url( __FILE__ ) . 'img/logo.svg', 26 );
      // this call removes the duplicate link at the top of the submenu 
	  	// bc you're giving the parent slug and menu slug the same values
	  //add_submenu_page( $this->plugin_name, 'wplauncher Dashboard', 'Dashboard', 'administrator', $this->plugin_name);
	  add_submenu_page( $this->plugin_name, 'WPLauncher Settings', 'Settings', 'administrator', $this->plugin_name.'-settings', array( $this, 'wpl_display_plugin_admin_settings' ));
	  //add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
  	  
	  /*add_submenu_page( $this->plugin_name, 'Products', 'Products', 'administrator', $this->plugin_name.'-products', array( $this, 'display_plugin_products_page' ));
	  add_submenu_page( $this->plugin_name, 'Plans', 'Plans', 'administrator', $this->plugin_name.'-plans', array( $this, 'display_plugin_plans_page' ));*/
	  
    }
	public function wpl_setup_request_metaboxes(){
		
		add_meta_box( 'request_data_meta_box', 'Product Data', array($this,'wpl_request_data_meta_box'), 'wplauncher_requests', 'normal','high' );
		// add_meta_box( $this->plugin_name.'_product_description_meta_box', 'Product Description', array($this,$this->plugin_name.'_product_description_meta_box'), 'wplauncher_products', 'normal','core' );
// 		add_meta_box( $this->plugin_name.'_product_button_shortcode_meta_box', 'Buy Button Shortcode', array($this,$this->plugin_name.'_product_button_shortcode_meta_box'), 'wplauncher_products', 'normal','core' );
		
		/*add_meta_box( $this->plugin_name.'_inventory', 'Product Inventory', array($this,$this->plugin_name.'_product_inventory_meta_box'), 'wplauncher_products', 'normal','high' );*/
	}
	/**
	 * When the post is saved, saves our custom data.
	 *
	 * @since    1.0.0
	 */
	function wpl_requests_save_meta_box_data( $post_id ) {
		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */
		
		// Check if our nonce is set.
		if ( ! isset( $_POST[$this->plugin_name.'_meta_box_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST[$this->plugin_name.'_meta_box_nonce'], $this->plugin_name.'_meta_box' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		/* OK, it's safe for us to save the data now. */
	
		// Make sure that it is set.
		if ( ! isset( $_POST[$this->plugin_name.'_cost'] ) && ! isset( $_POST[$this->plugin_name.'_description'] ) && ! isset( $_POST[$this->plugin_name.'_hourly_estimate'] ) && ! isset( $_POST[$this->plugin_name.'_cost_estimate'] ) && ! isset( $_POST[$this->plugin_name.'_budget'] ) && ! isset( $_POST[$this->plugin_name.'_response'] ) && ! isset( $_POST[$this->plugin_name.'_status'] )) {
			return;
		}

		// Sanitize user input.
		$request_budget = sanitize_text_field( $_POST[$this->plugin_name.'_budget'] );
		$request_cost = sanitize_text_field( $_POST[$this->plugin_name.'_cost'] );
		$request_hourly_estimate = sanitize_text_field( $_POST[$this->plugin_name.'_hourly_estimate'] );
		$request_description = wp_kses_post( $_POST[$this->plugin_name.'_description'] );
		$request_response = wp_kses_post( $_POST[$this->plugin_name.'_response'] );
		$request_cost_estimate = sanitize_text_field( $_POST[$this->plugin_name.'_cost_estimate'] );
		$request_status = sanitize_text_field( $_POST[$this->plugin_name.'_status'] );
				
		// Update the meta field in the database.
		update_post_meta( $post_id, $this->plugin_name.'_description', $request_description );
		update_post_meta( $post_id, $this->plugin_name.'_cost', $request_cost );
		update_post_meta( $post_id, $this->plugin_name.'_budget', $request_budget );
		update_post_meta( $post_id, $this->plugin_name.'_hourly_estimate', $request_hourly_estimate );
		update_post_meta( $post_id, $this->plugin_name.'_cost_estimate', $request_cost_estimate );
		update_post_meta( $post_id, $this->plugin_name.'_status', $request_status );
		update_post_meta( $post_id, $this->plugin_name.'_response', $request_response );
		
		// send this info to us, BUT If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) ){
			return;
		}

		$post_title = get_the_title( $post_id );
		$to = 'benshadle@gmail.com';
		$subject = 'WPLAUNCHER PLUGIN DEVELOPMENT REQUEST: '.$post_title;
		$body = '<p>BUDGET:</p><p>'.$request_budget.'</p><p>DESCRIPTION:</p><p>'.$request_description.'</p>';
		$headers = array('Content-Type: text/html; charset=UTF-8');
 
		wp_mail( $to, $subject, $body, $headers );
		
	}
	/**
	 * Product Data Meta Box Callback
	 * 
	 * @since    1.0.0
	 */
	function wpl_request_data_meta_box( $post ) {

		// Add a nonce field so we can check for it later.
		wp_nonce_field( $this->plugin_name.'_meta_box', $this->plugin_name.'_meta_box_nonce' );
	
		/*
		 * Use get_post_meta() to retrieve an existing value
		 * from the database and use the value for the form.
		 */
		//$description = get_post_meta( $post->ID, $this->plugin_name.'_description', true );
		unset($args);
		$args = array('dashicon'=>'dashicons-admin-home','class'=>'general_tab active', 'tab_text'=>'General','link_href'=>'#general_request_fields','link_title'=>'General');
		$general_tab_id = 'general_request_fields';
		$general_tab = $this->wpl_getPostMetaTab($args);
		unset($args);
		$args = array('dashicon'=>'dashicons-format-status','class'=>'conversation_tab', 'tab_text'=>'Conversation','link_href'=>'#conversation_request_fields','link_title'=>'Conversation');
		$conversation_tab = $this->wpl_getPostMetaTab($args);
		$conversation_tab_id = 'conversation_request_fields';
		unset($args);
		$args = array('dashicon'=>'dashicons-welcome-write-blog','class'=>'estimate_tab', 'tab_text'=>'Estimate','link_href'=>'#estimate_request_fields','link_title'=>'Estimate');
		$estimate_tab = $this->wpl_getPostMetaTab($args);
		$estimate_tab_id = 'estimate_request_fields';
		unset($args);
		$args = array('dashicon'=>'dashicons-flag','class'=>'final_tab', 'tab_text'=>'Final','link_href'=>'#final_request_fields','link_title'=>'Final');
		$final_tab = $this->wpl_getPostMetaTab($args);
		$final_tab_id = 'final_request_fields';
		echo '<div class="product_container">
				<ul class="product_container_tabs wpl_tabs">'.$general_tab.$conversation_tab.$estimate_tab.$final_tab.'</ul>
				<!--Display block if general product fields is clicked otherwise hide-->
				<div id="'.$general_tab_id.'" class="wpl_show tab_content"><div class="product_field_containers">';
		echo '<ul class="wplauncher_product_data_metabox">';
		
		echo '<li><label for="'.$this->plugin_name.'_status">';
		_e( 'Status', $this->plugin_name.'_status' );
		echo '</label>';
		$currency1 = get_option( $this->plugin_name.'_currency' );
		$currency = $this->wpl_get_currency_details($currency1);
		if($currency['symbol']){
			$currencyPrepend = $currency['symbol'];
		} else {
			$currencyPrepend = $currency['value'];
		}
		unset($args);
	  	$args = array (
	              'type'      => 'select',
				  'subtype'	  => '',
				  'id'	  => $this->plugin_name.'_status',
				  'name'	  => $this->plugin_name.'_status',
				  'required' => '',
				  'get_options_list' => 'wpl_get_dev_status_list',
				  'value_type'=>'normal',
				  'wp_data' => 'post_meta',
				  'post_id'=> $post->ID
	          );
		// this gets the post_meta value and echos back the input
		$this->wpl_render_settings_field($args);
		echo '</li><li><label for="'.$this->plugin_name.'_budget">';
		_e( 'Budget', $this->plugin_name.'_budget' );
		echo '</label>';
		unset($args);
		$args = array (
	              'type'      => 'input',
				  'subtype'	  => 'number',
				  'id'	  => $this->plugin_name.'_budget',
				  'name'	  => $this->plugin_name.'_budget',
				  'required' => 'required="required"',
				  'get_options_list' => '',
				  'value_type'=>'normal',
				  'wp_data' => 'post_meta',
				  'post_id'=> $post->ID,
				  'min'=> '0',
				  'step'=> 'any',
				  'prepend_value'=>$currencyPrepend
	          );
		// this gets the post_meta value and echos back the input
		$this->wpl_render_settings_field($args);
		echo '</li>';
		$description= get_post_meta( $post->ID, $this->plugin_name.'_description', true );
				// DON'T WANT APPLY FILTERS BECAUSE WE WANT SAVED SHORTCODES TO BE SHOWN - IT WON'T BE IF THE CONTENT IS RUN THROUGH THIS
		//$sold_out_message = apply_filters('the_content', $sold_out_message); 
		$args = array(
		'textarea_name' => $this->plugin_name.'_description',
		); 
		echo '<li><label for="'.$this->plugin_name.'_description">';
				_e( 'Description', $this->plugin_name.'_description' );
				echo '</label>';
		wp_editor( $description, 'wplauncher_description',$args); 
		echo '</li>';
		echo '</ul></div></div>';
		echo '<div id="'.$conversation_tab_id.'" class="wpl_hide tab_content"><div class="product_field_containers"><ul class="wplauncher_product_data_metabox">';
		$response= get_post_meta( $post->ID, $this->plugin_name.'_response', true );
				// DON'T WANT APPLY FILTERS BECAUSE WE WANT SAVED SHORTCODES TO BE SHOWN - IT WON'T BE IF THE CONTENT IS RUN THROUGH THIS
		//$sold_out_message = apply_filters('the_content', $sold_out_message); 
		$args = array(
		'textarea_name' => $this->plugin_name.'_response',
		); 
		echo '<li><label for="'.$this->plugin_name.'_response">';
				_e( 'Response', $this->plugin_name.'_response' );
				echo '</label>';
		wp_editor( $response, 'wplauncher_response',$args);
		echo '</li></ul></div></div>';
		echo '<div id="'.$estimate_tab_id.'" class="wpl_hide tab_content"><div class="product_field_containers"><ul class="wplauncher_product_data_metabox">';
		echo '<li><label for="'.$this->plugin_name.'_cost_estimate">';
				_e( 'Cost Estimate', $this->plugin_name.'_cost_estimate' );
				echo '</label>';
				unset($args);
			  		$args = array (
			  	              'type'      => 'input',
			  				  'subtype'	  => 'number',
			  				  'id'	  => $this->plugin_name.'_cost_estimate',
			  				  'name'	  => $this->plugin_name.'_cost_estimate',
			  				  'required' => '',
			  				  'get_options_list' => '',
			  				  'value_type'=>'normal',
			  				  'wp_data' => 'post_meta',
			  				  'post_id'=> $post->ID,
			  				  'min'=> '0',
			  				  'step'=> 'any',
			  				  'prepend_value'=>$currencyPrepend
			  	          );
				// this gets the post_meta value and echos back the input
				$this->wpl_render_settings_field($args);
				echo '</li><li><label for="'.$this->plugin_name.'_hourly_estimate">';
						_e( 'Hourly Estimate', $this->plugin_name.'_hourly_estimate' );
						echo '</label>';
						unset($args);
					  	$args = array (
					              'type'      => 'input',
								  'subtype'	  => 'text',
								  'id'	  => $this->plugin_name.'_hourly_estimate',
								  'name'	  => $this->plugin_name.'_hourly_estimate',
								  'required' => '',
								  'get_options_list' => '',
								  'value_type'=>'normal',
								  'wp_data' => 'post_meta',
								  'post_id'=> $post->ID
					          );
						// this gets the post_meta value and echos back the input
						$this->wpl_render_settings_field($args);
						echo '</li></ul></div></div>';
						echo '<div id="'.$final_tab_id.'" class="wpl_hide tab_content"><div class="product_field_containers"><ul class="wplauncher_product_data_metabox">';
						echo '<li><label for="'.$this->plugin_name.'_cost">';
								_e( 'Cost', $this->plugin_name.'_cost' );
								echo '</label>';
								unset($args);
							  		$args = array (
							  	              'type'      => 'input',
							  				  'subtype'	  => 'number',
							  				  'id'	  => $this->plugin_name.'_cost',
							  				  'name'	  => $this->plugin_name.'_cost',
							  				  'required' => '',
							  				  'get_options_list' => '',
							  				  'value_type'=>'normal',
							  				  'wp_data' => 'post_meta',
							  				  'post_id'=> $post->ID,
							  				  'min'=> '0',
							  				  'step'=> 'any',
							  				  'prepend_value'=>$currencyPrepend
							  	          );
								// this gets the post_meta value and echos back the input
								$this->wpl_render_settings_field($args);
				echo '</li></ul></div></div><div class="clear"></div></div>';
	}
	/**
	 * Render Post Meta Tab
	 *
	 * @since    1.0.0
	 */
	public function wpl_getPostMetaTab($args){
		return '<li class="'.$args['class'].'">
			<a title="'.$args['link_title'].'" data-href="'.$args['link_href'].'">
				<div>
					<span class="dashicons '.$args['dashicon'].'"></span><span class="wpmTabText">'.$args['tab_text'].'</span>
				</div>
			</a>
		</li>';
	}
	/**
	* Add Custom Columns to edit.php page for wplauncher_products
	 * 
	 * @since    1.0.0
	*/
	function wpl_requests_columns($columns) {
		// Remove Author and Comments from Columns and Add Cost, Inventory and Product Id
		unset(
			$columns['wpseo-score'],
			$columns['wpseo-title'],
			$columns['wpseo-metadesc'],
			$columns['wpseo-focuskw']
		);
		return array(
	           'cb' => '<input type="checkbox" />',
	           'title' => __('Request'),
	           'cost' => __('Cost'),
	           'budget' => __('Budget'),
	           'status' =>__( 'Status'),
			   'request_id' =>__( 'Request ID'),
			   'date' =>__( 'Date')
	       );
	    //return $columns;
	}
	/**
	*
	* Fill in Custom Columns
	* @since 1.0.0
	*/
	public function wpl_fill_requests_columns( $column, $post_id ) {
	    // Fill in the columns with meta box info associated with each post
		$currency1 = get_option( $this->plugin_name.'_currency' );
		$currency = $this->wpl_get_currency_details($currency1);
		$currencyPrepend = ($currency['symbol']) ? $currency['symbol'] : $currency['value'];
		switch ( $column ) {
		case 'cost' :
			$cost = get_post_meta( $post_id , $this->plugin_name.'_cost' , true );
			if($cost){
				echo $currencyPrepend.$cost;
			} else {
				echo '';
			}
			break;
			case 'budget' :
				$budget = get_post_meta( $post_id , $this->plugin_name.'_budget' , true );
				if($budget){
					echo $currencyPrepend.$budget;
				} else {
					echo '';
				}
				break;
		case 'status' :
			$raw_value = get_post_meta( $post_id , $this->plugin_name.'_status' , true ); 
			$wp_data_list = $this->wpl_get_dev_status_list();
			//$wp_data_list = $this->$args['get_options_list']($args);
			foreach($wp_data_list AS $o){
				if($o['value'] == $raw_value){
					$value = $o['name'];				
				}
			}
		    echo $value;
		    break;
		case 'request_id' :
		    echo $post_id; 
		    break;
	    }
	}
  	/**
  	 * View for Admin Page
  	 *
  	 * @since    1.0.0
  	 */
    public function wpl_display_plugin_admin_dashboard() {
		require_once 'partials/wplauncher-admin-display.php';
    
    }
	public function wpl_updated_messages($messages){
		$post             = get_post();
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );
		$post_types[] = array('id'=>'wplauncher_requests','singular'=>'Request');
		
		foreach($post_types AS $p){
			$messages[$p['id']] = array(
				0  => '', // Unused. Messages start at index 1.
				1  => __( $p['singular'].' updated.'),
				2  => __( 'Custom field updated.'),
				3  => __( 'Custom field deleted.'),
				4  => __( $p['singular'].' updated.'),
				/* translators: %s: date and time of the revision */
				5  => isset( $_GET['revision'] ) ? sprintf( __( $p['singular'].' restored to revision from %s', 'your-plugin-textdomain' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
				6  => __( $p['singular'].' published.'),
				7  => __( $p['singular'].' saved.'),
				8  => __( $p['singular'].' submitted.'),
				9  => sprintf(
					__( $p['singular'].' scheduled for: <strong>%1$s</strong>.', 'wplauncher' ),
					// translators: Publish box date format, see http://php.net/date
					date_i18n( __( 'M j, Y @ G:i', 'your-plugin-textdomain' ), strtotime( $post->post_date ) )
				),
				10 => __( $p['singular'].' draft updated.', 'your-plugin-textdomain' )
			);
			// plans isn't here because it's not publicly searchable
			if ( $p['id'] == 'wplauncher_requests' ) {
				$permalink = get_permalink( $post->ID );

				$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View '.$p['singular'], 'your-plugin-textdomain' ) );
				$messages[ $p['id'] ][1] .= $view_link;
				$messages[ $p['id'] ][6] .= $view_link;
				$messages[ $p['id'] ][9] .= $view_link;

				$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
				$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview '.$p['singular'], 'your-plugin-textdomain' ) );
				$messages[ $p['id'] ][8]  .= $preview_link;
				$messages[ $p['id'] ][10] .= $preview_link;
			}
		}
		return $messages;
	}
	/**
	* Get STock Status List for the Post Meta Product function
	* @since 1.0.2
	*/
	public function wpl_get_dev_status_list(){
		$devStatusList = array(		
							  0 => array('value'=> '0', 'name' => 'Awaiting Estimate'),	  
							  1 => array('value'=> '1', 'name' => 'Awaiting Customer Approval'),	  
							  2 => array('value'=> '2', 'name' => 'Build In Progress'),
							  3 => array('value'=> '3', 'name' => 'Changes In Progress'),
							  4 => array('value'=> '4', 'name' => 'Completed'),
						);
	  return $devStatusList;
	}
  	/**
  	 * View for AdminSettings Page
  	 *
  	 * @since    1.0.0
  	 */
    public function wpl_display_plugin_admin_settings() {
		// set this var to be used in the settings-display view
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';
	    if(isset($_GET['error_message'])){
			// to display an error - add the admin_notices action
			// run do_action to pass an argument to the admin notices callback function
			// in the callback array run add_settings_error
			add_action('admin_notices', array($this,'wpl_settings_messages'));
			do_action( 'admin_notices', $_GET['error_message'] );
	    }
		require_once 'partials/wplauncher-admin-settings-display.php';
    }
	
	
	/**
	OPTION PAGE FUNCTIONALITY
	**/
	/**
     * Register the Stripe account section Fields, Stripe API Secret and Public Key fields etc
     * 
     *
     * @since     1.0.0
     */
    public function wpl_register_and_build_fields() {
		/**
	   * First, we add_settings_section. This is necessary since all future settings must belong to one.
	   * Second, add_settings_field
	   * Third, register_setting
	   */ 
		
		
		add_settings_section(
		  // ID used to identify this section and with which to register options
		  'wplauncher_general_section', 
		  // Title to be displayed on the administration page
		  '',  
		  // Callback used to render the description of the section
		   array( $this, 'wplauncher_display_general_account' ),    
		  // Page on which to add this section of options
		  $this->plugin_name.'_general_settings'                   
		);
		unset($args);
	  	$args = array (
	              'type'      => 'input',
				  'subtype'	  => 'text',
				  'id'	  => $this->plugin_name.'_company_name',
				  'name'	  => $this->plugin_name.'_company_name',
				  'required' => '',
				  'get_options_list' => '',
				  'value_type'=>'normal',
				  'wp_data' => 'option'
	          );
		add_settings_field(
		  'wplauncher_company_name',
		  'Company Name',
		  array( $this, 'wpl_render_settings_field' ),
		  $this->plugin_name.'_general_settings',
		  'wplauncher_general_section',
		  $args
		);
		unset($args);
	  	$args = array (
	              'type'      => 'input',
				  'subtype'	  => 'text',
				  'id'	  => $this->plugin_name.'_logo',
				  'name'	  => $this->plugin_name.'_logo',
				  'required' => '',
				  'get_options_list' => '',
				  'value_type'=>'normal',
				  'wp_data' => 'option'
	          );
		add_settings_field(
		  'wplauncher_logo',
		  'Logo',
		  array( $this, 'wpl_render_settings_field' ),
		  $this->plugin_name.'_general_settings',
		  'wplauncher_general_section',
		  $args
		);
		unset($args);
	  	$args = array (
	              'type'      => 'select',
				  'subtype'	  => '',
				  'id'	  => $this->plugin_name.'_currency',
				  'name'	  => $this->plugin_name.'_currency',
				  'required' => 'required="required"',
				  'get_options_list' => 'wpl_get_currency_list',
				  'value_type'=>'normal',
				  'wp_data' => 'option'
	          );
		add_settings_field(
		  'wplauncher_currency',
		  'Currency',
		  array( $this, 'wpl_render_settings_field' ),
		  $this->plugin_name.'_general_settings',
		  'wplauncher_general_section',
		  $args
		);
		unset($args);
	  	// $args = array (
// 	              'type'      => 'select',
// 				  'subtype'	  => '',
// 				  'id'	  => $this->plugin_name.'_payment_processor',
// 				  'name'	  => $this->plugin_name.'_payment_processor',
// 				  'required' => 'required="required"',
// 				  'get_options_list' => 'get_payment_processor_list',
// 				  'value_type'=>'normal',
// 				  'wp_data' => 'option'
// 	          );
// 		add_settings_field(
// 		  'wplauncher_payment_processor',
// 		  'Payment Processor',
// 		  array( $this, 'wpl_render_settings_field' ),
// 		  $this->plugin_name.'_general_settings',
// 		  'wplauncher_general_section',
// 		  $args
// 		);
//
// 		unset($args);
// 	  	$args = array (
// 	              'type'      => 'select',
// 				  'subtype'	  => '',
// 				  'id'	  => $this->plugin_name.'_email_list_processor',
// 				  'name'	  => $this->plugin_name.'_email_list_processor',
// 				  'required' => 'required="required"',
// 				  'get_options_list' => 'get_email_list_processor_list',
// 				  'value_type'=>'normal',
// 				  'wp_data' => 'option'
// 	          );
// 		add_settings_field(
// 		  'wplauncher_email_list_processor',
// 		  'Email List Processor',
// 		  array( $this, 'wpl_render_settings_field' ),
// 		  $this->plugin_name.'_general_settings',
// 		  'wplauncher_general_section',
// 		  $args
// 		);
		
		register_setting(
				  			    'wplauncher_general_settings',
				  			    'wplauncher_company_name'
				  			    );
				register_setting(
				  			    'wplauncher_general_settings',
				  			    'wplauncher_logo'
				  			    );
				register_setting(
				   'wplauncher_general_settings',
				   'wplauncher_currency'
				   );
		   	 	// register_setting(
// 		   		   'wplauncher_general_settings',
// 		   		   'wplauncher_payment_processor'
// 		   		   );
// 		   	 	register_setting(
// 		   		   'wplauncher_general_settings',
// 		   		   'wplauncher_email_list_processor'
// 		   		   );	   		
			// add_settings_section(
// 				// ID used to identify this section and with which to register options
// 				'wplauncher_post_checkout_section',
// 				// Title to be displayed on the administration page
// 				'After Checkout',
// 				// Callback used to render the description of the section
// 				array( $this, 'wplauncher_display_post_checkout' ),
// 				// Page on which to add this section of options
// 				$this->plugin_name.'_post_checkout_settings'
// 			);
// 			unset($args);
// 		  	$args = array (
// 		              'type'      => 'input',
// 					  'subtype'	  => 'text',
// 					  'id'	  => $this->plugin_name.'_post_checkout_redirect',
// 					  'name'	  => $this->plugin_name.'_post_checkout_redirect',
// 					  'required' => '',
// 					  'get_options_list' => '',
// 					  'value_type'=>'normal',
// 					  'wp_data' => 'option'
// 		          );
// 			add_settings_field(
// 			  'wplauncher_post_checkout_redirect',
// 			  'Thank You Page Redirect',
// 			  array( $this, 'wpl_render_settings_field' ),
// 			  $this->plugin_name.'_post_checkout_settings',
// 			  'wplauncher_post_checkout_section',
// 			  $args
// 			);
// 			unset($args);
// 		  	$args = array (
// 		              'type'      => 'input',
// 					  'subtype'	  => 'text',
// 					  'id'	  => $this->plugin_name.'_post_checkout_msg',
// 					  'name'	  => $this->plugin_name.'_post_checkout_msg',
// 					  'required' => '',
// 					  'get_options_list' => '',
// 					  'value_type'=>'normal',
// 					  'wp_data' => 'option'
// 		          );
// 			add_settings_field(
// 			  'wplauncher_post_checkout_msg',
// 			  'Thank You Message',
// 			  array( $this, 'wpl_render_settings_field' ),
// 			  $this->plugin_name.'_post_checkout_settings',
// 			  'wplauncher_post_checkout_section',
// 			  $args
// 			);
// 	   		register_setting(
// 		   	 	'wplauncher_post_checkout_settings',
// 		    	'wplauncher_post_checkout_redirect'
// 		    );
// 	   		register_setting(
//   			    'wplauncher_post_checkout_settings',
//   			    'wplauncher_post_checkout_msg'
//   			    );
//
		
		
		// add_settings_section(
// 			// ID used to identify this section and with which to register options
// 			'wplauncher_stripe_account_section',
// 			// Title to be displayed on the administration page
// 			'Stripe Account',
// 			// Callback used to render the description of the section
// 			array( $this, 'wplauncher_display_stripe_account' ),
// 			// Page on which to add this section of options
// 			$this->plugin_name.'_stripe_settings'
// 		);
// 		unset($args);
// 	  	$args = array (
// 	              'type'      => 'select',
// 				  'subtype'	  => '',
// 				  'id'	  => $this->plugin_name.'_stripe_status',
// 				  'name'	  => $this->plugin_name.'_stripe_status',
// 				  'required' => 'required="required"',
// 				  'get_options_list' => 'get_stripe_status_list',
// 				  'value_type'=>'normal',
// 				  'wp_data' => 'option'
// 	          );
// 		add_settings_field(
// 			'wplauncher_stripe_status',
// 			'Stripe Status',
// 			array( $this, 'wpl_render_settings_field' ),
// 			$this->plugin_name.'_stripe_settings',
// 			'wplauncher_stripe_account_section',
// 			$args
// 		);
// 		unset($args);
// 	  	$args = array (
// 	              'type'      => 'input',
// 				  'subtype'	  => 'text',
// 				  'id'	  => $this->plugin_name.'_stripe_checkout_logo',
// 				  'name'	  => $this->plugin_name.'_stripe_checkout_logo',
// 				  'required' => '',
// 				  'get_options_list' => '',
// 				  'value_type'=>'normal',
// 				  'wp_data' => 'option'
// 	          );
// 		add_settings_field(
// 		  'wplauncher_stripe_checkout_logo',
// 		  'Checkout Logo (128x128px minimum)',
// 		  array( $this, 'wpl_render_settings_field' ),
// 		  $this->plugin_name.'_stripe_settings',
// 		  'wplauncher_stripe_account_section',
// 		  $args
// 		);
//
//
// 		$siteURL = urlencode(get_site_url().'/wp-admin/admin.php?page=wplauncher-settings');
// 		$stripeLivePublicKey = get_option('wplauncher_stripe_live_public_key');
// 		$stripeLiveSecretKey = get_option('wplauncher_stripe_live_secret_key');
// 		$stripeTestPublicKey = get_option('wplauncher_stripe_test_public_key');
// 		$stripeTestSecretKey = get_option('wplauncher_stripe_test_secret_key');
// 		if($stripeLivePublicKey && $stripeLiveSecretKey && $stripeTestPublicKey && $stripeTestSecretKey){
// 			add_settings_field(
// 			  'wplauncher_stripe_api_2',
// 			  'Connected to Stripe',
// 			  array( $this, 'wplauncher_render_stripe_connected' ),
// 			  $this->plugin_name.'_stripe_settings',
// 			  'wplauncher_stripe_account_section',
// 			  $args
// 			);
// 		} else {
// 			add_settings_field(
// 			  'wplauncher_stripe_api_2',
// 			  'Connect to Stripe',
// 			  array( $this, 'wplauncher_render_stripe_connect' ),
// 			  $this->plugin_name.'_stripe_settings',
// 			  'wplauncher_stripe_account_section',
// 			  $args
// 			);
// 		}
// 		/*if($stripeLivePublicKey && $stripeLiveSecretKey){
// 			add_settings_field(
// 			  'wplauncher_stripe_live_api_2',
// 			  'Connected to Live Stripe',
// 			  array( $this, 'wplauncher_render_stripe_live_connected' ),
// 			  $this->plugin_name,
// 			  'wplauncher_stripe_account_section',
// 			  $args
// 			);
// 		} else {
// 			unset($args);
// 		  	$args = array (
// 		              'type'      => 'link',
// 					  'subtype'	  => '',
// 					  'id'	  => $this->plugin_name.'_stripe_live_api_2',
// 					  'name'	  => $this->plugin_name.'_stripe_live_api_2',
// 					  'required' => '',
// 					  'get_options_list' => '',
// 					  'value_type'=>'normal',
// 					  'wp_data' => 'option',
// 					  'href'=> 'http://wplauncher.wpengine.com/stripe-connect/auth.php',
// 					  'content'=> 'Connect',
// 					  'target'=>'_blank',
// 					  'class'=>'btn stripe-live-login'
// 		          );
// 			add_settings_field(
// 			  'wplauncher_stripe_live_api_2',
// 			  'Connect to Live Stripe',
// 			  array( $this, 'wpl_render_settings_field' ),
// 			  $this->plugin_name,
// 			  'wplauncher_stripe_account_section',
// 			  $args
// 			);
// 		}*/
// 		unset($args);
// 	  	$args = array (
// 	              'type'      => 'input',
// 				  'subtype'	  => 'hidden',
// 				  'id'	  => $this->plugin_name.'_stripe_live_secret_key',
// 				  'name'	  => $this->plugin_name.'_stripe_live_secret_key',
// 				  'get_options_list' => '',
// 				  'value_type'=>'normal',
// 				  'wp_data' => 'option'
// 	          );
// 		add_settings_field(
// 		  'wplauncher_stripe_live_secret_key',
// 		  '',
// 		  array( $this, 'wpl_render_settings_field' ),
// 		  $this->plugin_name.'_stripe_settings',
// 		  'wplauncher_stripe_account_section',
// 		  $args
// 		);
// 		unset($args);
// 	  	$args = array (
// 	              'type'      => 'input',
// 				  'subtype'	  => 'hidden',
// 				  'id'	  => $this->plugin_name.'_stripe_live_public_key',
// 				  'name'	  => $this->plugin_name.'_stripe_live_public_key',
// 				  'get_options_list' => '',
// 				  'value_type'=>'normal',
// 				  'wp_data' => 'option'
// 	          );
// 		add_settings_field(
// 		  'wplauncher_stripe_live_public_key',
// 		  '',
// 		  array( $this, 'wpl_render_settings_field' ),
// 		  $this->plugin_name.'_stripe_settings',
// 		  'wplauncher_stripe_account_section',
// 		  $args
// 		);
// 		/* NEED TO BC USING REFRESH TOKEN TO GET TEST SECRET and PUBLIC KEYS DIDN"T WORK*/
// 		/*if($stripeTestPublicKey && $stripeTestSecretKey){
// 			add_settings_field(
// 			  'wplauncher_stripe_test_api_2',
// 			  'Connected to Test Stripe',
// 			  array( $this, 'wplauncher_render_stripe_test_connected' ),
// 			  $this->plugin_name,
// 			  'wplauncher_stripe_account_section',
// 			  $args
// 			);
// 		} else {
// 			unset($args);
// 		  	$args = array (
// 		              'type'      => 'link',
// 					  'subtype'	  => '',
// 					  'id'	  => $this->plugin_name.'_stripe_test_api_2',
// 					  'name'	  => $this->plugin_name.'_stripe_test_api_2',
// 					  'required' => '',
// 					  'get_options_list' => '',
// 					  'value_type'=>'normal',
// 					  'wp_data' => 'option',
// 					  'href'=> 'http://wplauncher.wpengine.com/stripe-connect/auth.php',
// 					  'content'=> 'Connect',
// 					  'target'=>'_blank',
// 					  'class'=>'btn stripe-test-login'
// 		          );
// 			add_settings_field(
// 			  'wplauncher_stripe_test_api_2',
// 			  'Connect to Test Stripe',
// 			  array( $this, 'wpl_render_settings_field' ),
// 			  $this->plugin_name,
// 			  'wplauncher_stripe_account_section',
// 			  $args
// 			);
// 		}*/
// 		unset($args);
// 	  	$args = array (
// 	              'type'      => 'input',
// 				  'subtype'	  => 'hidden',
// 				  'id'	  => $this->plugin_name.'_stripe_test_secret_key',
// 				  'name'	  => $this->plugin_name.'_stripe_test_secret_key',
// 				  'get_options_list' => '',
// 				  'value_type'=>'normal',
// 				  'wp_data' => 'option'
// 	          );
// 		add_settings_field(
// 		  'wplauncher_stripe_test_secret_key',
// 		  '',
// 		  array( $this, 'wpl_render_settings_field' ),
// 		  $this->plugin_name.'_stripe_settings',
// 		  'wplauncher_stripe_account_section',
// 		  $args
// 		);
// 		unset($args);
// 	  	$args = array (
// 	              'type'      => 'input',
// 				  'subtype'	  => 'hidden',
// 				  'id'	  => $this->plugin_name.'_stripe_test_public_key',
// 				  'name'	  => $this->plugin_name.'_stripe_test_public_key',
// 				  'get_options_list' => '',
// 				  'value_type'=>'normal',
// 				  'wp_data' => 'option'
// 	          );
// 		add_settings_field(
// 		  'wplauncher_stripe_test_public_key',
// 		  '',
// 		  array( $this, 'wpl_render_settings_field' ),
// 		  $this->plugin_name.'_stripe_settings',
// 		  'wplauncher_stripe_account_section',
// 		  $args
// 		);
//
// 		/*unset($args);
// 	  	$args = array (
// 	              'type'      => 'input',
// 				  'subtype'	  => 'text',
// 				  'id'	  => $this->plugin_name.'_stripe_live_secret_key',
// 				  'name'	  => $this->plugin_name.'_stripe_live_secret_key',
// 				  'required' => 'required="required"',
// 				  'get_options_list' => '',
// 				  'value_type'=>'normal',
// 				  'wp_data' => 'option'
// 	          );
// 		add_settings_field(
// 		  'wplauncher_stripe_live_secret_key',
// 		  'Live Secret Key*',
// 		  array( $this, 'wpl_render_settings_field' ),
// 		  $this->plugin_name,
// 		  'wplauncher_stripe_account_section',
// 		  $args
// 		);
// 		unset($args);
// 	  	$args = array (
// 	              'type'      => 'input',
// 				  'subtype'	  => 'text',
// 				  'id'	  => $this->plugin_name.'_stripe_live_public_key',
// 				  'name'	  => $this->plugin_name.'_stripe_live_public_key',
// 				  'required' => 'required="required"',
// 				  'get_options_list' => '',
// 				  'value_type'=>'normal',
// 				  'wp_data' => 'option'
// 	          );
// 		add_settings_field(
// 		  'wplauncher_stripe_live_public_key',
// 		  'Live Public Key*',
// 		  array( $this, 'wpl_render_settings_field' ),
// 		  $this->plugin_name,
// 		  'wplauncher_stripe_account_section',
// 		  $args
// 		);
// 		unset($args);
// 	  	$args = array (
// 	              'type'      => 'input',
// 				  'subtype'	  => 'text',
// 				  'id'	  => $this->plugin_name.'_stripe_test_secret_key',
// 				  'name'	  => $this->plugin_name.'_stripe_test_secret_key',
// 				  'required' => 'required="required"',
// 				  'get_options_list' => '',
// 				  'value_type'=>'normal',
// 				  'wp_data' => 'option'
// 	          );
// 		add_settings_field(
// 		  'wplauncher_stripe_test_secret_key',
// 		  'Test Secret Key*',
// 		  array( $this, 'wpl_render_settings_field' ),
// 		  $this->plugin_name,
// 		  'wplauncher_stripe_account_section',
// 		  $args
// 		);
// 		unset($args);
// 	  	$args = array (
// 	              'type'      => 'input',
// 				  'subtype'	  => 'text',
// 				  'id'	  => $this->plugin_name.'_stripe_test_public_key',
// 				  'name'	  => $this->plugin_name.'_stripe_test_public_key',
// 				  'required' => 'required="required"',
// 				  'get_options_list' => '',
// 				  'value_type'=>'normal',
// 				  'wp_data' => 'option'
// 	          );
// 		add_settings_field(
// 		  'wplauncher_stripe_test_public_key',
// 		  'Test Public Key*',
// 		  array( $this, 'wpl_render_settings_field' ),
// 		  $this->plugin_name,
// 		  'wplauncher_stripe_account_section',
// 		  $args
// 		);*/
//
// 		// Finally, we register the fields with WordPress
// 		register_setting(
// 				  			    'wplauncher_wplauncher_settings',
// 				  			    'wplauncher_stripe_live_secret_key',
// 								array( $this, 'wplauncher_validate_secret_api_key' )
// 				  			    );
// 				register_setting(
// 				  			    'wplauncher_wplauncher_settings',
// 				  			    'wplauncher_stripe_live_public_key'
// 								/*array( $this, 'wplauncher_validate_public_api_key' )*/
// 				  			    );
// 				register_setting(
// 				  			    'wplauncher_wplauncher_settings',
// 				  			    'wplauncher_stripe_test_secret_key'
// 								/*array( $this, 'wplauncher_validate_test_secret_api_key' )*/
// 				  			    );
// 				register_setting(
// 				  			    'wplauncher_wplauncher_settings',
// 				  			    'wplauncher_stripe_test_public_key'
// 								/*array( $this, 'wplauncher_validate_test_public_api_key' )*/
// 				  			    );
// 				register_setting(
// 				  			    'wplauncher_wplauncher_settings',
// 				  			    'wplauncher_stripe_checkout_logo'
// 								/*array( $this, 'wplauncher_validate_test_public_api_key' )*/
// 				  			    );
// 				register_setting(
// 				  			    'wplauncher_wplauncher_settings',
// 				  			    'wplauncher_stripe_status',
// 								array( $this, 'wplauncher_validate_status' )
// 				  			    );
//
// 			/*MAILCHIMP*/
// 		add_settings_section(
// 		  // ID used to identify this section and with which to register options
// 		  'wplauncher_mailchimp_account_section',
// 		  // Title to be displayed on the administration page
// 		  'MailChimp Account',
// 		  // Callback used to render the description of the section
// 		   array( $this, 'wplauncher_display_mailchimp_account' ),
// 		  // Page on which to add this section of options
// 		  $this->plugin_name.'_mailchimp_settings'
// 		);
// 		// GET MAILCHIMP API FROM OAUTH2
// 		$siteURL = urlencode(get_site_url().'/wp-admin/admin.php?page=wplauncher-settings');
// 		$mailchimpAPIKey = get_option('wplauncher_mailchimp_api');
// 		if($mailchimpAPIKey){
// 			add_settings_field(
// 			  'wplauncher_mailchimp_api_2',
// 			  'Connected to MailChimp',
// 			  array( $this, 'wplauncher_render_mailchimp_connected' ),
// 			  $this->plugin_name.'_mailchimp_settings',
// 			  'wplauncher_mailchimp_account_section',
// 			  $args
// 			);
// 		} else {
// 			add_settings_field(
// 			  'wplauncher_mailchimp_api_2',
// 			  'Connect to MailChimp',
// 			  array( $this, 'wplauncher_render_mailchimp_connect' ),
// 			  $this->plugin_name.'_mailchimp_settings',
// 			  'wplauncher_mailchimp_account_section',
// 			  $args
// 			);
// 		}
// 		unset($args);
// 	  	$args = array (
// 	              'type'      => 'input',
// 				  'subtype'	  => 'hidden',
// 				  'id'	  => $this->plugin_name.'_mailchimp_api',
// 				  'name'	  => $this->plugin_name.'_mailchimp_api',
// 				  'required' => '',
// 				  'get_options_list' => '',
// 				  'value_type'=>'normal',
// 				  'wp_data' => 'option'
// 	          );
// 		add_settings_field(
// 		  'wplauncher_mailchimp_api',
// 		  '',
// 		  array( $this, 'wpl_render_settings_field' ),
// 		  $this->plugin_name.'_mailchimp_settings',
// 		  'wplauncher_mailchimp_account_section',
// 		  $args
// 		);
// 		if($mailchimpAPIKey){
// 			unset($args);
// 		  	$args = array (
// 		              'type'      => 'select',
// 					  'subtype'	  => '',
// 					  'id'	  => $this->plugin_name.'_mailchimp_gen_list_id',
// 					  'name'	  => $this->plugin_name.'_mailchimp_gen_list_id',
// 					  'required' => '',
// 					  'get_options_list' => 'get_mailchimp_list',
// 					  'value_type'=>'normal',
// 					  'wp_data' => 'option',
// 					  'attr_value' =>true
// 		          );
// 			add_settings_field(
// 			  'wplauncher_mailchimp_gen_list_id',
// 			  'General Interest List',
// 			  array( $this, 'wpl_render_settings_field' ),
// 			  $this->plugin_name.'_mailchimp_settings',
// 			  'wplauncher_mailchimp_account_section',
// 			  $args
// 			);
// 		} else {
// 			unset($args);
// 		  	$args = array (
// 		              'type'      => 'select',
// 					  'subtype'	  => '',
// 					  'id'	  => $this->plugin_name.'_mailchimp_gen_list_id',
// 					  'name'	  => $this->plugin_name.'_mailchimp_gen_list_id',
// 					  'required' => '',
// 					  'get_options_list' => 'get_mailchimp_list',
// 					  'value_type'=>'normal',
// 					  'wp_data' => 'option',
// 					  'display'=>'none',
// 					  'attr_value' =>true
// 		          );
// 			add_settings_field(
// 			  'wplauncher_mailchimp_gen_list_id',
// 			  '',
// 			  array( $this, 'wpl_render_settings_field' ),
// 			  $this->plugin_name.'_mailchimp_settings',
// 			  'wplauncher_mailchimp_account_section',
// 			  $args
// 			);
// 		}
		/*unset($args);
	  	$args = array (
	              'type'      => 'input',
				  'subtype'	  => 'text',
				  'id'	  => $this->plugin_name.'_mc_sub_list_id',
				  'name'	  => $this->plugin_name.'_mc_sub_list_id',
				  'required' => '',
				  'get_options_list' => '',
				  'value_type'=>'normal',
				  'wp_data' => 'option'
	          );
		add_settings_field(
		  'wplauncher_mc_sub_list_id',
		  'Subscriber List ID',
		  array( $this, 'wpl_render_settings_field' ),
		  $this->plugin_name,
		  'wplauncher_mailchimp_account_section',
		  $args
		);
		unset($args);
	  	$args = array (
	              'type'      => 'input',
				  'subtype'	  => 'text',
				  'id'	  => $this->plugin_name.'_mc_sub_grouping_name',
				  'name'	  => $this->plugin_name.'_mc_sub_grouping_name',
				  'required' => '',
				  'get_options_list' => '',
				  'value_type'=>'normal',
				  'wp_data' => 'option'
	          );
		add_settings_field(
		  'wplauncher_mc_sub_grouping_name',
		  'General List Grouping Name (*for Subscriber)',
		  array( $this, 'wpl_render_settings_field' ),
		  $this->plugin_name,
		  'wplauncher_mailchimp_account_section',
		  $args
		);
		unset($args);
	  	$args = array (
	              'type'      => 'input',
				  'subtype'	  => 'text',
				  'id'	  => $this->plugin_name.'_mc_sub_group_name',
				  'name'	  => $this->plugin_name.'_mc_sub_group_name',
				  'required' => '',
				  'get_options_list' => '',
				  'value_type'=>'normal',
				  'wp_data' => 'option'
	          );
		add_settings_field(
		  'wplauncher_mc_sub_group_name',
		  'General List Group Name (*for Subscriber)',
		  array( $this, 'wpl_render_settings_field' ),
		  $this->plugin_name,
		  'wplauncher_mailchimp_account_section',
		  $args
		);*/
	 	// register_setting(
// 		   'wplauncher_mailchimp_settings',
// 		   'wplauncher_mailchimp_api'
// 		   );
//    	 	register_setting(
//    		   'wplauncher_mailchimp_settings',
//    		   'wplauncher_mailchimp_api_2'
//    		   );
// 	 	register_setting(
// 		   'wplauncher_mailchimp_settings',
// 		   'wplauncher_mailchimp_gen_list_id'
// 		   );	
    }
	/**
	 * Render Settings Fields Inputs/Select Boxes - This streamlines the creation of a setting input or select box field. Pass arguments to this function to create the setting field you would like to create
	 *
	 * @since    1.0.0
	 */
	public function wpl_render_settings_field($args) {
		/* EXAMPLE INPUT
	              'type'      => 'select',
				  'subtype'	  => '',
				  'id'	  => $this->plugin_name.'_currency',
				  'name'	  => $this->plugin_name.'_currency',
				  'required' => 'required="required"',
				  'get_option_list' => {function_name},
					'value_type' = serialized OR normal,
		'wp_data'=>(option or post_meta),
		'post_id' =>
		*/
		
		if($args['wp_data'] == 'option'){
			$wp_data_value = get_option($args['name']);
		} elseif($args['wp_data'] == 'post_meta'){
			$wp_data_value = get_post_meta($args['post_id'], $args['name'], true );
		}
		
		switch ($args['type']) {
			case 'select':
				// get the options list array from the get_options_list array value
				$wp_data_list = call_user_func(array('Wplauncher_Admin', $args['get_options_list']), $args);
				//$wp_data_list = $this->$args['get_options_list']($args);
				foreach($wp_data_list AS $o){
					$value = ($args['value_type'] == 'serialized') ? serialize($o) : $o['value'];
					$select_options .= ($value == $wp_data_value) ? '<option selected="selected" value=\''.esc_attr($value).'\'>'.$o['name'].'</option>' : '<option value=\''.esc_attr($value).'\'>'.$o['name'].'</option>';
				}
				if(isset($args['disabled'])){
					// hide the actual input bc if it was just a disabled input the informaiton saved in the database would be wrong - bc it would pass empty values and wipe the actual information
					echo '<select id="'.$args['id'].'_disabled" disabled name="'.$args['name'].'_disabled">'.$select_options.'</select><input type="hidden" id="'.$args['id'].'" name="'.$args['name'].'" value="' . esc_attr($wp_data_value) . '" />';
				} else {
					$display = (isset($args['display'])) ? 'style="display:'.$args['display'].';"' : '';
					$attr_value = (isset($args['attr_value'])) ? 'data-value="'.esc_attr($wp_data_value).'"' : '';
					echo '<select '.$attr_value.' '.$display.' id="'.$args['id'].'" "'.$args['required'].'" name="'.$args['name'].'">'.$select_options.'</select>';
					
				}
				
				break;
			case 'input':
				$value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
				if($args['subtype'] != 'checkbox'){
					$prependStart = (isset($args['prepend_value'])) ? '<div class="input-prepend"> <span class="add-on">'.$args['prepend_value'].'</span>' : '';
					$prependEnd = (isset($args['prepend_value'])) ? '</div>' : '';
					$step = (isset($args['step'])) ? 'step="'.$args['step'].'"' : '';
					$min = (isset($args['min'])) ? 'min="'.$args['min'].'"' : '';
					$max = (isset($args['max'])) ? 'max="'.$args['max'].'"' : '';
					if(isset($args['disabled'])){
						// hide the actual input bc if it was just a disabled input the informaiton saved in the database would be wrong - bc it would pass empty values and wipe the actual information
						echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'_disabled" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'_disabled" size="40" disabled value="' . esc_attr($value) . '" /><input type="hidden" id="'.$args['id'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="40" value="' . esc_attr($value) . '" />'.$prependEnd;
					} else {
						echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="40" value="' . esc_attr($value) . '" />'.$prependEnd;
					}
					/*<input required="required" '.$disabled.' type="number" step="any" id="'.$this->plugin_name.'_cost2" name="'.$this->plugin_name.'_cost2" value="' . esc_attr( $cost ) . '" size="25" /><input type="hidden" id="'.$this->plugin_name.'_cost" step="any" name="'.$this->plugin_name.'_cost" value="' . esc_attr( $cost ) . '" />*/
					
				} else {
					$checked = ($value) ? 'checked' : '';
					echo '<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" name="'.$args['name'].'" size="40" value="1" '.$checked.' />';
				}
				break;
			default:
				# code...
				break;
		}
	}
	/**
	* Currency List
	* @since 1.0.2
	*/
	public function wpl_get_currency_details($currency){
		$currency_list = $this->wpl_get_currency_list();
		foreach($currency_list AS $c){
			if($c['value'] == strtolower($currency) || $c['value'] == strtoupper($currency)){
				return $c;
				break;
			}
		}
	}
	/**
	* Currency List
	* @since 1.0.2
	*/
	public function wpl_get_currency_list(){
	  	  $currencyList = array(		
			  0 => array('value'=> 'AED', 'symbol' =>'', 'name' => 'AED - United Arab Emirates Dirham' 				),
			  1 => array('value'=> 'AFN', 'symbol' =>'؋', 'name' => 'AFN - Afghan Afghani*' 							),
			  2 => array('value'=> 'ALL', 'symbol' =>'Lek', 'name' => 'ALL - Albanian Lek' 							),
			  3 => array('value'=> 'AMD', 'symbol' =>'', 'name' => 'AMD - Armenian Dram' 							),
			  4 => array('value'=> 'ANG', 'symbol' =>'ƒ', 'name' => 'ANG - Netherlands Antillean Gulden' 			),
			  5 => array('value'=> 'AOA', 'symbol' =>'', 'name' => 'AOA - Angolan Kwanza*' 							),
			  6 => array('value'=> 'ARS', 'symbol' =>'$', 'name' => 'ARS - Argentine Peso*' 							),
			  7 => array('value'=> 'AUD', 'symbol' =>'$', 'name' => 'AUD - Australian Dollar' 						),
			  8 => array('value'=> 'AWG', 'symbol' =>'ƒ', 'name' => 'AWG - Aruban Florin' 							),
			  9 => array('value'=> 'AZN', 'symbol' =>'ман', 'name' => 'AZN - Azerbaijani Manat' 						),
			 10 => array('value'=> 'BAM', 'symbol' =>'KM', 'name' => 'BAM - Bosnia & Herzegovina Convertible Mark'	),
			 11 => array('value'=> 'BBD', 'symbol' =>'$', 'name' => 'BBD - Barbadian Dollar' 						),
			 12 => array('value'=> 'BDT', 'symbol' =>'', 'name' => 'BDT - Bangladeshi Taka' 						),
			 13 => array('value'=> 'BGN', 'symbol' =>'лв', 'name' => 'BGN - Bulgarian Lev' 							),
			 14 => array('value'=> 'BIF', 'symbol' =>'', 'name' => 'BIF - Burundian Franc' 							),
			 15 => array('value'=> 'BMD', 'symbol' =>'$', 'name' => 'BMD - Bermudian Dollar' 						),
			 16 => array('value'=> 'BND', 'symbol' =>'$', 'name' => 'BND - Brunei Dollar' 							),
			 17 => array('value'=> 'BOB', 'symbol' =>'$b', 'name' => 'BOB - Bolivian Boliviano*' 						),
			 18 => array('value'=> 'BRL', 'symbol' =>'R$', 'name' => 'BRL - Brazilian Real*' 							),
			 19 => array('value'=> 'BSD', 'symbol' =>'$', 'name' => 'BSD - Bahamian Dollar' 							),
			 20 => array('value'=> 'BWP', 'symbol' =>'P', 'name' => 'BWP - Botswana Pula' 							),
			 21 => array('value'=> 'BZD', 'symbol' =>'BZ$', 'name' => 'BZD - Belize Dollar' 							),
			 22 => array('value'=> 'CAD', 'symbol' =>'$', 'name' => 'CAD - Canadian Dollar' 							),
			 23 => array('value'=> 'CDF', 'symbol' =>'', 'name' => 'CDF - Congolese Franc' 							),
			 24 => array('value'=> 'CHF', 'symbol' =>'CHF', 'name' => 'CHF - Swiss Franc' 								),
			 25 => array('value'=> 'CLP', 'symbol' =>'$', 'name' => 'CLP - Chilean Peso*' 							),
			 26 => array('value'=> 'CNY', 'symbol' =>'¥', 'name' => 'CNY - Chinese Renminbi Yuan' 					),
			 27 => array('value'=> 'COP', 'symbol' =>'$', 'name' => 'COP - Colombian Peso*' 							),
			 28 => array('value'=> 'CRC', 'symbol' =>'₡', 'name' => 'CRC - Costa Rican Colón*' 						),
			 29 => array('value'=> 'CVE', 'symbol' =>'', 'name' => 'CVE - Cape Verdean Escudo*' 					),
			 30 => array('value'=> 'CZK', 'symbol' =>'Kč', 'name' => 'CZK - Czech Koruna*' 							),
			 31 => array('value'=> 'DJF', 'symbol' =>'', 'name' => 'DJF - Djiboutian Franc*' 						),
			 32 => array('value'=> 'DKK', 'symbol' =>'kr', 'name' => 'DKK - Danish Krone' 							),
			 33 => array('value'=> 'DOP', 'symbol' =>'RD$', 'name' => 'DOP - Dominican Peso' 							),
			 34 => array('value'=> 'DZD', 'symbol' =>'', 'name' => 'DZD - Algerian Dinar' 							),
			 35 => array('value'=> 'EEK', 'symbol' =>'kr', 'name' => 'EEK - Estonian Kroon*' 							),
			 36 => array('value'=> 'EGP', 'symbol' =>'£', 'name' => 'EGP - Egyptian Pound' 							),
			 37 => array('value'=> 'ETB', 'symbol' =>'', 'name' => 'ETB - Ethiopian Birr' 							),
			 38 => array('value'=> 'EUR', 'symbol' =>'€', 'name' => 'EUR - Euro' 									),
			 39 => array('value'=> 'FJD', 'symbol' =>'$', 'name' => 'FJD - Fijian Dollar' 							),
			 40 => array('value'=> 'FKP', 'symbol' =>'£', 'name' => 'FKP - Falkland Islands Pound*' 					),
			 41 => array('value'=> 'GBP', 'symbol' =>'£', 'name' => 'GBP - British Pound' 							),
			 42 => array('value'=> 'GEL', 'symbol' =>'', 'name' => 'GEL - Georgian Lari' 							),
			 43 => array('value'=> 'GIP', 'symbol' =>'£', 'name' => 'GIP - Gibraltar Pound' 							),
			 44 => array('value'=> 'GMD', 'symbol' =>'', 'name' => 'GMD - Gambian Dalasi' 							),
			 45 => array('value'=> 'GNF', 'symbol' =>'', 'name' => 'GNF - Guinean Franc*' 							),
			 46 => array('value'=> 'GTQ', 'symbol' =>'Q', 'name' => 'GTQ - Guatemalan Quetzal*' 						),
			 47 => array('value'=> 'GYD', 'symbol' =>'$', 'name' => 'GYD - Guyanese Dollar' 							),
			 48 => array('value'=> 'HKD', 'symbol' =>'$', 'name' => 'HKD - Hong Kong Dollar' 						),
			 49 => array('value'=> 'HNL', 'symbol' =>'L', 'name' => 'HNL - Honduran Lempira*' 						),
			 50 => array('value'=> 'HRK', 'symbol' =>'kn', 'name' => 'HRK - Croatian Kuna' 							),
			 51 => array('value'=> 'HTG', 'symbol' =>'', 'name' => 'HTG - Haitian Gourde' 							),
			 52 => array('value'=> 'HUF', 'symbol' =>'Ft', 'name' => 'HUF - Hungarian Forint*' 						),
			 53 => array('value'=> 'IDR', 'symbol' =>'Rp', 'name' => 'IDR - Indonesian Rupiah' 						),
			 54 => array('value'=> 'ILS', 'symbol' =>'₪', 'name' => 'ILS - Israeli New Sheqel' 						),
			 55 => array('value'=> 'INR', 'symbol' =>'', 'name' => 'INR - Indian Rupee*' 							),
			 56 => array('value'=> 'ISK', 'symbol' =>'kr', 'name' => 'ISK - Icelandic Króna' 							),
			 57 => array('value'=> 'JMD', 'symbol' =>'J$', 'name' => 'JMD - Jamaican Dollar' 							),
			 58 => array('value'=> 'JPY', 'symbol' =>'¥', 'name' => 'JPY - Japanese Yen' 							),
			 59 => array('value'=> 'KES', 'symbol' =>'', 'name' => 'KES - Kenyan Shilling' 							),
			 60 => array('value'=> 'KGS', 'symbol' =>'лв', 'name' => 'KGS - Kyrgyzstani Som' 							),
			 61 => array('value'=> 'KHR', 'symbol' =>'៛', 'name' => 'KHR - Cambodian Riel' 							),
			 62 => array('value'=> 'KMF', 'symbol' =>'', 'name' => 'KMF - Comorian Franc' 							),
			 63 => array('value'=> 'KRW', 'symbol' =>'₩', 'name' => 'KRW - South Korean Won' 						),
			 64 => array('value'=> 'KYD', 'symbol' =>'$', 'name' => 'KYD - Cayman Islands Dollar' 					),
			 65 => array('value'=> 'KZT', 'symbol' =>'лв', 'name' => 'KZT - Kazakhstani Tenge' 						),
			 66 => array('value'=> 'LAK', 'symbol' =>'₭', 'name' => 'LAK - Lao Kip*' 								),
			 67 => array('value'=> 'LBP', 'symbol' =>'£', 'name' => 'LBP - Lebanese Pound' 							),
			 68 => array('value'=> 'LKR', 'symbol' =>'₨', 'name' => 'LKR - Sri Lankan Rupee' 						),
			 69 => array('value'=> 'LRD', 'symbol' =>'$', 'name' => 'LRD - Liberian Dollar' 							),
			 70 => array('value'=> 'LSL', 'symbol' =>'', 'name' => 'LSL - Lesotho Loti' 							),
			 71 => array('value'=> 'LTL', 'symbol' =>'Lt', 'name' => 'LTL - Lithuanian Litas' 						),
			 72 => array('value'=> 'LVL', 'symbol' =>'Ls', 'name' => 'LVL - Latvian Lats' 							),
			 73 => array('value'=> 'MAD', 'symbol' =>'', 'name' => 'MAD - Moroccan Dirham' 							),
			 74 => array('value'=> 'MDL', 'symbol' =>'', 'name' => 'MDL - Moldovan Leu' 							),
			 75 => array('value'=> 'MGA', 'symbol' =>'', 'name' => 'MGA - Malagasy Ariary' 							),
			 76 => array('value'=> 'MKD', 'symbol' =>'ден', 'name' => 'MKD - Macedonian Denar' 						),
			 77 => array('value'=> 'MNT', 'symbol' =>'₮', 'name' => 'MNT - Mongolian Tögrög' 						),
			 78 => array('value'=> 'MOP', 'symbol' =>'', 'name' => 'MOP - Macanese Pataca' 							),
			 79 => array('value'=> 'MRO', 'symbol' =>'', 'name' => 'MRO - Mauritanian Ouguiya' 						),
			 80 => array('value'=> 'MUR', 'symbol' =>'₨', 'name' => 'MUR - Mauritian Rupee*' 						),
			 81 => array('value'=> 'MVR', 'symbol' =>'', 'name' => 'MVR - Maldivian Rufiyaa' 						),
			 82 => array('value'=> 'MWK', 'symbol' =>'', 'name' => 'MWK - Malawian Kwacha' 							),
			 83 => array('value'=> 'MXN', 'symbol' =>'$', 'name' => 'MXN - Mexican Peso*' 							),
			 84 => array('value'=> 'MYR', 'symbol' =>'RM', 'name' => 'MYR - Malaysian Ringgit' 						),
			 85 => array('value'=> 'MZN', 'symbol' =>'MT', 'name' => 'MZN - Mozambican Metical' 						),
			 86 => array('value'=> 'NAD', 'symbol' =>'$', 'name' => 'NAD - Namibian Dollar' 							),
			 87 => array('value'=> 'NGN', 'symbol' =>'₦', 'name' => 'NGN - Nigerian Naira' 							),
			 88 => array('value'=> 'NIO', 'symbol' =>'C$', 'name' => 'NIO - Nicaraguan Córdoba*' 						),
			 89 => array('value'=> 'NOK', 'symbol' =>'kr', 'name' => 'NOK - Norwegian Krone' 							),
			 90 => array('value'=> 'NPR', 'symbol' =>'₨', 'name' => 'NPR - Nepalese Rupee' 							),
			 91 => array('value'=> 'NZD', 'symbol' =>'$', 'name' => 'NZD - New Zealand Dollar' 						),
			 92 => array('value'=> 'PAB', 'symbol' =>'B/.', 'name' => 'PAB - Panamanian Balboa*' 						),
			 93 => array('value'=> 'PEN', 'symbol' =>'S/.', 'name' => 'PEN - Peruvian Nuevo Sol*' 						),
			 94 => array('value'=> 'PGK', 'symbol' =>'', 'name' => 'PGK - Papua New Guinean Kina' 					),
			 95 => array('value'=> 'PHP', 'symbol' =>'₱', 'name' => 'PHP - Philippine Peso' 							),
			 96 => array('value'=> 'PKR', 'symbol' =>'₨', 'name' => 'PKR - Pakistani Rupee' 							),
			 97 => array('value'=> 'PLN', 'symbol' =>'zł', 'name' => 'PLN - Polish Złoty' 							),
			 98 => array('value'=> 'PYG', 'symbol' =>'Gs', 'name' => 'PYG - Paraguayan Guaraní*' 						),
			 99 => array('value'=> 'QAR', 'symbol' =>'﷼', 'name' => 'QAR - Qatari Riyal' 							),
			100 => array('value'=> 'RON', 'symbol' =>'lei', 'name' => 'RON - Romanian Leu' 							),
			101 => array('value'=> 'RSD', 'symbol' =>'Дин.', 'name' => 'RSD - Serbian Dinar' 							),
			102 => array('value'=> 'RUB', 'symbol' =>'руб', 'name' => 'RUB - Russian Ruble' 							),
			103 => array('value'=> 'RWF', 'symbol' =>'', 'name' => 'RWF - Rwandan Franc' 							),
			104 => array('value'=> 'SAR', 'symbol' =>'﷼', 'name' => 'SAR - Saudi Riyal' 								),
			105 => array('value'=> 'SBD', 'symbol' =>'$', 'name' => 'SBD - Solomon Islands Dollar' 					),
			106 => array('value'=> 'SCR', 'symbol' =>'₨', 'name' => 'SCR - Seychellois Rupee' 						),
			107 => array('value'=> 'SEK', 'symbol' =>'kr', 'name' => 'SEK - Swedish Krona' 							),
			108 => array('value'=> 'SGD', 'symbol' =>'$', 'name' => 'SGD - Singapore Dollar' 						),
			109 => array('value'=> 'SHP', 'symbol' =>'£', 'name' => 'SHP - Saint Helenian Pound*' 					),
			110 => array('value'=> 'SLL', 'symbol' =>'', 'name' => 'SLL - Sierra Leonean Leone' 					),
			111 => array('value'=> 'SOS', 'symbol' =>'S', 'name' => 'SOS - Somali Shilling' 							),
			112 => array('value'=> 'SRD', 'symbol' =>'$', 'name' => 'SRD - Surinamese Dollar*' 						),
			113 => array('value'=> 'STD', 'symbol' =>'', 'name' => 'STD - São Tomé and Príncipe Dobra' 				),
			114 => array('value'=> 'SVC', 'symbol' =>'$', 'name' => 'SVC - Salvadoran Colón*' 						),
			115 => array('value'=> 'SZL', 'symbol' =>'', 'name' => 'SZL - Swazi Lilangeni' 							),
			116 => array('value'=> 'THB', 'symbol' =>'฿', 'name' => 'THB - Thai Baht' 								),
			117 => array('value'=> 'TJS', 'symbol' =>'', 'name' => 'TJS - Tajikistani Somoni' 						),
			118 => array('value'=> 'TOP', 'symbol' =>'', 'name' => 'TOP - Tongan Paʻanga' 							),
			119 => array('value'=> 'TRY', 'symbol' =>'', 'name' => 'TRY - Turkish Lira' 							),
			120 => array('value'=> 'TTD', 'symbol' =>'TT$', 'name' => 'TTD - Trinidad and Tobago Dollar' 				),
			121 => array('value'=> 'TWD', 'symbol' =>'NT$', 'name' => 'TWD - New Taiwan Dollar' 						),
			122 => array('value'=> 'TZS', 'symbol' =>'', 'name' => 'TZS - Tanzanian Shilling' 						),
			123 => array('value'=> 'UAH', 'symbol' =>'₴', 'name' => 'UAH - Ukrainian Hryvnia' 						),
			124 => array('value'=> 'UGX', 'symbol' =>'', 'name' => 'UGX - Ugandan Shilling' 						),
			125 => array('value'=> 'USD', 'symbol' =>'$', 'name' => 'USD - United States Dollar' 					),
			126 => array('value'=> 'UYU', 'symbol' =>'$U', 'name' => 'UYU - Uruguayan Peso*' 							),
			127 => array('value'=> 'UZS', 'symbol' =>'лв', 'name' => 'UZS - Uzbekistani Som' 							),
			128 => array('value'=> 'VND', 'symbol' =>'₫', 'name' => 'VND - Vietnamese Đồng' 							),
			129 => array('value'=> 'VUV', 'symbol' =>'', 'name' => 'VUV - Vanuatu Vatu' 							),
			130 => array('value'=> 'WST', 'symbol' =>'', 'name' => 'WST - Samoan Tala' 								),
			131 => array('value'=> 'XAF', 'symbol' =>'', 'name' => 'XAF - Central African Cfa Franc' 				),
			132 => array('value'=> 'XCD', 'symbol' =>'$', 'name' => 'XCD - East Caribbean Dollar' 					),
			133 => array('value'=> 'XOF', 'symbol' =>'', 'name' => 'XOF - West African Cfa Franc*' 					),
			134 => array('value'=> 'XPF', 'symbol' =>'', 'name' => 'XPF - Cfp Franc*' 								),
			135 => array('value'=> 'YER', 'symbol' =>'﷼', 'name' => 'YER - Yemeni Rial' 								),
			136 => array('value'=> 'ZAR', 'symbol' =>'R', 'name' => 'ZAR - South African Rand' 						),
			137 => array('value'=> 'ZMW', 'symbol' =>'', 'name' => 'ZMW - Zambian Kwacha' 							),
		);
		return $currencyList;
	}
}
