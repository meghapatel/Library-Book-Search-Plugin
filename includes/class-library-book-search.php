<?php
/**
 * Heart Of the Plugin : This Class Handles all stuff/functionality of this Plugin. 
 */
if( !class_exists( 'Library_Book_Search' ) ) {
	
class Library_Book_Search {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 * @var string VERSION Plugin version.
	 */
	const VERSION = '1';

	/**
	 * Unique identifier for your plugin.
	 *
	 */
	const PLUGIN_SLUG = 'library-book-search';

	protected $post_type = 'library';

	protected $taxonomies = array( 'author', 'publisher' );
	
	 /** Static property to hold our singleton instance */
    protected static $instance = null;
	
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Initialize the plugin by setting localization and new site activation hooks.
	 *
	 */
	public function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		
		add_action( 'init', array( $this, 'register_posttype_taxonomies_metaboxes' ) );
		
		 if ( is_admin() ) {
			// Load admin style sheet and JavaScript.
			add_action( 'admin_enqueue_scripts', array( $this, 'backend_enqueue_styles' ) );
			add_action('admin_menu', array($this,'admin_menu'));
			
        }
				
	    add_action( 'save_post', array( $this, 'library_meta_box_save' ), 10, 2 );
		add_filter('manage_edit-library_columns', array( $this, 'library_columns' ) , 10, 2 );
		add_action('manage_library_posts_custom_column', array( $this, 'library_show_columns' ) , 10, 2 );
		
		 if (  ! is_admin() ) 
		 {
			add_shortcode('library-listing-searching', array( $this, 'booklistview_template' ) ); 
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue_styles' ) );
			add_filter( 'template_include', array( $this, 'custom_post_type_template' ) );

		 }
		   
        // activation - deactivation things
		register_activation_hook( LBS_PLUGIN_FILE_PATH , array( $this, 'activate' ));
		register_deactivation_hook( LBS_PLUGIN_FILE_PATH , array( $this, 'deactivate' ));
	}
	
	
	public function admin_menu() {
		$plugin_page = add_menu_page(__("Library Guide","LibraryGuide"),__("Library Guide","LibraryGuide"),'manage_options','guide', array($this,'library_guide_page'),'',59);
	}
	
	public function backend_enqueue_styles() {
		wp_enqueue_style( self::PLUGIN_SLUG. '-backend', plugins_url( 'css/backend.css', LBS_PLUGIN_FILE_PATH ), array(), self::VERSION );
	}
	public function library_guide_page() {
		include_once( LBS_PLUGIN_DIR_PATH.'views/admin.php' );
	}
	
	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 */
	public function activate() {
		global $wp_version;
		global $wp_rewrite;
		if ( version_compare( $wp_version, '3.0', '<' ) )
		{
			wp_die( sprintf( 'Please use WordPress version above 3.0.1 or Upgrade source' ) );
		}
			
        $this->register_posttype_taxonomies_metaboxes();
		$this->add_records_with_author_publisher();
		$wp_rewrite->flush_rules();
	}
	
	public function add_records_with_author_publisher() {
		
		global $wp_error, $wpdb;
		
		$count_posts = wp_count_posts( 'library' )->publish;
		
		//adding static data
		
		if( isset( $count_posts ) && $count_posts==0 )
		{
		
				wp_insert_term(
					'Penguin Random House',   // the term 
					'publisher', // the taxonomy
					array(
						'description' => 'A Wellknown Publisher',
						'slug'        => 'random-house',
						'parent'      => 0,
					)
				);
				
				wp_insert_term(
					'HarperCollins',   // the term 
					'publisher', // the taxonomy
					array(
						'description' => 'A Wellknown Publisher',
						'slug'        => 'harpercollins',
						'parent'      => 0,
					)
				);
				
				wp_insert_term(
					'Hachette Livre',   // the term 
					'publisher', // the taxonomy
					array(
						'description' => 'A Wellknown Publisher',
						'slug'        => 'hachette-livre',
						'parent'      => 0,
					)
				);
				
				wp_insert_term(
					'William Shakespeare',   // the term 
					'author', // the taxonomy
					array(
						'description' => 'A Wellknown Author',
						'slug'        => 'william-shakespeare',
						'parent'      => 0,
					)
				);
				
				wp_insert_term(
					'Henry James',   // the term 
					'author', // the taxonomy
					array(
						'description' => 'A Wellknown Author',
						'slug'        => 'henry-james',
						'parent'      => 0,
					)
				);
				
				wp_insert_term(
					'Leo Tolstoy',   // the term 
					'author', // the taxonomy
					array(
						'description' => 'A Wellknown Author',
						'slug'        => 'leo-tolstoy',
						'parent'      => 0,
					)
				); 
	
			$books_list = array ( "Don Quixote by Miguel de Cervantes", "In Search of Lost Time by Marcel Proust", "Ulysses by James Joyce", "The Odyssey by Homer", "War and Peace by Leo Tolstoy", "Moby Dick by Herman Melville", "The Divine Comedy by Dante Alighieri", "Hamlet by William Shakespeare", "The Adventures of Huckleberry Finn by Mark Twain", "The Great Gatsby by F. Scott Fitzgerald", "The Iliad by Homer", "One Hundred Years of Solitude by Gabriel Garcia Marquez","Madame Bovary by Gustave Flaubert","Crime and Punishment by Fyodor Dostoyevsky", "The Brothers Karamazov by Fyodor Dostoyevsky", "Pride and Prejudice by Jane Austen", "Wuthering Heights by Emily Brontë", "The Sound and the Fury by William Faulkner", "Lolita by Vladimir Nabokov", "Nineteen Eighty Four by George Orwell", "Alice's Adventures in Wonderland by Lewis Carroll", "To the Lighthouse by Virginia Woolf", "Great Expectations by Charles Dickens", "Anna Karenina by Leo Tolstoy", "The Catcher in the Rye by J. D. Salinger");
			
			$book_rating = array ( 1,2,3,4,5,5,4,3,2,1,2,3,4,5,4,3,2,1,1,2,3,5,4,3,2);
			$book_price = array ( 123,124,125,126,127,128,129,521,522,524,578,987,789,456,654,213,417,852,963,369,258,147,753,951,357);
			
			$all_terms_author = get_terms( array( 'taxonomy' => 'author','hide_empty' => 0, 'fields' => 'ids', 'get' => 'all' ) );
			$all_terms_publisher = get_terms( array( 'taxonomy' => 'publisher','hide_empty' => 0, 'fields' => 'ids', 'get' => 'all' ) );
		
			
			foreach($books_list as $key => $value)
			{
				$hierarchical_tax = array(
					'author' => array( $all_terms_author[array_rand($all_terms_author, 1)] ),
					'publisher' => array( $all_terms_publisher[array_rand($all_terms_publisher, 1)] )
				);
			
				 
				$post_arr = array(
					'post_title'   => $value,
					'post_content' => $value.'is very popular book',
					'post_status'  => 'publish',
					'post_type'  => 'library',
					'post_author'  => get_current_user_id(),
					'tax_input'    => $hierarchical_tax ,
					'meta_input'   => array(
						'price_meta_box_text' => $book_price[$key],
						'rating_meta_box_select' => $book_rating[$key],
					),
				);
				
				wp_insert_post( $post_arr, true );
			}
	  }

	}
	
	
	public function library_columns($columns) {
		
  		  $new = array();
		  foreach($columns as $key => $title) {
			if ($key=='date') // Put the price,rating column before the date column
			{
			  $new['price'] = __( 'Price', 'library' );
			  $new['rating'] = __( 'Rating', 'library' );
			}
			$new[$key] = $title;
		  }
		  return $new;
	}
	
	public function library_show_columns( $column, $post_id ) {
			global $post;

			switch( $column ) {

				/* If displaying the 'price' column. */
				case 'price' :

					/* Get the post meta. */
					$price = get_post_meta( $post_id, 'price_meta_box_text', true );

					/* If no price is found, output a default message. */
					if ( empty( $price ) )
						echo __( 'N/A' );

					/* If there is a price, print text string. */
					else
						echo $price;

					break;

				/* If displaying the 'rating' column. */
				case 'rating' :

					/* Get the post meta. */
					$rating = get_post_meta( $post_id, 'rating_meta_box_select', true );

					/* If no rating is found, output a default message. */
					if ( empty( $rating ) )
						echo __( 'N/A' );

					/* If there is a rating, print text string. */
					else
						echo $rating;

					break;
					
				/* Just break out of the switch statement for everything else. */
				default :
					break;
			}
	}
	
	public function library_meta_box() {
		
		add_meta_box(
            'library-meta',
            __( 'Additional Information', 'library-information' ),
            array( $this, 'library_meta_box_cb' ),
            'library',
            'normal',
            'default'
        );
	}
	
	public function library_meta_box_cb( $post )
	{
		$values = get_post_custom( $post->ID );
		$text = isset( $values['price_meta_box_text'] ) ? esc_attr( $values['price_meta_box_text'][0] ) : '';
		$selected = isset( $values['rating_meta_box_select'] ) ? esc_attr( $values['rating_meta_box_select'][0] ) : '';
		?>
		<p>
			<label for="price_meta_box_text">Book Price</label>
			<input type="text" name="price_meta_box_text" id="price_meta_box_text" value="<?php echo $text; ?>" />
		</p>
		 
		<p>
			<label for="rating_meta_box_select">Book Rating</label>
			<select name="rating_meta_box_select" id="rating_meta_box_select">
				<option value="1" <?php selected( $selected, 1 ); ?>>1</option>
				<option value="2" <?php selected( $selected, 2 ); ?>>2</option>
				<option value="3" <?php selected( $selected, 3 ); ?>>3</option>
				<option value="4" <?php selected( $selected, 4 ); ?>>4</option>
				<option value="5" <?php selected( $selected, 5 ); ?>>5</option>
			</select>
		</p>
		<?php    
		wp_nonce_field( 'library_meta_box', 'library_meta_box_nonce' );
	}
	public function library_meta_box_save( $post_id, $post )
	{
		// if we're doing an auto save
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		
		if($post->post_type!='library') {
		   return $post->ID;
		}
		// verify this came from the our screen and with proper authorization,
		
		if( !isset( $_POST['library_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['library_meta_box_nonce'], 'library_meta_box' ) ){
			return;
		}
		
		// Is the user allowed to edit the post or page?
		if ( !current_user_can( 'edit_post', $post->ID ))
			return $post->ID; 
	 
		
		$safe_price = number_format( esc_attr( $_POST['price_meta_box_text'] ), 2 );
		if ( ! $safe_price ) {
		  $safe_price = '';
		} 
		
		$safe_rating = intval( $_POST['rating_meta_box_select'] );
		if ( ! $safe_rating ) {
		  $safe_rating = '';
		} 
		
		// set data to save
		if( isset( $_POST['price_meta_box_text'] ) )
			update_post_meta( $post_id, 'price_meta_box_text', $safe_price );
			 
		if( isset( $_POST['rating_meta_box_select'] ) )
			update_post_meta( $post_id, 'rating_meta_box_select', $safe_rating );
	}
	
	public function register_posttype_taxonomies_metaboxes() {
		
		// Ads Custom Post Type
        $labels = array(
            'name'               => 'Books',
            'singular_name'      => 'Book',
            'add_new'            => 'Add Book',
            'add_new_item'       => 'Add Book',
            'edit_item'          => 'Edit Book',
            'new_item'           => 'New Book',
            'all_items'          => 'All Books',
            'view_item'          => 'View Book',
            'search_items'       => 'Search Books',
            'not_found'          => 'No Books found',
            'not_found_in_trash' => 'No Books found in Trash',
            'parent_item_colon'  => '',
            'menu_name'          => 'Library'
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'library' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => true,
            'menu_position'      => null,
            'supports'           => array( 'title', 'editor', 'revisions' ),
			'taxonomies' => $this->taxonomies,
			'register_meta_box_cb' => array( $this, 'library_meta_box' )
        );

        register_post_type( 'library', $args );
		
	
		
		$args = array( 
            'hierarchical' => true,  
			'show_admin_column' => true,
                'labels' => array(
            'name'=> _x('Book Publishers', 'Book Publishers' ),
            'singular_name' => _x('Book Publisher', 'Book Publisher'),
            'search_items' => __('Search Publishers'),
            'popular_items' => __('Popular Publishers'),
            'all_items' => __('All Publishers'),
            'edit_item' => __('Edit Publisher'),
            'edit_item' => __('Edit Publisher'),
            'update_item' => __('Update Publisher'),
            'add_new_item' => __('Add New Publisher'),
            'new_item_name' => __('New Publisher Name'),
            'separate_items_with_commas' => __('Seperate Publishers with Commas'),
            'add_or_remove_items' => __('Add or Remove Publishers'),
            'choose_from_most_used' => __('Choose from Most Used Publishers')
            ),  
                'query_var' => true,  
            'rewrite' => array('slug' =>'publisher')        
           );
        register_taxonomy('publisher', 'library',$args);
		
			$args = array( 
            'hierarchical' => true,  
			'show_admin_column' => true,
                'labels' => array(
            'name'=> _x('Book Authors', 'Book Authors' ),
            'singular_name' => _x('Book Author', 'Book Author'),
            'search_items' => __('Search Authors'),
            'popular_items' => __('Popular Authors'),
            'all_items' => __('All Authors'),
            'edit_item' => __('Edit Author'),
            'edit_item' => __('Edit Author'),
            'update_item' => __('Update Author'),
            'add_new_item' => __('Add New Author'),
            'new_item_name' => __('New Author Name'),
            'separate_items_with_commas' => __('Seperate Authors with Commas'),
            'add_or_remove_items' => __('Add or Remove Authors'),
            'choose_from_most_used' => __('Choose from Most Used Authors')
            ),  
                'query_var' => true,  
            'rewrite' => array('slug' =>'author')        
           );
        register_taxonomy('author', 'library',$args);
		
	}
    
	/**
	 * Fired for each site when the plugin is deactivated.
	 *
	 */
	public function deactivate() {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 */
	public function load_plugin_textdomain() {
		$domain = self::PLUGIN_SLUG;
		load_plugin_textdomain( $domain, FALSE, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages' );
	}
	// start process for front end 
	public function booklistview_template() {
		include_once( LBS_PLUGIN_DIR_PATH.'views/booklistview.php' );
	}
	
	public function frontend_enqueue_styles() {
		global $post;
		if ( ! is_admin() &&  ( $post->post_type=="post" || $post->post_type=="page" || $post->post_type=="library" ) ) {
			
			wp_enqueue_style( self::PLUGIN_SLUG . '-b', plugins_url( 'css/bootstrap.min.css', LBS_PLUGIN_FILE_PATH ), array(), self::VERSION );
			
			wp_enqueue_style( self::PLUGIN_SLUG . '-boosl', plugins_url( 'css/bootstrap-slider.css', LBS_PLUGIN_FILE_PATH ), array(), self::VERSION );
			
			wp_enqueue_style( self::PLUGIN_SLUG . '-bd', plugins_url( 'css/dataTables.bootstrap4.min.css', LBS_PLUGIN_FILE_PATH ), array(), self::VERSION );
			
			wp_enqueue_style( self::PLUGIN_SLUG . '-fo', plugins_url( 'css/font-awesome.min.css', LBS_PLUGIN_FILE_PATH ), array(), self::VERSION );
			
			wp_enqueue_style( self::PLUGIN_SLUG . '-front', plugins_url( 'css/frontend.css', LBS_PLUGIN_FILE_PATH ), array(), self::VERSION );
			
			wp_register_script( self::PLUGIN_SLUG . '-jdt', plugins_url( 'js/jquery.dataTables.min.js', LBS_PLUGIN_FILE_PATH ), array( 'jquery' ), self::VERSION  );
			wp_enqueue_script( self::PLUGIN_SLUG . '-jdt' );
			
			wp_register_script( self::PLUGIN_SLUG . '-dt', plugins_url( 'js/dataTables.bootstrap4.min.js', LBS_PLUGIN_FILE_PATH ), array( 'jquery' ), self::VERSION  );
			wp_enqueue_script( self::PLUGIN_SLUG . '-dt' );
		
			wp_register_script( self::PLUGIN_SLUG . '-bsd', plugins_url( 'js/bootstrap-slider.js', LBS_PLUGIN_FILE_PATH ), array( 'jquery' ), self::VERSION  );
			wp_enqueue_script( self::PLUGIN_SLUG . '-bsd' );
			
		}
	}
	
	public function custom_post_type_template($template) {
		
		$post_types = array('library');
		
		if ( is_singular( $post_types ) && ! file_exists( get_stylesheet_directory() . '/single-library.php' ) )
        $template = dirname( LBS_PLUGIN_FILE_PATH ) . '/views/library-template.php';
	
		return $template;
	}
	
  } 
}