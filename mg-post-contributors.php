<?php

/**
 * The MG Post Contributor Plugin
 *
 * Plugin Name:     MG POST Contributors
 * Plugin URI:      http://mgwebthemes.com
 * Github URI:      https://github.com/maheshwaghmare/mg-post-contributors
 * Description:     MG Post Contributors helps Admin users to set multiple authors for single post. Simply selecting authors check boxes at Post Editor. It show list of users with checkboxes and show them at POST. Getting started <strong> 1) </strong> Click 'Activate'  <strong> 2)</strong>  Go to  POST->Add New OR Select existing one i.e. POST->All Posts and select Post <strong> 3) </strong> Choose  'Contributors' and click 'Publish'. To check result just click View Post. We also provide <strong>['mg-post-contributors']</strong> shortcode for sidebars to show contributors in list format.
 * Author:          Mahesh Waghmare
 * Author URI:      http://mgwebthemes.com
 * Version:         1.3.
 * License:         GPL2+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @author          Mahesh M. Waghmare <mwaghmare7@gmail.com>
 * @license         GNU General Public License, version 2
 * @copyright       2014 MG Web Themes
 */

 /**
 * Register new extentions
 *
 * @since MG Contributors 1.4.
 */
//require_once('framework/loader.php');

 /**
 * Register user fields
 *
 * @since MG Contributors 1.3.
 */
require_once('admin/actions.php');
require_once('admin/settings.php');


 /**
 * Initialize meta box setup functions
 *
 * @since MG Contributors 1.0
 */

// 	Init Theme Options framework via ReduxFramework
require_once('framework/core/framework.php');
require_once('framework/settings/mg-config.php');



 /**
 * Register user fields
 *
 * @since MG Contributors 1.1
 */

// apply_filters('mgms_enable_social_profile_links', false);
 require_once('admin/user_profile.php');


// add meta box actions
add_action( 'load-post.php', 'mg_contributor_metabox_setup' );
add_action( 'load-post-new.php', 'mg_contributor_metabox_setup' );


	 /**
	 * Add meta box hooks (add_meta_boxes, save_post)
	 *
	 * @since MG Contributors 1.0
	 */
	function mg_contributor_metabox_setup() {

		// 		'add_meta_boxes' hook
		add_action( 'add_meta_boxes', 'mg_add_contributor_post_meta_boxes' );

		// 		'save_post' hook
		add_action( 'save_post', 'mg_save_contributorpost_class_meta', 10, 2 );
	}


	 /**
	 * ('add_meta_boxes') HOOK functions definition to add new meta box
	 *
	 * @since MG Contributors 1.0
	 **/

	// Add new meta box
	function mg_add_contributor_post_meta_boxes() {

		add_meta_box(
			'mg-contributor-class',								// Unique ID
			esc_html__( 'MG Contributors', 'contributors' ),	// Title
			'mg_contributor_post_class_meta_box',				// Callback function
			'post',												// Admin page (or post type)
			'side',												// Context
			'default'											// Priority
		);
	}


	 /**
	 * call back function of ('add_meta_boxes') to generate meta box structure (labels, list of contributors)
	 *
	 * @since MG Contributors 1.0
	 */

	// Show meta box structure
	function mg_contributor_post_class_meta_box( $object, $box ) { ?>
		<?php wp_nonce_field( basename( __FILE__ ), 'mg_post_class_nonce' ); ?>

		<?php
				//	Get ALL CONTRIBUTORS from DB 
				$post_id = get_the_ID();
				$contributors = get_post_meta( $post_id, 'mgpc_contributors', true );
				$enable_value = get_post_meta( $post_id, 'enable-contributors', true );

				if(empty($enable_value)) {
					$checked = "checked";
				} else if( !empty($enable_value) && $enable_value =="on") { 
					$checked = "checked";
				} else {
					$checked = "";
				}
				?>
		<label for="enabled-status"><small>Do you want to show list? <i>Default: Enable</i></small></label>
		<h3 for="enable-contributors" class="selectit"><input name="enable-contributors" type="checkbox" id="enable-contributors" <?php echo $checked; ?> > Enable List?</h3>
		<label for="current-status"><small style="font-size: 9px;">Current Status:
			<?php 
				if($checked =="checked"){
					echo '<span style="color: #C6F8C1;background: green;border-radius: 3px;font-size: 9px;padding: 0px 4px;">Enable</span>';
				} else {
					echo '<span style="color: #F1EBEB;background: rgb(255, 58, 58);border-radius: 3px;font-size: 9px;padding: 0px 4px;"> Disabled </span>';
				}
		?>
		</small></label>

		<h3 for="mg-contributor-class"><?php _e( "# Select Contributors of this Post.", 'mgpc' ); ?></h3>		
		
			<?php 

				global $wp_roles, $mgpc;
				$roles = $wp_roles->get_names();

				// Show users order by GROUP
				foreach($roles as $role) 
				{
					//	set EMPTY exclude to get all list
					$excludes ="";
					//	Check excludes ites are not empty from admin panel
					if(isset($mgpc['exclude-roles'])) {
						$excludes = $mgpc['exclude-roles'];
					}
					
					if(is_array($excludes) && !empty($excludes)) {
						if(!in_array( strtolower($role), $excludes)) {
							//	pass not excluded role to @function show_included_contributor()
							//	@variable $role: excluded
							show_included_contributor($role);
						}
					} else {
						//	Pass regular variable $role
						//	@variable $role: regular ALL
						show_included_contributor($role);
					}



					/*?>
					<h3><?php echo $role;?></h3>
					<?php 
					
					$blogusers = get_users('blog_id=1&orderby=nicename&role=' .$role );
					
					foreach ($blogusers as $user) 
					{
						// Check CONTRIBUTTORS already SET or NOT SET
						if(is_array($contributors))
						{
							if (in_array( $user->ID, $contributors)) 
							{
								echo '<label class="selectit" for="'.$user->ID.'"><input type="checkbox" checked="checked" value="'.$user->ID.'" id="mgpc_contributors" name="mgpc_contributors[]"> '.ucfirst($user->user_nicename).' </label><br />';
							}
							else 
							{
								echo '<label class="selectit" for="'.$user->ID.'"><input type="checkbox" value="'.$user->ID.'" id="mgpc_contributors" name="mgpc_contributors[]"> '.ucfirst($user->user_nicename).' </label><br />';
							}
						}
						else 
						{
							echo '<label class="selectit" for="'.$user->ID.'"><input type="checkbox" value="'.$user->ID.'" id="mgpc_contributors" name="mgpc_contributors[]"> '.ucfirst($user->user_nicename).' </label><br />';
						}
					}
					?>
					</ul>	
					<?php 	*/
				} 
		}
		// Meta Box structure ENDs


	/**
	 * Show included or excluded single users
	 * @variable $role is either excluded or regular
	 * @since MG Contributors 1.1
	 */

	 function show_included_contributor($role) {
		
		//	Get ALL CONTRIBUTORS from DB
		$post_id = get_the_ID();
		$contributors = get_post_meta( $post_id, 'mgpc_contributors', true );	
		$blogusers = get_users('blog_id=1&orderby=nicename&role=' .$role );

		/**
		* Check blog user role is not empty. Check role has atleas 1 user.
		*
		* @since MG Contributors 1.1
		*/
		if($blogusers) 
		{
			?>
			<h3><?php echo $role;?></h3>
			<?php 
					
			$blogusers = get_users('blog_id=1&orderby=nicename&role=' .$role );
					
			foreach ($blogusers as $user) 
			{
				// Check CONTRIBUTTORS already SET or NOT SET
				if(is_array($contributors))
				{
					if (in_array( $user->ID, $contributors)) 
					{
						echo '<label class="selectit" for="'.$user->ID.'"><input type="checkbox" checked="checked" value="'.$user->ID.'" id="mgpc_contributors" name="mgpc_contributors[]"> '.ucfirst($user->user_nicename).' </label><br />';
					}
					else 
					{
						echo '<label class="selectit" for="'.$user->ID.'"><input type="checkbox" value="'.$user->ID.'" id="mgpc_contributors" name="mgpc_contributors[]"> '.ucfirst($user->user_nicename).' </label><br />';
					}
				}
				else 
				{
					echo '<label class="selectit" for="'.$user->ID.'"><input type="checkbox" value="'.$user->ID.'" id="mgpc_contributors" name="mgpc_contributors[]"> '.ucfirst($user->user_nicename).' </label><br />';
				}
			}
		} 
	}	// @function show_included_contributor END






	 /**
	 * ('save_post') HOOK functions definition to save meta box values
	 *
	 * @since MG Contributors 1.0
	 */

	//	Save meta box values
	function mg_save_contributorpost_class_meta( $post_id, $post ) {

		// Verify the post before proceeding
		if ( !isset( $_POST['mg_post_class_nonce'] ) || !wp_verify_nonce( $_POST['mg_post_class_nonce'], basename( __FILE__ ) ) )
			return $post_id;
			
		// Get the post type object
		$post_type = get_post_type_object( $post->post_type );

		// Check if the current user has permission to edit the post
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;

		// Get the posted data and sanitize it for use as an HTML class
		$new_meta_value = ( isset( $_POST['mg-contributor-class'] ) ? sanitize_html_class( $_POST['mg-contributor-class'] ) : '' );
		$enable_contributors_value = ( isset( $_POST['enable-contributors'] ) ? sanitize_html_class( $_POST['enable-contributors'] ) : 'off' );

		//	Check post values of contributors	
		if( isset( $_POST['mgpc_contributors'] ) )
		{
			$new_meta_value = array();
			
			 /**
			 * Generate Contributor array 
			 * save to 'mgpc_contributors' meta_key
			 * to see check array list within 'wp_postmeta' -> meta_key 'mgpc_contributors' 
			 *
			 * @since MG Contributors 1.0
			 */
				foreach($_POST['mgpc_contributors'] as $checkbox){
					array_push($new_meta_value, $checkbox);
				}
		}	
			
		
		//	 Set the meta key
		$meta_key 	= 'mgpc_contributors';
		
		// Get meta value 'mgpc_contributors' meta_key
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		// ADD NEW values if not exist
		if ( $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );

		// UPDATE it if exist
		elseif ( $new_meta_value && $new_meta_value != $meta_value )
			update_post_meta( $post_id, $meta_key, $new_meta_value );

		// If there is no new meta value but an old value exists, DELETE it
		elseif ( '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, $meta_key, $meta_value );



		//	 Set the meta key
		$enable 	= 'enable-contributors';
		
		// Get meta value 'enable-contributors' enable
		$enable_value = get_post_meta( $post_id, $enable, true );

		// ADD NEW values if not exist
		if ( $enable_contributors_value && '' == $enable_value )
			add_post_meta( $post_id, $enable, $enable_contributors_value, true );

		// UPDATE it if exist
		elseif ( $enable_contributors_value && $enable_contributors_value != $enable_value )
			update_post_meta( $post_id, $enable, $enable_contributors_value );

		// If there is no new meta value but an old value exists, DELETE it
		elseif ( '' == $enable_contributors_value && $enable_value )
			delete_post_meta( $post_id, $enable, $enable_value );
	}


/**
 * Custom CSS
 *
 * @since MG Contributors 1.2
 */
add_action('wp_head','mgpc_custom_css_to_header');
function mgpc_custom_css_to_header() {
	global $mgpc;
	$outputCSS  = "<style type='text/css'>";
	if(isset($mgpc['mgpc-additional-code-css'])) :
		$CustomCSS = $mgpc['mgpc-additional-code-css'];
		if(!empty($CustomCSS) ) {
			$outputCSS .= $mgpc['mgpc-additional-code-css'];
		}
	endif;
	$outputCSS .= "</style>";
	echo $outputCSS;
}


/**
 * Custom JS
 *
 * @since MG Contributors 1.2
 */
add_action('wp_head','mgpc_custom_js_to_header');
function mgpc_custom_js_to_header() {
	global $mgpc;
	$outputJS  = "<script type='text/javascript'>";
	if(isset($mgpc['mgpc-additional-code-js'])) :
		$CustomCSS = $mgpc['mgpc-additional-code-js'];
		if(!empty($CustomCSS) ) {
			$outputJS .= $mgpc['mgpc-additional-code-js'];
		}
	endif;
	$outputJS .= "</script>";
	echo $outputJS;
}


/**
 * Custom HTML - BEFORE
 *
 * @since MG Contributors 1.2
 */
function mgpc_custom_html_before() {
	global $mgpc;
	if(!empty($mgpc['mgpc-additional-code-html-before'])) {
		return $mgpc['mgpc-additional-code-html-before'];
	}
}

/**
 * Custom HTML - BEFORE
 *
 * @since MG Contributors 1.2
 */
function mgpc_custom_html_after() {
	global $mgpc;
	if(!empty($mgpc['mgpc-additional-code-html-after'])) {
		return $mgpc['mgpc-additional-code-html-after'];
	}
}



 /**
 * Add Filter to generate contributors list
 * show contributors list after POST->CONTENTS
 *
 * @since MG Contributors 1.1
 */

add_filter( 'the_content', 'show_contributors_after_post_contents' );	 	

 /**
 * Show contributors list
 *
 * @since MG Contributors 1.0
 */
  
//	generate contributors list 
function show_contributors_after_post_contents($content) {

	global $mgpc;

	// assuming you have created a page/post entitled 'debug'	
	if ($GLOBALS['post']->post_name == 'debug') {
		return var_export($GLOBALS['post'], TRUE );
	}
  
	//	Get POST ID
	$post_id = get_the_ID();

	// Check post id is not EMPTY
	if ( !empty( $post_id ) ) {
		
		// Assign 'wp_postmeta' -> meta_key ('mgpc_contributors') to variable
		$enable_value = get_post_meta( $post_id, 'enable-contributors', true );
		$contributors = get_post_meta( $post_id, 'mgpc_contributors', true );
	}
	
	//	Avoid from blog page [Show only if post is opened]
	if(!is_singular('post')) {
		return $content;
	}	

	//Get Post Contetns
	$content_post = get_post( $post_id );
	$content = $content_post->post_content;

	if(!empty($enable_value) && $enable_value =="on" ) {

		//	Check meta_key ('mgpc_contributors') is not EMPTY
		if(isset($contributors) && !empty($contributors)) {

			if($contributors != '') {
				$show_contributors    = "";

				/**
				 * Enable Carouse slider of contributors
				 *
				 * @since MG Contributors 1.3
				 */
					//	Set data attributes for carousel
					$data_enable = $data_items = $data_slidespeed = $data_autoplay = $data_stoponhover = $data_navigation = $data_pagination = $data_responsive = '';
					global $mgpc;

					if($mgpc['enable-carousel-list']) {
						wp_enqueue_style( 'mgpc_owl_carousel_css');
						wp_enqueue_style( 'mgpc_owl_carousel_theme');
						wp_enqueue_script( 'mgpc_owl_carousel_js');


						do_action('mgpc_show_carousel');
						//	IMP
						//wp_enqueue_script( 'mgpc_carousel_op' );
						/*$data_enable = 'data-enable="1"';				
						if($mgpc['carousel-items']!='') 	  {	$data_items = 'data-items="'. $mgpc['carousel-items']. '"';	}
						if($mgpc['carousel-slidespeed']!='')  {	$data_slidespeed = 'data-slidespeed="'. $mgpc['carousel-slidespeed']. '"';	}
						if($mgpc['carousel-autoplay']!='') 	  {	$data_autoplay = 'data-autoplay="'. $mgpc['carousel-autoplay']. '"';	}
						if($mgpc['carousel-stoponhover']!='') {	$data_stoponhover = 'data-stoponhover="'. $mgpc['carousel-stoponhover']. '"';	}
						if($mgpc['carousel-navigation']!='')  {	$data_navigation = 'data-navigation="'. $mgpc['carousel-navigation']. '"';	}
						if($mgpc['carousel-pagination']!='')  {	$data_pagination = 'data-pagination="'. $mgpc['carousel-pagination']. '"';	}
						if($mgpc['carousel-responsive']!='')  {	$data_responsive = 'data-responsive="'. $mgpc['carousel-responsive']. '"';	}
*/
					}


				/**
				 * Print Custom CSS, JS, HTML [Before]
				 *
				 * @since MG Contributors 1.2
				 */
				do_action('mgpc_custom_css_to_header');
				do_action('mgpc_custom_js_to_header');
				$show_contributors .= mgpc_custom_html_before();
				// add custom code CSS, JS, HTML BEFORE

				$show_contributors   .= 	"<div id='mgpc-wrapper'>";
				$show_contributors   .= 	"	<div id='mgpc'  " .$data_enable. " " .$data_items. " " .$data_slidespeed. " " .$data_autoplay. " " .$data_stoponhover. " " .$data_navigation. " " .$data_pagination. " " .$data_responsive. ">";
						
						if(isset($mgpc['enable-label'])) {
							if($mgpc['enable-label']) {
								if(isset($mgpc['enable-label-text']) && !empty($mgpc['enable-label-text'])) {
									$show_contributors  .= 	"<div class='mgpc-title'><h3 class='title'>". $mgpc['enable-label-text'] ."</h3></div>";
								} else {
									$show_contributors  .= 	"<div class='mgpc-title'><h3 class='title'> Contributors: </h3></div>";
								}
							}
						}

				$show_contributors  .= 	"<div class='mgpc-list-wrapper'><ul class='mgpc-list' id='mgpc-list-carousel'>";
				
				foreach($contributors as $user_id)
				{
					/**
					 * Check Excluded Roles and skip those authors from list
					 * SKIP] Excluded author roles
					 * @excludes
					 * @since MG Contributors 1.2
					 */
					//	set EMPTY exclude to get all list
					global $mgpc;
					$excludes ="";
					$author_meta = get_userdata( $user_id );	//	Get user details by using $user_id
					$author_roles = $author_meta->roles;		// get author roles

					//	Check excludes ites are not empty from admin panel
					if(isset($mgpc['exclude-roles'])) {
						$excludes = $mgpc['exclude-roles'];
					}
					
					if(is_array($excludes) && !empty($excludes)) {
						
						//	Check all author roles step by step
						foreach($author_roles as $role) {
							if(in_array( strtolower($role), $excludes)) {
								//	pass not excluded role to @function show_included_contributor_list()
								//	@variable $role: excluded
								break;
							} else {
								//	Pass regular variable $role
								//	@variable $role: regular ALL
								//	pass @user_id, @show_contributors
								$show_contributors .= show_included_contributor_list($user_id);
							}
						}

					} else {
						//	Pass regular variable $role
						//	@variable $role: regular ALL

						$show_contributors .= show_included_contributor_list($user_id);
					}
				}

				$show_contributors	.=	"			</ul><!-- .mgpc-list -->";
				$show_contributors	.=	"		</div><!-- .author-block -->";
				//$show_contributors	.=	"		</div><!-- .author-block-wrapper -->";
				$show_contributors	.=	"	</div><!-- .mgpc --> ";
				$show_contributors	.=	"</div><!-- .mgpc-wrapper -->";

				/**
				 * Print Custom HTML [After]
				 *
				 * @since MG Contributors 1.2
				 */
				$show_contributors .= mgpc_custom_html_after();

				return $content . $show_contributors;
			}
		} else {
			return $content;
		}
	}
	else {
		return $content;
	}
}


/**
* @single-author
*	Show single author SKIP excluded role authors
*
* @since MG Contributors 1.2
*/
function show_included_contributor_list($user_id) {
	global $mgpc;
	/*print_r($mgpc['opt-slides']);*/
	$show_contributors = "";
	//	Get Gravators of Contributor
		
		$user_avatar = get_avatar( $user_id,  $size = '100'); 

		//	Get user details by using $user_id
		$user_info = get_userdata( $user_id );

		$desc 			= get_the_author_meta( 'description', $user_id );
		$author_email 	= get_the_author_meta( 'user_email', $user_id );
		$author_website = get_the_author_meta( 'user_url', $user_id );

		
		$user_name = $user_info->user_firstname. " " .$user_info->user_lastname;
		
		if($user_name==" " || empty($user_name)) {
			$user_name = $user_info->user_nicename;
		}

		$show_contributors  .= 	"<li class='mgpc-author item' >";

		 /**
		 * @basic-settings
		 *	Image Block
		 *
		 * @since MG Contributors 1.1
		 */
		if ($mgpc['enable-block-image']) :
			$show_contributors  .=  "<div class='mgpc-block image-block-wrapper' >";
			$show_contributors  .=  "	<div class='image-block' >";

			/**
			 *	Check custom avatar image set or not
			 *	if yes set uploaded image else use avatar.
			 * @since MG Contributors 1.3.
			 */
			$imgUrl = get_the_author_meta( 'mgpc_original_pic', $user_id );
			if($imgUrl) {
				$show_contributors  .=	"<img src='" .$imgUrl. "' />";
			} else {
				$show_contributors  .= 			$user_avatar;
			}

			$show_contributors  .=  "	</div><!-- image-block -->";
			$show_contributors  .=  "</div><!-- image-block-wrapper -->";
		endif;	//	.image-block


		 /**
		 * @basic-settings
		 * 	Meta Block
		 *
		 * @since MG Contributors 1.1
		 */
		 if ($mgpc['enable-block-meta']) :


		 	//	Check view Horizontal / Verticle
		 	$view = "";
		 	$spacing = "";

		 	if(isset($mgpc['author-block-view'])){
		 		if($mgpc['author-block-view']==2) {
		 			$view = "verticle-block";
		 			$spacing = "verticle-spacing";
		 		}  else if(!$mgpc['enable-block-image']) {
				 	$view = "horizontal-block";
			 	} else {
					$view = "horizontal-block";
		 			$spacing = "horizontal-spacing";	
			 	}
			}

			$show_contributors  .=  "<div class='mgpc-block author-block-wrapper ". $view ." ' >";
			$show_contributors  .=  "	<div class='author-block " .$spacing. "' >";

			// 	Name
			if($mgpc['enable-meta-name']) :
				$show_contributors	.=	" 	<h4 class='author-name'>" .ucfirst($user_name). "</h4>";
			endif; 

			//	Role
			if($mgpc['enable-meta-role']) :
				$show_contributors	.=	" 	<h5 class='author-role'>" .ucfirst($user_info->roles[0]). "</h5>";
			endif;



				//	Bio
				if($mgpc['enable-meta-bio']) :
					$show_contributors	.=	"	<p class='description'>" .$desc. " </p>";
				endif;

				//	Email 
				if($mgpc['enable-meta-email'] && $author_email != '') :
					$show_contributors	.=  "	<p class='email'>";

					//	Hide Icon Font if it disable
					if(isset($mgpc['enable-email-iconfont'])) {
						if($mgpc['enable-email-iconfont']==1) {
							$show_contributors	.=  "<i class='mgpc-icon fa fa-envelope-o'> </i>";
						}
					} else {
						$show_contributors	.=  "<i class='mgpc-icon fa fa-envelope-o'> </i>";
					}
					$show_contributors	.=  $author_email ."</p>";
					
				endif;

				// Website
				if($mgpc['enable-meta-website'] && $author_website != '') :
					$show_contributors	.=  "<p class='website'>";

						//	Hide Icon Font if it disable
						if(isset($mgpc['enable-website-iconfont'])) {
							if($mgpc['enable-website-iconfont']==1) {
								$show_contributors	.=  "<i class='mgpc-icon fa fa-globe'> </i>";
							}
						} else {
							$show_contributors	.=  "<i class='mgpc-icon fa fa-globe'> </i>";
						}
						$show_contributors	.=	$author_website ."</p>";
				endif;


			//	Show social links
			if($mgpc['enable-meta-social-links']) :
				$show_contributors	.=	"<div class='social-links-wrapper'>";						
				$show_contributors	.=	"	<ul class='social-links'>";

					//	get social profiles from admin panel
					$profile_status = $mgpc['enable-social-profile-links'];
					if(isset($profile_status) && $profile_status!=0) {
						$get_profiles = $mgpc['mgpc_social_profiles'];
						if(is_array($get_profiles) && !empty($get_profiles)) {
							foreach ($get_profiles as $key => $value) {
								if($value) {
									$activeLink = get_the_author_meta( 'mgpc_social_link_' .$key, $user_id );
									if(!empty($activeLink)) {
										$show_contributors	.=  "<li class='mgpc-social-link ". $key ."'><a href='". $activeLink ."'><i class='mgpc-icon fa fa-". $key ."'></i></a></li>";
									}
								}
							}
						}
					}

					//	get profiles from array
					/*global $profiles;
					echo '<pre>';
					print_r($profiles);
					echo '</pre>';*/
						
					/*foreach($profiles as $socialProfile) {
						$activeLink = get_the_author_meta( 'mgpc_social_link_' .$socialProfile, $user_id );
						if($activeLink) {
							$show_contributors	.=  "<li class='mgpc-social-link ". $socialProfile ."'><a href='". $activeLink ."'><i class='mgpc-icon fa fa-". $socialProfile ."'></i></a></li>";
						}
					}*/
				$show_contributors	.=  "	</ul>";
				$show_contributors	.=  "</div>";
			endif; //	.social links


		endif; //	.meta-block

		$show_contributors	.=	"</li>";

		return $show_contributors;
}	// .Show single author



/**
 * Enqueue scripts and styles for front-end.
 * Loads style
 */
function mg_contributor_style() {
	wp_enqueue_style( 'mgpc_default_css', plugins_url( '/css/style.css', __FILE__ ) );
	wp_enqueue_style( 'mgpc_dynamic_css', plugins_url( '/framework/settings/style.css', __FILE__ ) );
	wp_enqueue_style( 'mgpc_dynamic_fontawesome', plugins_url( '/css/font-awesome-4.0.3/css/font-awesome.min.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'mg_contributor_style' );

function mgpc_carousal() {
	wp_register_style( 'mgpc_owl_carousel_css', plugins_url( '/carousal/owl.carousel.css', __FILE__ ) );
	wp_register_style( 'mgpc_owl_carousel_theme', plugins_url( '/carousal/owl.theme.css', __FILE__ ) );
	wp_register_script( 'mgpc_owl_carousel_js', plugins_url( '/carousal/owl.carousel.min.js', __FILE__ ) , array(), '1.0', true );
}
add_action( 'wp_enqueue_scripts', 'mgpc_carousal' );


//  enqueue scripts for image upload
/*add_action( 'admin_enqueue_scripts', 'mgms_enqueue_admin_rating' );
function mgms_enqueue_admin_rating()
{
	wp_enqueue_script( 'mgpc_rating_js', plugins_url( '/js/rating.js', __FILE__ ) , array(), '1.0', true );
	wp_enqueue_style( 'mgpc_rating_css', plugins_url( '/css/rating.css', __FILE__ ) );
}*/

add_action( 'admin_enqueue_scripts', 'mgms_enqueue_admin_styling' );
function mgms_enqueue_admin_styling()
{
	wp_enqueue_style( 'mgms_admin_style', plugins_url( '/admin/mgms-admin.css', __FILE__ ) );
}

//	Show author carousel if enabled.
add_action('mgpc_show_carousel','mgpc_show_carousel_init');
function mgpc_show_carousel_init()
{
	$outputJS   = "<script type='text/javascript'>";
	$outputJS  .= 	'jQuery(document).ready(function() {';
	$outputJS  .= 	'	jQuery("#mgpc-list-carousel").owlCarousel({';
	$outputJS  .= 	'			autoPlay: 3000,';
	$outputJS  .= 	'			items : 1,';
	
/*	$outputJS  .= 	'			itemsDesktop : [1199,1],';
	$outputJS  .= 	'			itemsDesktopSmall : [979,1]';*/
	
	$outputJS  .= 	'			pagination: false,';
	$outputJS  .= 	'			});';
	$outputJS  .= 	'    });';
	$outputJS .= "</script>";
	echo $outputJS;
}

?>