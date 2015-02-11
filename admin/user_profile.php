<?php

/**
 * 		Social Profiles
 * 	
 * 	Add social profiles fields at user profile window
 *
 * @since version 1.4.
 */
add_action( 'show_user_profile', 'mgpc_user_profile_fields' );
add_action( 'edit_user_profile', 'mgpc_user_profile_fields' );
function mgpc_user_profile_fields( $user ) { 

	global $mgpc;

	$profile_status = $mgpc['enable-social-profile-links'];
	if(isset($profile_status) && $profile_status!=0) {
		$get_profiles = $mgpc['mgpc_social_profiles'];
		if(isset($get_profiles) && is_array($get_profiles)) { ?>
		<h3>MGPC - Social Links</h3>
		<table class="form-table">
		<?php	foreach ($get_profiles as $profile => $val) {
					if($val) { ?>
						<tr>
							<?php echo "<th><label for='mgpc_social_link_". $profile ."'>". ucfirst($profile) ."</label></th>"; ?>
							<td>
								<input type="url" name='mgpc_social_link_<?php echo $profile; ?>' id='mgpc_social_link_<?php echo $profile; ?>' value="<?php echo esc_attr( get_the_author_meta( 'mgpc_social_link_'. $profile, $user->ID ) ); ?>" class="regular-text" /><br />
								<?php printf("<span class='description'>Please enter your %s username.</span>", ucfirst($profile)); ?>
							</td> 
						</tr>
		<?php 		}
				} ?>
		</table>
		<?php
		} 
	}

	/**
	 *		Profile Image
	 * 
	 *	Add profile image uploader at user profile window.
	 */
	$image_status = $mgpc['enable-profile-image-option'];
	if(isset($image_status) && $image_status!=0) {
	?>
		<h3>MGPC - Profile Images</h3>
			<style type="text/css">
			.fh-profile-upload-options th,
			.fh-profile-upload-options td,
			.fh-profile-upload-options input {
				vertical-align: top;
			}
			.user-preview-image {
				display: block;
				height: auto;
				width: 300px;
			}
		</style>
		<table class="form-table fh-profile-upload-options">
			<tr>
				<th>
					<label for="image">Image/Avatar</label>
				</th>
				<td>
					<img class="user-preview-image" src="<?php echo esc_attr( get_the_author_meta( 'mgpc_original_pic', $user->ID ) ); ?>">
					<input type="text" name="mgpc_original_pic" id="mgpc_original_pic" value="<?php echo esc_attr( get_the_author_meta( 'mgpc_original_pic', $user->ID ) ); ?>" class="regular-text" />
					<input type='button' class="button-primary" value="Upload Image" id="mgpc_uploadimage"/><br />
					<span class="description">Please upload an image for your profile.</span>
				</td>
			</tr>
			<?php /*
			<tr>
				<th>
					<label for="image">Thumbnail Pic</label>
				</th>
				<td>
					<img class="user-preview-image" src="<?php echo esc_attr( get_the_author_meta( 'mgpc_thumb_pic', $user->ID ) ); ?>">
					<input type="text" name="mgpc_thumb_pic" id="mgpc_thumb_pic" value="<?php echo esc_attr( get_the_author_meta( 'mgpc_thumb_pic', $user->ID ) ); ?>" class="regular-text" />
					<input type='button' class="button-primary" value="Upload Image" id="mgpc_sidebarUploadimage"/><br />
					<span class="description">Please upload an image for the sidebar.</span>
				</td>
			</tr>
			*/	?>
		</table>
		<script type="text/javascript">
			(function( $ ) {
				jQuery( '#mgpc_uploadimage' ).on( 'click', function() {
					tb_show('Upload Main Avatar Image:', 'media-upload.php?type=image&TB_iframe=1');
					window.send_to_editor = function( html ) 
					{
						imgurl = jQuery( 'img',html ).attr( 'src' );
						jQuery( '#mgpc_original_pic' ).val(imgurl);
						tb_remove();
					}
					return false;
				});
				/*jQuery( '#mgpc_sidebarUploadimage' ).on('click', function() {
					tb_show('Upload Thumbnail Avatar Image:', 'media-upload.php?type=image&TB_iframe=true');
					window.send_to_editor = function( html ) 
					{
						imgurl = jQuery( 'img', html ).attr( 'src' );
						jQuery( '#mgpc_thumb_pic' ).val(imgurl);
						tb_remove();
					}
					return false;
				});*/
			})(jQuery);
		</script>
<?php
	}
}

//	Saving data (Additional social links, Uploaded profile image)
add_action( 'personal_options_update', 'mgpc_save_user_profile_fields' );
add_action( 'edit_user_profile_update', 'mgpc_save_user_profile_fields' );
function mgpc_save_user_profile_fields( $user_id ) {
	
	//	get profiles from array
	global $mgpc;
	$get_profiles = $mgpc['mgpc_social_profiles'];

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	if(isset($get_profiles)) {
		foreach($get_profiles as $key => $value) {
			$getLink = 'mgpc_social_link_'. $key;
			update_usermeta( $user_id, esc_attr( $getLink ), $_POST[$getLink] );
		}
	}
	update_user_meta( $user_id, 'mgpc_original_pic', $_POST[ 'mgpc_original_pic' ] );
	update_user_meta( $user_id, 'mgpc_thumb_pic', $_POST[ 'mgpc_thumb_pic' ] );
}

//	Enqueue upload image scripts
add_action( 'admin_enqueue_scripts', 'mgms_author_image_enqueue_admin' );
function mgms_author_image_enqueue_admin()
{
	wp_enqueue_script( 'thickbox' );
	wp_enqueue_style('thickbox');
	wp_enqueue_script('media-upload');
}

?>