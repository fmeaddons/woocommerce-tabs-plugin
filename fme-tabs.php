<?php 

/*
 * Plugin Name:       Product Tabs(Free)
 * Plugin URI:        https://www.fmeaddons.com/woocommerce-plugins-extensions/product-custom-tabs.html
 * Description:       FME Product Tabs provide the feature to add tabs for products. By using this module admin can add global or indivisual tabs for products.
 * Version:           1.0.1
 * Author:            FME Addons
 * Developed By:  	  Raja Usman Mehmood
 * Author URI:        http://fmeaddons.com/
 * Text Domain:       product-tabs-pro
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Check if WooCommerce is active
 * if wooCommerce is not active FME Tabs module will not work.
 **/
if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	echo 'This plugin required woocommerce installed!';
    exit;
}

if ( !class_exists( 'FME_Tabs' ) ) {

	class FME_Tabs { 

		function __construct()
		{ 
			
			$this->module_constants();
			$this->module_tables();
			
			
			
			if ( is_admin() ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );



				//add product tabs pro link in admin
        		add_action( 'woocommerce_product_write_panel_tabs', array($this,'fme_product_write_panel_tabs' ));
        		//add product tab content in admin
       			add_action('woocommerce_product_write_panels', array($this,'fme_product_write_panels'));
       			add_action('wp_ajax_tab_session', array($this, 'tab_session')); 
       			add_action('wp_ajax_tab_session_del', array($this, 'tab_session_del')); 
       			add_action('wp_ajax_tab_session_edit', array($this, 'tab_session_edit')); 
       			add_action('wp_ajax_ptab_submit', array($this, 'ptab_submit')); 




            } 

            else
            {
            	require_once( FMET_PLUGIN_DIR . 'fme-tabs-front.php' );
            }



		}

		

		public function admin_scripts() {	
            
        	wp_enqueue_style( 'fme-tabs-css', FMET_URL . 'fme_tabs.css', false );

        	
        }


       


        public function module_tables() {
            
			global $wpdb;
			
			$charset_collate = '';
			$wpdb->fmet_temp_tabs = $wpdb->prefix . 'fmet_temp_tabs';
			$wpdb->fmet_temp_gtabs = $wpdb->prefix . 'fmet_temp_gtabs';
			$wpdb->fmet_product_gtabs = $wpdb->prefix . 'fmet_product_gtabs';
			$wpdb->fmet_product_tabs = $wpdb->prefix . 'fmet_product_tabs';
			$wpdb->fmet_temp_dtabs = $wpdb->prefix . 'fmet_temp_dtabs';
			$wpdb->fmet_product_dtabs = $wpdb->prefix . 'fmet_product_dtabs';
			$wpdb->fmet_use_gtabs = $wpdb->prefix . 'fmet_use_gtabs';
			if ( !empty( $wpdb->charset ) )
				$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
			if ( !empty( $wpdb->collate ) )
				$charset_collate .= " COLLATE $wpdb->collate";	
				
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->fmet_temp_tabs'" ) != $wpdb->fmet_temp_tabs ) {
				$sql = "CREATE TABLE " . $wpdb->fmet_temp_tabs . " (
									 tab_id int(25) NOT NULL auto_increment,
									 ip varchar(255) NULL,
									 tab_name varchar(255) NULL,
									 tab_icon varchar(255) NULL,
									 tab_description text NULL,
									 date date NULL,
									 
									 PRIMARY KEY (tab_id)
									 ) $charset_collate;";
		
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
			}

			if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->fmet_temp_gtabs'" ) != $wpdb->fmet_temp_gtabs ) {
				$sql = "CREATE TABLE " . $wpdb->fmet_temp_gtabs . " (
									 tab_id int(25) NOT NULL auto_increment,
									 ip varchar(255) NULL,
									 tab_name varchar(255) NULL,
									 tab_icon varchar(255) NULL,
									 tab_description text NULL,
									 date date NULL,
									 status varchar(255) NULL,
									 postid varchar(255) NULL,
									 sort_order varchar(255) NULL,
									 
									 PRIMARY KEY (tab_id)
									 ) $charset_collate;";
		
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
			}


			if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->fmet_temp_dtabs'" ) != $wpdb->fmet_temp_dtabs ) {
				$sql = "CREATE TABLE " . $wpdb->fmet_temp_dtabs . " (
									 id int(25) NOT NULL auto_increment,
									 tab_id varchar(255) NULL,
									 tab_name varchar(255) NULL,
									 tab_icon varchar(255) NULL,
									 
									 PRIMARY KEY (id)
									 ) $charset_collate;";
		
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
			}



			if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->fmet_product_gtabs'" ) != $wpdb->fmet_product_gtabs ) {
				$sql1 = "CREATE TABLE " . $wpdb->fmet_product_gtabs . " (
									 tab_id int(25) NOT NULL auto_increment,
									 postid varchar(255) NULL,
									 product_id varchar(255) NULL,
									 tab_name varchar(255) NULL,
									 tab_icon varchar(255) NULL,
									 tab_description text NULL,
									 status varchar(255) NULL,
									 sort_order varchar(255) NULL,
									 
									 PRIMARY KEY (tab_id)
									 ) $charset_collate;";
		
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql1 );
			}


			if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->fmet_product_tabs'" ) != $wpdb->fmet_product_tabs ) {
				$sql1 = "CREATE TABLE " . $wpdb->fmet_product_tabs . " (
									 tab_id int(25) NOT NULL auto_increment,
									 product_id varchar(255) NULL,
									 tab_name varchar(255) NULL,
									 tab_icon varchar(255) NULL,
									 tab_description text NULL,
									 
									 PRIMARY KEY (tab_id)
									 ) $charset_collate;";
		
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql1 );
			}


			if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->fmet_product_dtabs'" ) != $wpdb->fmet_product_dtabs ) {
				$sql1 = "CREATE TABLE " . $wpdb->fmet_product_dtabs . " (
									 id int(25) NOT NULL auto_increment,
									 tab_id varchar(255) NULL,
									 product_id varchar(255) NULL,
									 tab_name varchar(255) NULL,
									 tab_icon varchar(255) NULL,
									 
									 PRIMARY KEY (id)
									 ) $charset_collate;";
		
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql1 );
			}

			if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->fmet_use_gtabs'" ) != $wpdb->fmet_use_gtabs ) {
				$sql1 = "CREATE TABLE " . $wpdb->fmet_use_gtabs . " (
									 id int(25) NOT NULL auto_increment,
									 product_id varchar(255) NULL,
									 use_gtabs varchar(255) NULL,
									 
									 PRIMARY KEY (id)
									 ) $charset_collate;";
		
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql1 );
			}




			


		}


		public function module_constants() {
            
            if ( !defined( 'FMET_URL' ) )
                define( 'FMET_URL', plugin_dir_url( __FILE__ ) );

            if ( !defined( 'FMET_BASENAME' ) )
                define( 'FMET_BASENAME', plugin_basename( __FILE__ ) );

            if ( ! defined( 'FMET_PLUGIN_DIR' ) )
                define( 'FMET_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        }




		/**
	     * Used to add a product tabs pro link to product add / edit screen
	     * @return void
	    */
	    function fme_product_write_panel_tabs() {
	        ?>
	        <li class="fme_tab">
	            <a href="#fme_tab_data">
	                <?php _e('Product Tabs', 'FMET'); ?>
	            </a>
	        </li>
	        <?php
	    }


	    



	     /**
	     * Used to display a product tabs pro tab content (fields) to product add / edit screen
	     * @return void
	     */
	    function fme_product_write_panels() { 
	    	$this->add_more_js();
	    	$this->delete_all_session_tabs();
	    	
	    	?>
	        <div id="fme_tab_data" class="panel woocommerce_options_panel fmetabsarea">

				
	        		

	        	
			   

	           <h2>Product Tabs</h2>
	           	   <div class="hlp">
	        			<img class="help_tip" data-tip='<?php _e( 'Product Tabs are specific tabs for this product. These tabs will only show with the current product on the frontend.', 'FMET' ) ?>' src="<?php echo  FMET_URL; ?>/help.png" height="16" width="16" />
	        		</div>
		           <div id="p_tabs">
			           <?php $this->tab_session_html(); ?>
		           	</div>

	           <div class="ftabcustom">
	           		<div class="add_tab_bt" onclick="openpopup();"><span class="preview button add-box my_modal_open"><span style="float: left;font-size: 20px;font-weight: bolder;margin-right: 5px;margin-top: -1px;" >+</span> Add Custom Tab</span></div>
	           </div>


	           <div id="pdtabs">
	        		<h2>Default Tabs</h2>
	        		<div class="hlp">
	        			<img class="help_tip" data-tip='<?php _e( 'Default Tabs are by default tabs of WooCommerce these tabs are created with each product. You can change tab icon and title from here.', 'FMET' ) ?>' src="<?php echo  FMET_URL; ?>/help.png" height="16" width="16" />
	        		</div>
	        		
		        		<div id="p_dtabs">

		        			<div class="upgrade_block">
								 <div class="inner_top_container">
								 <div class="logo">
								   <img src="<?php echo  FMET_URL ?>/fme-addons.png" alt="">
								 </div>
								<h2 style="float: none">Upgrade to <span>Premium</span></h2>
								<p>To get access to enhanced customization, improved experience and the ability to manage your tasks better, upgrade to the premium version now.</p>
								</div>
								<div class="buttonss"><a href="https://www.fmeaddons.com/woocommerce-plugins-extensions/product-custom-tabs.html" target="_blank">Upgrade</a></div>
							</div>

			            </div>
		        			
		        </div>

		        <div id="pdtabs">
	        		<h2>Global Tabs</h2>
	        		<div class="hlp">
	        			<img class="help_tip" data-tip='<?php _e( 'Global Tabs are created in Global Tabs section, by default these tabs are come with each product, but you can choose which tab to show and overwrite its icon, title and description here.', 'FMET' ) ?>' src="<?php echo  FMET_URL; ?>/help.png" height="16" width="16" />
	        		</div>
	        		
		        		<div id="p_dtabs">

		        			<div class="upgrade_block">
								 <div class="inner_top_container">
								 <div class="logo">
								   <img src="<?php echo  FMET_URL ?>/fme-addons.png" alt="">
								 </div>
								<h2 style="float: none">Upgrade to <span>Premium</span></h2>
								<p>To get access to enhanced customization, improved experience and the ability to manage your tasks better, upgrade to the premium version now.</p>
								</div>
								<div class="buttonss"><a href="https://www.fmeaddons.com/woocommerce-plugins-extensions/product-custom-tabs.html" target="_blank">Upgrade</a></div>
							</div>

			            </div>
		        			
		        </div>

	            <!-- The Modal -->
				<div id="my_modal" class="content w3-modal">
				  <div class="w3-modal-content">
				    <div class="w3-container">
				      <span onclick="document.getElementById('my_modal').style.display='none'"
				      class="w3-closebtn">&times;</span>
				      

						<form action="#" method="post" id="form">
					      <p><h2>Product Tab</h2></p>
					     
					      
					      	<div class="tabsform">
		           				
		           				<div class="tab_input">
		           					<b>Tab Title:</b><br />
		           					<input type="text" name="fme_product_custom_tab" class="" style="width:100%;" id="tabtitle" />
		           				</div>
		           				<div class="tab_content"> 
		           					<b>Tab Description:</b><br />
		           					<?php 
		           					$editor_id = "addfmetabcontent"; 
		           					if (isset( $_POST["fme_custom_tab_content"] ) && $_POST["fme_custom_tab_content"]!='' ) { 
		           							$content = esc_attr( html_entity_decode(stripslashes( $_POST["fme_custom_tab_content"] ) )); 
		           						} else {
		           							$content = ''; 
		           						}
		           						$settings = array(  "textarea_name" => "fme_custom_tab_content", "textarea_rows" => "20", 'editor_height' => 425 ); 
		           						wp_editor( $content, $editor_id, $settings ); 
		           						?> 
		           				</div>
		           				<input type="hidden" name="tab_id" id="tab_id" value="">
		       				</div>

					     
					      <p>
					      	<input id="submit" class="button button-primary button-large" type="button" value="Save" name="save">
					      	&nbsp;
					      	<input id="cancel" class="button button-primary button-large" type="button" value="Cancel" name="cancel">
					      </p>
					      
					    </form>


				    </div>
				  </div>
				</div>



			  

	        </div>
	        <?php }


	        function tab_session() {

	        	if ( !current_user_can( apply_filters( 'fmetabs_capability', 'manage_options' ) ) )
				die( '-1' );

				check_ajax_referer( 'fmetabs-add-ajax-nonce', 'addsecurity', false );
	        	
	        	$tab_id = intval($_POST['tab_id']); 
				$title = sanitize_text_field($_POST['title']);
				$content = stripslashes($_POST['content']);
				$date = date('Y-m-d');
				$ip = $_SERVER['REMOTE_ADDR'];
				global $wpdb;

			if($title!='') {
			if(isset($_POST['edit']) && $_POST['edit']=='edit')
    		{
    			if(isset($_POST['post']) && $_POST['post']!='')
    			{
    				$product_id = intval($_POST['post']);
    			} else $product_id = 0;

    			if($tab_id=='') {
				$wpdb->insert(
					$wpdb->prefix . 'fmet_temp_tabs',
					array(
						'tab_name' => $title,
						'tab_description' => $content,
						'date' => $date,
						'ip' => $ip
					),
					array(
						'%s','%s','%s','%s'
					)
				);
			}
			else
			{
				

				$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix . "fmet_product_tabs
                                    SET 
                                    tab_name = %s, 
                                    tab_description = %s
                                    WHERE tab_id = %d", $title, $content, $tab_id));  
			}

    		}
    		
    		else {

			if($tab_id=='') {
				$wpdb->insert(
					$wpdb->prefix . 'fmet_temp_tabs',
					array(
						'tab_name' => $title,
						'tab_description' => $content,
						'date' => $date,
						'ip' => $ip
					),
					array(
						'%s','%s','%s','%s'
					)
				);
			}
			else
			{ 
				



				$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix . "fmet_temp_tabs
                                    SET 
                                    tab_name = %s, 
                                    tab_description = %s,
                                    date = %s,
                                    ip = %s
                                    WHERE tab_id = %d", $title, $content, $date, $ip, $tab_id));
			}
		
		}
	}



				$this->tab_session_html();
				die();
				return true;
				
				
			
			}



			

			function tab_session_del()
			{

				if ( !current_user_can( apply_filters( 'fmetabs_capability', 'manage_options' ) ) )
				die( '-1' );

				check_ajax_referer( 'fmetabs-del-ajax-nonce', 'delsecurity', false );

				$tab_id = intval($_POST['tab_id']);
				global $wpdb;
				$wpdb->query( $wpdb->prepare( "DELETE FROM ".$wpdb->prefix . "fmet_temp_tabs WHERE tab_id = %d", $tab_id ) );
				die();
				return true;
			}

			function tab_session_edit()
			{
				if ( !current_user_can( apply_filters( 'fmetabs_capability', 'manage_options' ) ) )
				die( '-1' );

				check_ajax_referer( 'fmetabs-edit-ajax-nonce', 'editsecurity', false );

				$tab_id = intval($_POST['tab_id']);
				global $wpdb;

				if(isset($_POST['edit']) && $_POST['edit']=='edit' && $_POST['temptab']=='')
	        		{ 
	        			if(isset($_POST['post']) && $_POST['post']!='')
	        			{
	        				$product_id = intval($_POST['post']);
	        			} else $product_id = 0;
	        			
	        			$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix . "fmet_product_tabs WHERE tab_id = %d AND product_id = %d",  $tab_id, $product_id) );
	        		}
	        		else
	        		{

						$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix . "fmet_temp_tabs WHERE tab_id = %d", $tab_id ) );
					}
				echo json_encode($result);
				exit();
			}

			

			function delete_all_session_tabs()
			{ 
				global $wpdb;
				$date = date('Y-m-d');
				$ip = $_SERVER['REMOTE_ADDR'];
				$wpdb->query( $wpdb->prepare( "DELETE FROM ".$wpdb->prefix . "fmet_temp_tabs WHERE date = %s AND ip = %s", $date, $ip));
			}


			function get_all_session_tabs()
			{
				global $wpdb;
				$date = date('Y-m-d');
				$ip = $_SERVER['REMOTE_ADDR'];
	           	$result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix . "fmet_temp_tabs WHERE date = %s AND ip = %s",  $date, $ip) ); 

	           	return $result;
			}

			

			function ptab_submit()
			{

				if ( !current_user_can( apply_filters( 'fmetabs_capability', 'manage_options' ) ) )
				die( '-1' );

				check_ajax_referer( 'fmetabs-sub-ajax-nonce', 'subsecurity', false );

				global $wpdb;
				$last = $wpdb->get_row("SHOW TABLE STATUS LIKE 'wp_posts'");
        		$productid = ($last->Auto_increment)-1;

        		if(isset($_POST['edit']) && $_POST['edit']=='edit')
	        		{
	        			if(isset($_POST['post']) && $_POST['post']!='')
	        			{
	        				$product_id = intval($_POST['post']);
	        			} else $product_id = 0;

        				$wpdb->query( $wpdb->prepare( "DELETE FROM " .$wpdb->prefix . "fmet_product_tabs WHERE product_id = %d", $product_id ) );
        				if(isset($_POST['producttabs']) && $_POST['producttabs']!='')
						{
							
	                        foreach ($_POST['producttabs'] as $ptabs) {
	                         	
	                                $chk = explode('-_-',$ptabs);
	                                $result = $wpdb->query( 
	                                    $wpdb->prepare( 
	                                            "
	                                            INSERT INTO ".$wpdb->prefix . "fmet_product_tabs
	                                            (product_id, tab_name, tab_description)
	                                            VALUES (%s, %s, %s)
	                                            ",
	                                            intval($product_id),
	                                            sanitize_text_field($chk[0]),
	                                            stripslashes($chk[1])
	                                            
	                                            )
	                                       );
	                                
	                                
	                                
	                                }
	                            
						}

        			} else 
        			{
				if($_POST['producttabs']!='')
					{
						
                        foreach ($_POST['producttabs'] as $ptabs) {
                         	
                                
                                $chk = explode('-_-',$ptabs);
	                                $result = $wpdb->query( 
	                                    $wpdb->prepare( 
	                                            "
	                                            INSERT INTO ".$wpdb->prefix . "fmet_product_tabs
	                                            (product_id, tab_name, tab_description)
	                                            VALUES (%s, %s, %s)
	                                            ",
	                                            intval($productid),
	                                            sanitize_text_field($chk[0]),
	                                            stripslashes($chk[1])
	                                            
	                                            )
	                                       );
                                
                                
                                }
                            
					}
				}
				$date = date('Y-m-d');
				$ip = $_SERVER['REMOTE_ADDR'];
				$wpdb->query( $wpdb->prepare( "DELETE FROM " .$wpdb->prefix . "fmet_temp_tabs WHERE date = %s AND ip = %s",  $date, $ip) );
				die();
				return true;
			}


			

			

			function getProductTabs($product_id)
			{
				global $wpdb;
	           	$result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix . "fmet_product_tabs WHERE product_id = %d",  $product_id) ); 

	           	return $result;
			}

			


			
			function tab_session_html()
			{ 
				
				if(isset($_GET['action']) && $_GET['action']=='edit')
	        		{
	        			if(isset($_GET['post']) && $_GET['post']!='')
	        			{
	        				$product_id = intval($_GET['post']);
	        			} else $product_id = 0;

	        			$protabs = $this->getProductTabs($product_id);
	        			foreach($protabs as $protab)
	        			{ ?>

	        		<div class="ftab" id="ftab<?php echo $protab->tab_id; ?>">
	           			<span class="ftext"><?php echo $protab->tab_name; ?></span>
	           			<span class="preview button" onClick="javascript:del('<?php echo $protab->tab_id; ?>','<?php echo $protab->tab_name; ?>')">Remove</span>
	           			<span class="preview">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
	           			<span class="button preview " onClick="javascript:editprotab('<?php echo $protab->tab_id; ?>')">Edit</span>
	           			<textarea name="ptabid[]" style="display:none"><?php echo $protab->tab_name; ?>-_-<?php echo stripslashes($protab->tab_description); ?></textarea>
	           		</div>

	        		<?php } }

	        		if(isset($_POST['edit']) && $_POST['edit']=='edit')
	        		{
	        			if(isset($_POST['post']) && $_POST['post']!='')
	        			{
	        				$product_id = intval($_POST['post']);
	        			} else $product_id = 0;

	        			$protabs = $this->getProductTabs($product_id);
	        			foreach($protabs as $protab)
	        			{ ?>

	        		<div class="ftab" id="ftab<?php echo $protab->tab_id; ?>">
	           			<span class="ftext"><?php echo $protab->tab_name; ?></span>
	           			<span class="preview button" onClick="javascript:del('<?php echo $protab->tab_id; ?>','<?php echo $protab->tab_name; ?>')">Remove</span>
	           			<span class="preview">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
	           			<span class="button preview " onClick="javascript:editprotab('<?php echo $protab->tab_id; ?>')">Edit</span>
	           			<textarea name="ptabid[]" style="display:none"><?php echo $protab->tab_name; ?>-_-<?php echo stripslashes($protab->tab_description); ?></textarea>
	           		</div>

	        		<?php } }

				$all_tabs = $this->get_all_session_tabs();
	           		 if($all_tabs!='') {
	           		 foreach ($all_tabs as $tab) { ?>

	           		 <div class="ftab" id="ftab<?php echo $tab->tab_id; ?>">
	           			<span class="ftext"><?php echo $tab->tab_name; ?></span>
	           			<span class="preview button" onClick="javascript:del('<?php echo $tab->tab_id; ?>','<?php echo $tab->tab_name; ?>')">Remove</span>
	           			<span class="preview">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
	           			<span class="button preview " onClick="javascript:edittab('<?php echo $tab->tab_id; ?>')">Edit</span>
	           			<textarea name="ptabid[]" style="display:none"><?php echo $tab->tab_name; ?>-_-<?php echo stripslashes($tab->tab_description); ?></textarea>
	           			
	           		</div>
	           		 
	           	<?php } }


			} 

			

			

	        function add_more_js() {
        ?>

        <script>

        function openpopup() {

			jQuery("#my_modal").show();
			jQuery('#tabtitle').val('');
	        jQuery('#tab_id').val('');
	        if (jQuery("#wp-addfmetabcontent-wrap").hasClass("tmce-active")){
	                  
	              tinymce.get('addfmetabcontent').setContent(' ');
	          } else {

	            jQuery('#addfmetabcontent').val(' ');
	          }
		}

        	jQuery(document).ready(function(){

        	 jQuery("#submit").click(function(){ 
			      var title = jQuery("#tabtitle").val();
			      var tab_id = jQuery('#tab_id').val();
			      if (jQuery("#wp-addfmetabcontent-wrap").hasClass("tmce-active")){
			                  
			            var desc = tinymce.get('addfmetabcontent').getContent(); 
			        } else {

			          var desc = jQuery('#addfmetabcontent').val();
			        }
			      var ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';
			      var edit = '<?php echo $_GET["action"] ?>';
			      var post = '<?php echo $_GET["post"] ?>';
			      jQuery.ajax({
			      type: 'POST',   // Adding Post method
			      url: ajaxurl, // Including ajax file
			      data: {"action": "tab_session", "title":title, "content":desc, "tab_id":tab_id, "edit":edit, "post":post, "addsecurity":'<?php echo wp_create_nonce( "fmetabs-add-ajax-nonce" ); ?>'}, // Sending data dname to post_word_count function.
			      success: function(data){ // Show returned data using the function.
			        jQuery('#p_tabs').html(data);
			        jQuery('#tabtitle').val('');
			        jQuery('#tab_id').val('');
			        if (jQuery("#wp-addfmetabcontent-wrap").hasClass("tmce-active")){
			                  
			              tinymce.get('addfmetabcontent').setContent(' ');
			          } else {

			            jQuery('#addfmetabcontent').val(' ');
			          }
			        jQuery("#my_modal").hide();

			      }
			      });
		      });



			 jQuery("#cancel").click(function(){ 

		        jQuery('#tabtitle').val('');
		        jQuery('#tab_id').val('');
		        jQuery('#tabstatus').val('');
		        if (jQuery("#wp-addfmetabcontent-wrap").hasClass("tmce-active")){
		                  
		              tinymce.get('addfmetabcontent').setContent(' ');
		          } else {

		            jQuery('#addfmetabcontent').val(' ');
		          }
		        jQuery("#my_modal").hide();
		      });

			});

        </script>


        <script type="text/javascript">
      

        function del(tab_id,tab_name) { 
        var ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';
        if(confirm("Are you sure to delete "+tab_name+" tab?"))
        {
        jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data: {"action": "tab_session_del", "tab_id":tab_id, "delsecurity":'<?php echo wp_create_nonce( "fmetabs-del-ajax-nonce" ); ?>'},
        success: function() {

          jQuery('#ftab'+tab_id).fadeOut('slow');
          jQuery('#ftab'+tab_id).remove();

        }
        });
        
        }
        return false;
        }

        function edittab(tab_id)
        { 
           jQuery("#my_modal").show();
          var ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';
          var edit = '<?php echo $_GET["action"] ?>';
          var post = '<?php echo $_GET["post"] ?>';
          var temptab = 'yes';
          jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: {"action": "tab_session_edit", "tab_id":tab_id, "edit":edit, "post":post, "temptab":temptab, "editsecurity":'<?php echo wp_create_nonce( "fmetabs-edit-ajax-nonce" ); ?>'},
          dataType: 'json',
          success: function(json) {
            jQuery('#tabtitle').val(json['tab_name']);
            jQuery('#tab_id').val(json['tab_id']);
            if (jQuery("#wp-addfmetabcontent-wrap").hasClass("tmce-active")){
                  
                  tinymce.get('addfmetabcontent').setContent(json['tab_description']);
              } else {

                jQuery('#addfmetabcontent').val(json['tab_description']);
              }
          }
          });
        }

        function editprotab(tab_id)
        { 
          jQuery("#my_modal").show();
          var ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';
          var edit = '<?php echo $_GET["action"] ?>';
          var post = '<?php echo $_GET["post"] ?>';
          var temptab = '';
          jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: {"action": "tab_session_edit", "tab_id":tab_id, "edit":edit, "post":post, "temptab":temptab, "editsecurity":'<?php echo wp_create_nonce( "fmetabs-edit-ajax-nonce" ); ?>'},
          dataType: 'json',
          success: function(json) {
            jQuery('#tabtitle').val(json['tab_name']);
            jQuery('#tab_id').val(json['tab_id']);
            if (jQuery("#wp-addfmetabcontent-wrap").hasClass("tmce-active")){
                  
                  tinymce.get('addfmetabcontent').setContent(json['tab_description']);
              } else {

                jQuery('#addfmetabcontent').val(json['tab_description']);
              }
          }
          });
        }

        

        

      
          jQuery('#publish').click(function() { 
            var globaltabs = [];
            var ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';
            var edit = '<?php echo $_GET["action"] ?>';
            var post = '<?php echo $_GET["post"] ?>';
            jQuery('#pgtabs :checked').each(function() {
                globaltabs.push(jQuery(this).val());
            });
            

            var producttabs = [];
            jQuery('textarea[name^="ptabid"]').each(function() {
                producttabs.push(jQuery(this).val());
            });
            
            jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {"action": "ptab_submit", "producttabs":producttabs, "edit":edit, "post":post, "subsecurity":'<?php echo wp_create_nonce( "fmetabs-sub-ajax-nonce" ); ?>'},
            success: function(data) {
            }
            });

            

          });

        
      
    </script>

		


        <?php  }



        
	}

	$fmet = new FME_Tabs();
}
?>