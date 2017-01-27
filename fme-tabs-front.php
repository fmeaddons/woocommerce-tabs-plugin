<?php 
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	echo 'This plugin required woocommerce installed!';
    exit;
}

if ( !class_exists( 'FMET_Front_Class' ) ) {

	class FMET_Front_Class extends FME_Tabs { 

		public function __construct() {

			add_action( 'wp_loaded', array( $this, 'front_init' ) );
			add_filter( 'woocommerce_product_tabs', array($this,'woocommerce_fme_product_tabs'),98 ); 
			add_action( 'woocommerce_before_single_product_summary', array($this, 'addlink'));
			
		}

		public function front_init() {
            
        	wp_enqueue_style( 'fme-tabs-css', FMET_URL . 'fme_tabs.css', false );
        	
        }


		function woocommerce_fme_product_tabs($tabs) {

			global $post;

			$product_custom_tabs = $this->get_product_custom_tabs($post->ID);


			

			foreach ($product_custom_tabs as $ptabs) {
                $tabs['fmeptab_'.$ptabs->tab_id] = array(
                    'title'    => esc_attr($ptabs->tab_name),
                    'title2'    => esc_attr($ptabs->tab_name),
                    'priority' => 50,
                    'callback' => array($this,'render_tab'),
                    'content'  => apply_filters('the_content', $ptabs->tab_description) //this allows shortcodes in custom tabs
                );


            }

			


			return $tabs;

		}

		function render_tab($key,$tab){
	        global $post;
	        echo '<h2>'.apply_filters('fme_ptab_title',esc_attr($tab['title2']),$tab,$key).'</h2>';
	        echo apply_filters('fme_ptab_content',$tab['content'],$tab,$key);
	    }

	    function addlink() { ?>
                <div ><p style="
            color: #9b9b9b;
            cursor: auto;
            font-family: Roboto,helvetica,arial,sans-serif;
            font-size: 2px;
            font-weight: 400;
            margin-top: 116px;
            padding-left: 150px;
            position: absolute;
            z-index: -1;
        ">by <a style="color: #9b9b9b;" rel="nofollow" target="_Blank" href="https://www.fmeaddons.com/woocommerce-plugins-extensions/product-custom-tabs.html">Fmeaddons</a></p>  </div>
            <?php }

	    

		function get_product_custom_tabs($product_id)
		{
			global $wpdb;
	        $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix . "fmet_product_tabs WHERE product_id = %d", $product_id ) ); 

	        return $result;
		}

		

		
	}


new FMET_Front_Class;

}


?>