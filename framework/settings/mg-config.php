<?php

if (!class_exists('mgpc')) {

    class mgpc {

        public $args        = array();
        public $sections    = array();
        public $ReduxFramework;

        public function __construct() {

            if (!class_exists('ReduxFramework')) {
                return;
            }

            // This is needed. Bah WordPress bugs.  ;)
            if (  true == Redux_Helpers::isTheme(__FILE__) ) {
                $this->initSettings();
            } else {
                add_action('plugins_loaded', array($this, 'initSettings'), 10);
            }

        }

        public function initSettings() {

            // Set the default arguments
            $this->setArguments();

            // Set a few help tabs so you can see how it's done
            $this->setHelpTabs(); 

            // Create the sections and fields
            $this->setSections();

            if (!isset($this->args['opt_name'])) { // No errors please
                return;
            }

           
            // Function to test the compiler hook and demo CSS output.
            // Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
            add_filter('redux/options/'.$this->args['opt_name'].'/compiler', array( $this, 'compiler_action' ), 10, 3);           

            $this->ReduxFramework = new ReduxFramework($this->sections, $this->args);
        }

        function compiler_action($options, $css, $changed_values) {

			 $filename = dirname(__FILE__) . '/style' . '.css';
              global $wp_filesystem;
              if( empty( $wp_filesystem ) ) {
                require_once( ABSPATH .'/wp-admin/includes/file.php' );
              WP_Filesystem();
              }

              if( $wp_filesystem ) {
                $wp_filesystem->put_contents(
                    $filename,
                    $css,
                    FS_CHMOD_FILE // predefined mode settings for WP files
                );
              }
        }


       

        // Remove the demo link and the notice of integrated demo from the redux-framework plugin
        function remove_demo() {

            // Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
            if (class_exists('ReduxFrameworkPlugin')) {
                remove_filter('plugin_row_meta', array(ReduxFrameworkPlugin::instance(), 'plugin_metalinks'), null, 2);

                // Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
                remove_action('admin_notices', array(ReduxFrameworkPlugin::instance(), 'admin_notices'));
            }
        }

        public function setSections() {

            /**
              Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
             * */
            // Background Patterns Reader

            $mgposttypes = array(
                '1' => 'Posts', 
                '2' => 'Pages', 
            );
            /*
            $sample_patterns_path   = ReduxFramework::$_dir . '../settings/patterns/';
            $sample_patterns_url    = ReduxFramework::$_url . '../settings/patterns/';
            */
            $img_url = ReduxFramework::$_url . '../settings/img/';
            $mgpc_profiles =  array(
                'facebook'     =>   'Facebook',
                'twitter'      =>   'Twitter',
                'google-plus'  =>   'Google Plus',
                'wordpress'    =>   'Wordpress',
                'linkedin'     =>   'Linkedin',
                'youtube'      =>   'Youtube',
                'pinterest'    =>   'Pinterest',
                'instagram'    =>   'Instagram',
                'tumblr'       =>   'Tumblr',
                'flickr'       =>   'Flickr',
                'skype'        =>   'Skype',
            );
            //  Social Links
            /*$social_links = array();

            //  get profiles from array
            global $profiles;
            foreach($profiles as $socialProfile) {
                    $profileLink = ucfirst($socialProfile);
                    array_push($social_links, $profileLink );
            }*/


            /*$this->sections[] = array(
                'icon'      => 'dashicons el-icon-cog',
                'title'     => __('Basic', 'mgpc'),
                'heading'   => __('Basic Settings:', 'mgpc'),
                'desc'      => __('<p class="description">Contributors list always show after the post contents. Change this settings here...</p>', 'mgpc'),
                'fields'    => array(
                    array(
                        'id'       => 'contributors-enable',
                        'type'     => 'switch',
                        'title'    => __('Enable Contributors List?', 'mgpc'),
                        'subtitle' => __('Do you want to enable contributors role? If enable it show Contributors List after post contents. Default: True', 'mgpc'),
                        'default'  => true
                    ),
                ),
            );*/
            




        $this->sections[] = array(
                'icon'      => 'el-icon-cogs',
                'title'     => __('Basic', 'mgpc'),
                'heading'   => __('Basic [HELP] Settings:', 'mgpc'),
/*                'desc'      => __('', 'mgpc'),*/
                'subsection' => false,
                'fields'    => array(
/*                    array(
                        'id' => 'help-metabox',
                        'type' => 'info',
                        'icon' => 'el-icon-info-sign',
                        'style' => 'success',
                        'title' => __('Help : Meta Box Setting', 'mgpc'),
                    ),
*/                  array(
                            'id'        => 'help-exclude-roles',
                            'type'      => 'callback',
                            'title'     => __('Exclude Roles:', 'mgpc'),
                            'subtitle'  => __('Select <small><i>Basic -> Meta Box </i></small>to exclude roles.', 'mgpc'),
                            'callback'  => 'help_exclude_roles'
                    ),
                 ),
            );

            $this->sections[] = array(
                'icon'      => 'el-icon-check-empty',
                'title'     => __('Social Profiles Options', 'mgpc'),
                'subsection' => true,
                'heading'   => __('User Profile Options:', 'mgpc'),
                'desc'      => __('<p class="description">Enable/Disable social profile and Avatar image. </p>', 'mgpc'),
                'fields'    => array(
                    /*array(
                        'id'       => 'first1',
                        'type'     => 'first',
                        'title'    => __('First Element', 'mgpc'),
                        'default'   => 1,
                        'on'        => 'Enabled',
                        'off'       => 'Disabled',
                        'hint'      => array(
                            'title'     => 'Enable / Disable Social Profile Links',
                            'content'   => 'Enable / Disable Social profile links from user profile window. <i>Default: unselected.</i>.',
                        ),
                    ),*/
                    array(
                        'id'       => 'enable-profile-image-option',
                        'type'     => 'switch',
                        'title'    => __('Profile Image?', 'mgpc'),
                        'default'   => 1,
                        'on'        => 'Enabled',
                        'off'       => 'Disabled',
                        'hint'      => array(
                            'title'     => 'Enable / Disable Social Profile Image',
                            'content'   => 'Enable / Disable Social profile image from user profile window. <i>Default: Enabled.</i>.',
                        ),
                    ),
                    array(
                        'id'       => 'enable-social-profile-links',
                        'type'     => 'switch',
                        'title'    => __('Social Profile?', 'mgpc'),
                        'default'   => 1,
                        'on'        => 'Enabled',
                        'off'       => 'Disabled',
                        'hint'      => array(
                            'title'     => 'Enable / Disable Social Profile Links',
                            'content'   => 'Enable / Disable Social profile links from user profile window. <i>Default: unselected.</i>.',
                        ),
                    ),
                    array(
                        'id'        => 'mgpc_social_profiles',
                        'type'      => 'sortable',
                        'mode'      => 'checkbox', // checkbox or text
                        'title'     => __('Available Social Profiles', 'redux-framework-demo'),
                        'options'   => $mgpc_profiles,
                        'hint'      => array(
                            'title'     => 'Add / Remove / Sort Social Profile Links',
                            'content'   => 'Add / Remove / Sort Social profile links from user profile window. <i>Default: unselected.</i>.',
                        ),
                        'required'  => array('enable-social-profile-links', "=", 1),
                    ),
                    /*array(
                            'id'        => 'show-all-social-profile',
                            'type'      => 'callback',
                            'title'     => __('All Profile Social Links:', 'mgpc'),
                            'subtitle'  => __('Current active profiles', 'mgpc'),
                            'callback'  => 'show_all_social_profile_links'
                    ),*/
                    /*array(
                        'id'        => 'opt-check-sortable',
                        'type'      => 'sortable',
                        'mode'      => 'checkbox', // checkbox or text
                        'title'     => __('Sortable Text Option', 'mgms-framework'),
                        'subtitle'  => __('Define and reorder these however you want.', 'mgms-framework'),
                        'desc'      => __('This is the description field, again good for additional info.', 'mgms-framework'),
                        'options'   => array(
                            'si1' => 'Test',
                            'si2' => 'test2',
                            'si3' => 'test3',
                        )
                    ),*/
                    
                    //  Create social media links
/*                    array(
                        'id'        => 'create-social-links-profile',
                        'type'      => 'switch',
                        'title'     => __('Show all profile links:', 'mgpc'),
                        'subtitle'  => __('', 'mgpc'),
                      'desc'      => __('If Disable, Disappear Image block. <small><i>Default: Enable</i></small>', 'mgpc'),
                        'default'   => 0,
                        'on'        => 'Enabled',
                        'off'       => 'Disabled',
                    ),*/
/*                    array(
                            'id'        => 'show-all-social-profile',
                            'type'      => 'callback',
                            'title'     => __('All Profile Social Links:', 'mgpc'),
                            'subtitle'  => __('Current active profiles', 'mgpc'),
                            'callback'  => 'show_all_social_profile_links'
                    ),*/
                    
/*                    array(
                        'id'        => 'opt-slides',
                        'type'      => 'slides',
                        'title'     => __('Slides Options', 'redux-framework-demo'),
                        'subtitle'  => __('Unlimited slides with drag and drop sortings.', 'redux-framework-demo'),
                        'desc'      => __('This field will store all slides values into a multidimensional array to use into a foreach loop.', 'redux-framework-demo'),
                        'placeholder'   => array(
                            'title'         => __('This is a title', 'redux-framework-demo'),
                            'description'   => __('Description Here', 'redux-framework-demo'),
                            'url'           => __('Give us a link!', 'redux-framework-demo'),
                        ),
                    ),*/
                 ),
            );

            $this->sections[] = array(
                'icon'      => 'el-icon-check-empty',
                'title'     => __('Meta Box', 'mgpc'),
                'subsection' => true,
                'heading'   => __('Meta Box Settings:', 'mgpc'),
                'desc'      => __('<p class="description">Change Meta Box Structure</p>', 'mgpc'),
                'fields'    => array(
                    /*array(
                        'id'        => 'opt-multi-select',
                        'type'      => 'select',
                        'multi'     => true,
                        'title'     => __('Multi Select Option', 'redux-framework-demo'),
                        'subtitle'  => __('No validation can be done on this field type', 'redux-framework-demo'),
                        'desc'      => __('This is the description field, again good for additional info.', 'redux-framework-demo'),
                        
                        //Must provide key => value pairs for radio options
                        'options'   => $mgposttypes,
                        'default'   => array('1'),
                    ),
                    array(
                        'id'        => 'set-contributors-for',
                        'type'      => 'select',
                        'data'      => 'post_type',
                        'multi'     => true,
                        'title'     => __('Assign Post Types:', 'mgpc'),
                        'hint'      => array(
                            'title'     => 'Assign Post Types',
                            'content'   => 'Assign custom post types for contributors list. <i>Default: post</i>.',
                        )
                    ),*/
                    array(
                        'id'        => 'exclude-roles',
                        'type'      => 'select',
                        'data'      => 'roles',
                        'multi'     => true,
                        'title'     => __('Exclude Roles:', 'mgpc-framework'),
                        /*'subtitle'  => __('No validation can be done on this field type', 'mgpc-framework'),*/
                        'desc'      => __('Exclude unwanted roles from contributors list.', 'mgpc-framework'),
                        'hint'      => array(
                            'title'     => 'Exclude Roles',
                            'content'   => 'Exclude unwanted roles from contributors list. <i>Default: empty</i>.',
                        )
                    ),
                 ),
            );

            $this->sections[] = array(
                'icon'      => 'el-icon-cogs',
                'title'     => __('Structure', 'mgpc'),
                'heading'   => __('Structure [HELP] Section: ', 'mgpc'),
/*                'desc'      => __('', 'mgpc'),*/
                'subsection' => false,
                'fields'    => array(
                        array(
                                'id'        => 'help-structure-settings',
                                'type'      => 'callback',
                                'title'     => __('Label and Author Block:', 'mgpc'),
                                'subtitle'  => __('Select:<br><small><i>Structure -> Author Block </i></small><br/> OR <br/><small><i>Structure -> Label </i></small>', 'mgpc'),
                                'callback'  => 'help_structure_settings'
                        ),
                    ),
            );

            /*$this->sections[] = array(
                'icon'      => 'el-icon-check-empty',
                'title'     => __('Carousel', 'mgpc'),
                'heading'   => __('Carousel List:', 'mgpc'),
                'subsection' => true,
                'desc'      => __('<p class="description">Here, You can Hide / Show contributors image, name, role, bio, social links etc.</p>', 'mgpc'),
                'fields'    => array(
                    
                    array(
                        'id'        => 'carousel-items',
                        'type'      => 'text',
                        'title'     => __('Show no. of Authors:', 'mgpc'),
                        'desc'      => __('<small><i>Default: 2</i></small>'),
                        'default'   => '2',
                    ),
                    array(
                        'id'        => 'carousel-slidespeed',
                        'type'      => 'text',
                        'title'     => __('Slide Speed:', 'mgpc'),
                        'desc'      => __('<small><i>Default: 200</i></small>'),
                        'default'   => '200',
                    ),
                    array(
                        'id'        => 'carousel-autoplay',
                        'type'      => 'switch',
                        'title'     => __('Auto Play:', 'mgpc'),
                        'subtitle'  => __('', 'mgpc'),
                        'default'   => false,
                        'on'        => 'True',
                        'off'       => 'False',
                        'hint'      => array(
                            'title'     => 'Carousel Auto Play',
                            'content'   => 'If True, Auto Paly Contributors carousel. <small><i>Default: True</i></small>',
                        )
                    ),
                    array(
                        'id'        => 'carousel-stoponhover',
                        'type'      => 'switch',
                        'title'     => __('Stop On Hover:', 'mgpc'),
                        'subtitle'  => __('', 'mgpc'),
                        'default'   => false,
                        'on'        => 'True',
                        'off'       => 'False',
                        'hint'      => array(
                            'title'     => 'Stop Carousel On Hover',
                            'content'   => 'If True, Contributors Carousel Stop on Hover. <small><i>Default: False</i></small>',
                        )
                    ),
                    array(
                        'id'        => 'carousel-navigation',
                        'type'      => 'switch',
                        'title'     => __('Navigation:', 'mgpc'),
                        'subtitle'  => __('', 'mgpc'),
                        'default'   => false,
                        'on'        => 'True',
                        'off'       => 'False',
                        'hint'      => array(
                            'title'     => 'Navigation',
                            'content'   => 'If True, Contributors Carousel Show Navigation. <small><i>Default: False</i></small>',
                        )
                    ),
                    array(
                        'id'        => 'carousel-pagination',
                        'type'      => 'switch',
                        'title'     => __('Pagination:', 'mgpc'),
                        'subtitle'  => __('', 'mgpc'),
                        'default'   => true,
                        'on'        => 'True',
                        'off'       => 'False',
                        'hint'      => array(
                            'title'     => 'Pagination',
                            'content'   => 'If True, Contributors Carousel Show Pagination. <small><i>Default: True</i></small>',
                        )
                    ),
                    array(
                        'id'        => 'carousel-responsive',
                        'type'      => 'switch',
                        'title'     => __('Responsive:', 'mgpc'),
                        'subtitle'  => __('', 'mgpc'),
                        'default'   => true,
                        'on'        => 'True',
                        'off'       => 'False',
                        'hint'      => array(
                            'title'     => 'Responsive',
                            'content'   => 'If True, Contributors Carousel is Responsive. <small><i>Default: True</i></small>',
                        )
                    ),
                ),
            );*/

            $this->sections[] = array(
                'icon'      => 'el-icon-check-empty',
                'title'     => __('Author Block', 'mgpc'),
                'heading'   => __('Author Block:', 'mgpc'),
                'subsection' => true,
                'desc'      => __('<p class="description">Here, You can Hide / Show contributors image, name, role, bio, social links etc.</p>', 'mgpc'),
                'fields'    => array(
                    array(
                        'id'        => 'enable-carousel-list',
                        'type'      => 'switch',
                        'title'     => __('Carousel List:', 'mgpc'),
                        'subtitle'  => __('', 'mgpc'),
                        'default'   => 0,
                        'on'        => 'Enabled',
                        'off'       => 'Disabled',
                        'hint'      => array(
                            'title'     => 'Carousel List',
                            'content'   => 'If Disable, Disappear Contributors carousel. <small><i>Default: Disabled</i></small>',
                        )
                    ),
                    array(
                        'id'        => 'author-block-view',
                        'type'      => 'image_select',
                        //'compiler'  => array(''),
                        'title'     => __('Author Block Layout:', 'mgpc'),
/*                        'desc'      => __('<p class="description">Select Contributors basic structure.</p>', 'mgpc'),*/
                        'options'  => array(
                            '1'      => array(
/*                                'title'   => 'Horizontal',*/
                                'img'   => plugins_url( '../../images/view-horizontal-200.png', __FILE__ ),
                            ),
                            '2'      => array(
/*                                'title'   => 'Verticle',*/
                                'img'   => plugins_url( '../../images/view-verticle-200.png', __FILE__ ),
                            )
                        ),
                        'default' => '1',
                        'hint'      => array(
                            'title'     => 'Author Block View',
                            'content'   => 'Here you can change author block layout. You can set <i>Verticle / Horizontal</i> Layout.',
                        )
                    ),
                    array(
                        'id'        => 'enable-block-image',
                        'type'      => 'switch',
                        'title'     => __('Image Block:', 'mgpc'),
                        'subtitle'  => __('Hide/Show Images.', 'mgpc'),
                        'desc'      => __('If Disable, Disappear Image block. <small><i>Default: Enable</i></small>', 'mgpc'),
                        'default'   => 1,
                        'on'        => 'Enabled',
                        'off'       => 'Disabled',
                    ),
                    array(
                        'id'        => 'enable-block-meta',
                        'type'      => 'switch',
                        'title'     => __('Meta Block:', 'mgpc'),
                        'subtitle'  => __('Name, Role, Bio etc.', 'mgpc'),
                        'desc'      => __('If Disable, Disappear Name, Role, Bio etc. <small><i>Default: Enable</i></small>', 'mgpc'),
                        'default'   => 1,
                        'on'        => 'Enabled',
                        'off'       => 'Disabled',
                    ),
                    array(
                        'id'        => 'enable-meta-name',
                        'type'      => 'switch',
                        'title'     => __('Name:', 'mgpc'),
                        'subtitle'  => __('Hide / Show Name', 'mgpc'),
                        'desc'      => __('If Hide, Disappear Name from list. <small><i>Default: Show</i></small>', 'mgpc'),
                        'default'   => 1,
                        'on'        => 'Show',
                        'off'       => 'Hide',
                        'required'  => array('enable-block-meta', "=", 1),
                    ),
                    array(
                        'id'        => 'enable-meta-role',
                        'type'      => 'switch',
                        'title'     => __('Role:', 'mgpc'),
                        'subtitle'  => __('Hide / Show Role', 'mgpc'),
                        'desc'      => __('If Hide, Disappear Role from list. <small><i>Default: Hide</i></small>', 'mgpc'),
                        'default'   => 1,
                        'on'        => 'Show',
                        'off'       => 'Hide',
                        'required'  => array('enable-block-meta', "=", 1),
                    ),
                    array(
                        'id'        => 'enable-meta-bio',
                        'type'      => 'switch',
                        'title'     => __('Biographical Info:', 'mgpc'),
                        'subtitle'  => __('Hide / Show Biographical Info', 'mgpc'),
                        'desc'      => __('If Hide, Disappear Biographical Info from list. <small><i>Default: Hide</i></small>', 'mgpc'),
                        'default'   => 1,
                        'on'        => 'Show',
                        'off'       => 'Hide',
                        'required'  => array('enable-block-meta', "=", 1),
                    ),
                    array(
                        'id'        => 'enable-meta-email',
                        'type'      => 'switch',
                        'title'     => __('Email:', 'mgpc'),
                        'subtitle'  => __('Hide / Show Email', 'mgpc'),
                        'desc'      => __('If Hide, Disappear Email from list. <small><i>Default: Hide</i></small>', 'mgpc'),
                        'default'   => 1,
                        'on'        => 'Show',
                        'off'       => 'Hide',
                        'required'  => array('enable-block-meta', "=", 1),
                    ),
                    array(
                        'id'        => 'enable-meta-website',
                        'type'      => 'switch',
                        'title'     => __('Website:', 'mgpc'),
                        'subtitle'  => __('Hide / Show Website', 'mgpc'),
                        'desc'      => __('If Hide, Disappear Website from list. <small><i>Default: Hide</i></small>', 'mgpc'),
                        'default'   => 1,
                        'on'        => 'Show',
                        'off'       => 'Hide',
                        'required'  => array('enable-block-meta', "=", 1),
                    ),
                    array(
                        'id'        => 'enable-meta-social-links',
                        'type'      => 'switch',
                        'title'     => __('Social Links:', 'mgpc'),
                        'subtitle'  => __('Hide / Show Social Links', 'mgpc'),
                        'desc'      => __('If Hide, Disappear Social Links from list. <small><i>Default: Show</i></small>', 'mgpc'),
                        'default'   => 1,
                        'on'        => 'Show',
                        'off'       => 'Hide',
                        'required'  => array('enable-block-meta', "=", 1),
                    ),
                    /*array(
                        'id'        => 'sort-social-links',
                        'type'      => 'sortable',
                        'mode'      => 'checkbox', // checkbox or text
                        'title'     => __('Sortable Text Option', 'mgpc'),
                        'subtitle'  => __('Define and reorder these however you want.', 'mgpc'),
                        'desc'      => __('This is the description field, again good for additional info.', 'mgpc'),
                        'options'   => $social_links, 
                    ),*/
                    /*array(
                        'id'        => 'add-social-links',
                        'type'      => 'switch',
                        'title'     => __('Create Social Links:', 'mgpc'),
                        'subtitle'  => __('Do you want to create social link fields at user profile.', 'mgpc'),
                        'desc'      => __('If enable, Social links add at at User Profile. Goto <i>(Users -> Your Profile)</i>.', 'mgpc'),
                        'default'   => 0,
                        'on'        => 'Enabled',
                        'off'       => 'Disabled',
                    ),*/

                ),
            );
            $this->sections[] = array(
                'icon'      => 'dashicons dashicons-editor-quote',
                'title'     => __('Label', 'mgpc'),
                'heading'   => __('Label Setting:', 'mgpc'),
                'subsection' => true,
                'desc'      => __('<p class="description">Here, You can change / disable contributors label.</p>', 'mgpc'),
                'fields'    => array(
                    array(
                        'id'        => 'enable-label',
                        'type'      => 'switch',
                        'title'     => __('Label:', 'mgpc'),
                        'desc'      => __('<small><i>Default: Enabled</i></small>'),
                        'default'   => 1,
                        'on'        => 'Enabled',
                        'off'       => 'Disabled',
                        'hint'      => array(
                            'title'     => 'Hide / Show Label?',
                            'content'   => '<small>Hide / Show label from contributors list. <i>Default: Enable</i></small>.',
                        )
                    ),
                    array(
                        'id'        => 'enable-label-text',
                        'type'      => 'text',
                        'title'     => __('Chanage Label to:', 'mgpc'),
                        'desc'      => __('<small><i>Default: Contributors:</i></small>'),
                        'default'   => 'Contributors:',
                    ),
                ),
            );

            $this->sections[] = array(
                'icon'      => 'el-icon-eye-open',
                'title'     => __('Styling', 'mgpc'),
                'heading'   => __('Styling [HELP] Section:', 'mgpc'),
                'subsection' => false,
                'desc'      => __('<p class="description">Here, You can Change styling of contributors image, name, role, bio, social links etc.</p>', 'mgpc'),
                'fields'    => array(
                    array(
                                'id'        => 'help-styling-settings',
                                'type'      => 'callback',
                                'title'     => __('Author Block Styling:', 'mgpc'),
                                'subtitle'  => __('Select:<br><small><i>Styling -> Author Block </i></small><br/> OR <br/><small><i>Structure -> Label </i></small>', 'mgpc'),
                                'callback'  => 'help_styling'
                        ),
                ),
            );

            $this->sections[] = array(
                'icon'      => 'dashicons dashicons-admin-appearance',
                'title'     => __('Main Block', 'mgpc'),
                'subsection' => true,
                'heading'   => __('Main Block Settings:', 'mgpc'),
                'desc'      => __('<p class="description">Change contributors block background color, border, margin, padding etc.</p>', 'mgpc'),
                'fields'    => array(
                    array(
                        'id'       => 'background-color',
                        'type'     => 'color',
                        'mode'     => 'background',
                        'title'    => __('Background Color', 'mgpc'),
                        'desc' => __('<small><i>Default: #ffffff</i></small>', 'mgpc'),
                        'default'  => '#ffffff',
                        'validate' => 'color',
                        'output'  => array('#mgpc'),
                        'hint'      => array(
                            'title'     => 'Background Color',
                            'content'   => 'Pick a background color for the main block. <small><i>Default: #ffffff</i></small>.',
                        ),
                    ),
					array(
                        'id'        => 'background-border',
                        'title'     => __('Border', 'mgpc'),
                        'type'      => 'border',
                        'output'    => array('#mgpc'),
                        'all'       => false,
                        'desc'      => __('<small><i>Default: Border: 1px solid #CCCDDD</i></small>', 'mgpc'),
                        'default'   => array(
                            'border-color'  => '#CCCDDD', 
                            'border-style'  => 'solid', 
                            'border-top'    => '1px', 
                            'border-bottom' => '1px',
                            'border-right' => '1px',
                            'border-left' => '1px'
                        ),
                        'hint'      => array(
                            'title'     => 'Border',
                            'content'   => 'Change contributors block border color, size for (right, left, top, bottom) or all. <small><i>Default: Border: 1px solid #CCCDDD</i></small>.',
                        ),
                    ),

                    array(
                        'id'             => 'background-padding',
                        'title'          => __('Padding', 'mgpc'),
                        'desc'          => __('<small><i>Default: 0px 15px 0px 15px</i></small>', 'mgpc'),
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array('em', 'px'),
                        'units_extended' => 'false',
                        'output'    => array('#mgpc'),
                        'default'            => array(
                            'padding-top'     => '0px',
                            'padding-right'   => '15px',
                            'padding-bottom'  => '0px',
                            'padding-left'    => '15px',
                            'units'          => 'px',
                        ),
                        'hint'      => array(
                            'title'     => 'Padding',
                            'content'   => 'Change contributors block Padding for (right, left, top, bottom) or all. <small><i>Default: 0px 15px 0px 15px</i></small>.',
                        ),
                    ),
                    array(
                        'id'             => 'background-margin',
                        'title'          => __('Margin', 'mgpc'),
                        'desc'          => __('<small><i>Default: 0px 0px 0px 0px</i></small>', 'mgpc'),
                        'type'           => 'spacing',
                        'output'    => array('#mgpc'),
                        'mode'           => 'margin',
                        'units'          => array('px', 'em'),
                        'units_extended' => 'false',
                        'default'            => array(
                            'margin-top'     => '0px',
                            'margin-right'   => '0px',
                            'margin-bottom'  => '0px',
                            'margin-left'    => '0px',
                            'units'          => 'px',
                        ),
                        'hint'      => array(
                            'title'     => 'Margin',
                            'content'   => 'Change contributors block Margin for (right, left, top, bottom) or all. <small><i>Default: 0px 0px 0px 0px</i></small>.',
                        ),
                    ),
                )
            );

            $this->sections[] = array(
                'icon'      => 'dashicons dashicons-editor-quote',
                'title'     => __('Label', 'mgpc'),
                'heading'   => __('Contributors LABEL settings.', 'mgpc'),
                'subsection' => true,
                'desc'      => __('<p class="description">Choose best font face, sice, color which is suitable for your theme.</p>', 'mgpc'),
                'fields'    => array(
                    array(
                        'id'        => 'label-typography',
                        'type'      => 'typography',
                        'title'     => __('Typography', 'mgpc'),
                        'output'  => array('#mgpc .mgpc-title h3.title'),
                        'word-spacing'  => true,
                        'letter-spacing'    => true,
                        'text-transform'    => true,
                        'default'     => array(
                              'color' => '#CAB045',
                              'font-family'  => 'sans-serif',
                              'font-size'  => '25px',
                              'font-style'  => 'normal',
                              'font-weight'  => '400',
                              'line-height'  => '27px',
                              'text-align'  => 'left'
                        ),
                        'hint'      => array(
                            'title'     => 'Typography',
                            'content'   => 'Change typography of the label.</small>.',
                        ),
                    ),
                    array(
                        'id'       => 'label-color',
                        'type'     => 'color',
                        'mode'     => 'background',
                        'title'    => __('Background Color', 'mgpc'),
                        'desc' => __('<small><i>Default: none.</i></small>', 'mgpc'),
                        'validate' => 'color',
                        'output'  => array('#mgpc .mg-contributors-post h3.title'),
                        'hint'      => array(
                            'title'     => 'Background Color',
                            'content'   => 'Pick a background color for the label. <small><i>Default: none.</i></small>.',
                        ),
                    ),
                    array(
                        'id'        => 'label-border',
                        'title'     => __('Border', 'mgpc'),
                        'type'      => 'border',
                        'output'  => array('#mgpc .mgpc-title'),
                        'all'       => false,
                        'desc'      => __('<small><i>Default: border-bottom: 1px solid #ddd</i></small>', 'mgpc'),
                        'default'   => array(
                            'border-color'  => '#ddd', 
                            'border-style'  => 'solid', 
                            'border-top'    => '0px', 
                            'border-bottom' => '1px',
                            'border-right' => '0px',
                            'border-left' => '0px'
                        ),
                        'hint'      => array(
                            'title'     => 'Border',
                            'content'   => 'Change contributors label border color, size for (right, left, top, bottom) or all. <small><i>Default: Border: 1px solid #CCCDDD</i></small>.',
                        ),
                    ),
                    array(
                        'id'             => 'label-padding',
                        'title'          => __('Padding', 'mgpc'),
                        'desc'          => __('<small><i>Default: 15px 0 15px 0</i></small>', 'mgpc'),
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array('em', 'px'),
                        'units_extended' => 'false',
                        'output'    => array('#mgpc .mgpc-title'),
                        'default'            => array(
                            'padding-top'     => '15px',
                            'padding-right'   => '0px',
                            'padding-bottom'  => '15px',
                            'padding-left'    => '0px',
                            'units'          => 'px',
                        ),
                        'hint'      => array(
                            'title'     => 'Padding',
                            'content'   => 'Change contributors label Padding for (right, left, top, bottom) or all. <small><i>Default: 15px 0 15px 0</i></small>.',
                        ),
                    ),
                    array(
                        'id'             => 'label-margin',
                        'title'          => __('Margin', 'mgpc'),
                        'desc'          => __('<small><i>Default: 0px 0px 0px 0px</i></small>', 'mgpc'),
                        'type'           => 'spacing',
                        'output'    => array('#mgpc .mgpc-title'),
                        'mode'           => 'margin',
                        'units'          => array('px', 'em'),
                        'units_extended' => 'false',
                        'default'            => array(
                            'margin-top'     => '0px',
                            'margin-right'   => '0px',
                            'margin-bottom'  => '0px',
                            'margin-left'    => '0px',
                            'units'          => 'px',
                        ),
                        'hint'      => array(
                            'title'     => 'Margin',
                            'content'   => 'Change contributors label Margin for (right, left, top, bottom) or all. <small><i>Default: 0px 0px 0px 0px</i></small>.',
                        ),
                    ),
                )
            );

            $this->sections[] = array(
                'icon'      => 'el-icon-check-empty',
                'title'     => __('Author Block', 'mgpc'),
                'heading'   => __('Author Block Styling:', 'mgpc'),
                'subsection' => true,
                'desc'      => __('<p class="description">Here, You can Change image, name, role, bio styling etc.</p>', 'mgpc'),
                'fields'    => array(
                    array(
                        'id'        => 'mgpc-info-styling-image',
                        'type'      => 'info',
                        'notice'    => true,
                        'style'     => 'info',
                        'icon'      => 'el-icon-picture',
                        'title'     => __('IMAGE', 'mgpc'),
                        'desc'      => __('Change authar image height / width, border, margin, padding etc.', 'mgpc')
                    ),
                    array(
                        'id'       => 'image_dimensions',
                        'type'     => 'dimensions',
                        'units'    => array('em','px','%'),
                        'title'    => __('Dimensions (Width/Height)', 'mgpc'),
                        'desc'     => __('<small><i>Default: none</i></small>'),
                        'output'  => array('#mgpc .image-block img'),
                        'hint'      => array(
                            'title'     => 'Dimensions Height / Width',
                            'content'   => 'Change Image Dimensions Height / Width of the author.</small>.',
                        ),
                    ),
                    array(
                        'id'        => 'image-border',
                        'title'     => __('Border', 'mgpc'),
                        'type'      => 'border',
                        'output'  => array('#mgpc .image-block img'),
                        'all'       => false,
                        'desc'      => __('<small><i>Default: none</i></small>', 'mgpc'),
                        'default'   => array(
                            'border-top'    => '0px', 
                            'border-bottom' => '0px',
                            'border-right' => '0px',
                            'border-left' => '0px'
                        ),
                        'hint'      => array(
                            'title'     => 'Border',
                            'content'   => 'Change contributors image border color, size for (right, left, top, bottom) or all. <small><i>Default: none</i></small>.',
                        ),
                    ),
                    array(
                        'id'             => 'image-padding',
                        'title'          => __('Padding', 'mgpc'),
                        'desc'          => __('<small><i>Default: 0px 0px 0px 0px</i></small>', 'mgpc'),
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array('em', 'px'),
                        'units_extended' => 'false',
                        'output'  => array('#mgpc .image-block img'),
                        'default'            => array(
                            'padding-top'     => '0px',
                            'padding-right'   => '0px',
                            'padding-bottom'  => '0px',
                            'padding-left'    => '0px',
                            'units'          => 'px',
                        ),
                        'hint'      => array(
                            'title'     => 'Padding',
                            'content'   => 'Change contributors image Padding for (right, left, top, bottom) or all. <small><i>Default: 0px 0px 0px 0px</i></small>.',
                        ),
                    ),
                    array(
                        'id'             => 'image-margin',
                        'title'          => __('Margin', 'mgpc'),
                        'desc'          => __('<small><i>Default: 0px 0px 0px 0px</i></small>', 'mgpc'),
                        'type'           => 'spacing',
                        'output'  => array('#mgpc .image-block img'),
                        'mode'           => 'margin',
                        'units'          => array('px', 'em'),
                        'units_extended' => 'false',
                        'default'            => array(
                            'margin-top'     => '0px',
                            'margin-right'   => '0px',
                            'margin-bottom'  => '0px',
                            'margin-left'    => '0px',
                            'units'          => 'px',
                        ),
                        'hint'      => array(
                            'title'     => 'Margin',
                            'content'   => 'Change contributors image Margin for (right, left, top, bottom) or all. <small><i>Default: 0px 0px 0px 0px</i></small>.',
                        ),
                    ),
                    //  .end image

                    array(
                        'id'   =>'divider-mgpc-meta-name',
                        'type' => 'divide'
                    ),
                    array(
                        'id'        => 'mgpc-info-styling-name',
                        'type'      => 'info',
                        'notice'    => true,
                        'style'     => 'info',
                        'icon'      => 'el-icon-font',
                        'title'     => __('NAME', 'mgpc'),
                        'desc'      => __('Change authar name typography, background-color, border, margin, padding etc.', 'mgpc')
                    ),
                    array(
                        'id'        => 'name-typography',
                        'type'      => 'typography',
                        'title'     => __('Typography', 'mgpc'),
                        'output'  => array('#mgpc .author-block h4.author-name'),
                        'word-spacing'  => true,
                        'letter-spacing'    => true,
                        'text-transform'    => true,
                        'default'     => array(
                              'color' => '#555',
                              'font-family'  => 'raleway',
                              'font-size'  => '16px',
                              'font-style'  => 'normal',
                              'font-weight'  => '600',
                              'line-height'  => '20px',
                              'text-align'  => 'left'
                        ),
                        'hint'      => array(
                            'title'     => 'Typography',
                            'content'   => 'Change typography of the name.</small>.',
                        ),
                    ),
                    array(
                        'id'       => 'name-color',
                        'type'     => 'color',
                        'mode'     => 'background',
                        'title'    => __('Background Color', 'mgpc'),
                        'desc' => __('<small><i>Default: none.</i></small>', 'mgpc'),
                        'validate' => 'color',
                        'output'  => array('#mgpc .author-block h4.author-name'),
                        'hint'      => array(
                            'title'     => 'Background Color',
                            'content'   => 'Pick a background color for the name. <small><i>Default: none.</i></small>.',
                        ),
                    ),
                    array(
                        'id'        => 'name-border',
                        'title'     => __('Border', 'mgpc'),
                        'type'      => 'border',
                        'output'  => array('#mgpc .author-block h4.author-name'),
                        'all'       => false,
                        'desc'      => __('<small><i>Default: none</i></small>', 'mgpc'),
                        'default'   => array(
                            'border-top'    => '0px', 
                            'border-bottom' => '0px',
                            'border-right' => '0px',
                            'border-left' => '0px'
                        ),
                        'hint'      => array(
                            'title'     => 'Border',
                            'content'   => 'Change contributors name border color, size for (right, left, top, bottom) or all. <small><i>Default: none</i></small>.',
                        ),
                    ),
                    array(
                        'id'             => 'name-padding',
                        'title'          => __('Padding', 'mgpc'),
                        'desc'          => __('<small><i>Default: 0px 0px 0px 0px</i></small>', 'mgpc'),
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array('em', 'px'),
                        'units_extended' => 'false',
                        'output'  => array('#mgpc .author-block h4.author-name'),
                        'default'            => array(
                            'padding-top'     => '0px',
                            'padding-right'   => '0px',
                            'padding-bottom'  => '0px',
                            'padding-left'    => '0px',
                            'units'          => 'px',
                        ),
                        'hint'      => array(
                            'title'     => 'Padding',
                            'content'   => 'Change contributors name Padding for (right, left, top, bottom) or all. <small><i>Default: 0px 0px 0px 0px</i></small>.',
                        ),
                    ),
                    array(
                        'id'             => 'name-margin',
                        'title'          => __('Margin', 'mgpc'),
                        'desc'          => __('<small><i>Default: 0px 0px 0px 0px</i></small>', 'mgpc'),
                        'type'           => 'spacing',
                        'output'  => array('#mgpc .author-block h4.author-name'),
                        'mode'           => 'margin',
                        'units'          => array('px', 'em'),
                        'units_extended' => 'false',
                        'default'            => array(
                            'margin-top'     => '0px',
                            'margin-right'   => '0px',
                            'margin-bottom'  => '0px',
                            'margin-left'    => '0px',
                            'units'          => 'px',
                        ),
                        'hint'      => array(
                            'title'     => 'Margin',
                            'content'   => 'Change contributors name Margin for (right, left, top, bottom) or all. <small><i>Default: 0px 0px 0px 0px</i></small>.',
                        ),
                    ),
                    //  .name


                    array(
                        'id'   =>'divider-mgpc-meta-role',
                        'type' => 'divide'
                    ),
                    array(
                        'id'        => 'mgpc-info-styling-role',
                        'type'      => 'info',
                        'notice'    => true,
                        'style'     => 'info',
                        'icon'      => 'el-icon-font',
                        'title'     => __('ROLE', 'mgpc'),
                        'desc'      => __('Change authar name typography, background-color, border, margin, padding etc.', 'mgpc')
                    ),
                    array(
                        'id'        => 'role-typography',
                        'type'      => 'typography',
                        'title'     => __('Typography', 'mgpc'),
                        'output'  => array('#mgpc .author-block h5.author-role'),
                        'word-spacing'  => true,
                        'letter-spacing'    => true,
                        'text-transform'    => true,
                        'default'     => array(
                              'color' => '#979797',
                              'font-family'  => 'coda',
                              'font-size'  => '11px',
                              'font-style'  => 'normal',
                              'font-weight'  => '600',
                              'line-height'  => '15px',
                              'text-align'  => 'left'
                        ),
                        'hint'      => array(
                            'title'     => 'Typography',
                            'content'   => 'Change typography of the role.</small>.',
                        ),
                    ),
                    array(
                        'id'       => 'role-color',
                        'type'     => 'color',
                        'mode'     => 'background',
                        'title'    => __('Background Color', 'mgpc'),
                        'desc' => __('<small><i>Default: none.</i></small>', 'mgpc'),
                        'validate' => 'color',
                        'output'  => array('#mgpc .author-block h5.author-role'),
                        'hint'      => array(
                            'title'     => 'Background Color',
                            'content'   => 'Pick a background color for the role. <small><i>Default: none.</i></small>.',
                        ),
                    ),
                    array(
                        'id'        => 'role-border',
                        'title'     => __('Border', 'mgpc'),
                        'type'      => 'border',
                        'output'  => array('#mgpc .author-block h5.author-role'),
                        'all'       => false,
                        'desc'      => __('<small><i>Default: none</i></small>', 'mgpc'),
                        'default'   => array(
                            'border-top'    => '0px', 
                            'border-bottom' => '0px',
                            'border-right' => '0px',
                            'border-left' => '0px'
                        ),
                        'hint'      => array(
                            'title'     => 'Border',
                            'content'   => 'Change contributors role border color, size for (right, left, top, bottom) or all. <small><i>Default: none</i></small>.',
                        ),
                    ),
                    array(
                        'id'             => 'role-padding',
                        'title'          => __('Padding', 'mgpc'),
                        'desc'          => __('<small><i>Default: 0px 0px 0px 0px</i></small>', 'mgpc'),
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array('em', 'px'),
                        'units_extended' => 'false',
                        'output'  => array('#mgpc .author-block h5.author-role'),
                        'default'            => array(
                            'padding-top'     => '0px',
                            'padding-right'   => '0px',
                            'padding-bottom'  => '0px',
                            'padding-left'    => '0px',
                            'units'          => 'px',
                        ),
                        'hint'      => array(
                            'title'     => 'Padding',
                            'content'   => 'Change contributors role Padding for (right, left, top, bottom) or all. <small><i>Default: 0px 0px 0px 0px</i></small>.',
                        ),
                    ),
                    array(
                        'id'             => 'role-margin',
                        'title'          => __('Margin', 'mgpc'),
                        'desc'          => __('<small><i>Default: 0px 0px 0px 0px</i></small>', 'mgpc'),
                        'type'           => 'spacing',
                        'output'  => array('#mgpc .author-block h5.author-role'),
                        'mode'           => 'margin',
                        'units'          => array('px', 'em'),
                        'units_extended' => 'false',
                        'default'            => array(
                            'margin-top'     => '0px',
                            'margin-right'   => '0px',
                            'margin-bottom'  => '0px',
                            'margin-left'    => '0px',
                            'units'          => 'px',
                        ),
                        'hint'      => array(
                            'title'     => 'Margin',
                            'content'   => 'Change contributors role Margin for (right, left, top, bottom) or all. <small><i>Default: 0px 0px 0px 0px</i></small>.',
                        ),
                    ),
                    //  .role

                    array(
                        'id'   =>'divider-mgpc-meta-bio',
                        'type' => 'divide'
                    ),
                    array(
                        'id'        => 'mgpc-info-styling-bio',
                        'type'      => 'info',
                        'notice'    => true,
                        'style'     => 'info',
                        'icon'      => 'el-icon-quotes',
                        'title'     => __('BIOGRAPHY', 'mgpc'),
                        'desc'      => __('Change authar name typography, background-color, border, margin, padding etc.', 'mgpc')
                    ),
                    array(
                        'id'        => 'bio-typography',
                        'type'      => 'typography',
                        'title'     => __('Typography', 'mgpc'),
                        'output'  => array('#mgpc p.description'),
                        'word-spacing'  => true,
                        'letter-spacing'    => true,
                        'text-transform'    => true,
                        'default'     => array(
                              'color' => '#998846',
                              'font-family'  => 'open sans',
                              'font-size'  => '16px',
                              'font-style'  => 'italic',
                              'font-weight'  => 'normal',
                              'line-height'  => '20px',
                              'text-align'  => 'left'
                        ),
                        'hint'      => array(
                            'title'     => 'Typography',
                            'content'   => 'Change typography of the author biography.</small>.',
                        ),
                    ),
                    array(
                        'id'       => 'bio-color',
                        'type'     => 'color',
                        'mode'     => 'background',
                        'title'    => __('Background Color', 'mgpc'),
                        'desc' => __('<small><i>Default: none.</i></small>', 'mgpc'),
                        'validate' => 'color',
                        'output'  => array('#mgpc p.description'),
                        'hint'      => array(
                            'title'     => 'Background Color',
                            'content'   => 'Pick a background color for the author biography. <small><i>Default: none.</i></small>.',
                        ),
                    ),
                    array(
                        'id'        => 'bio-border',
                        'title'     => __('Border', 'mgpc'),
                        'type'      => 'border',
                        'output'  => array('#mgpc p.description'),
                        'all'       => false,
                        'desc'      => __('<small><i>Default: none</i></small>', 'mgpc'),
                        'default'   => array(
                            'border-top'    => '0px', 
                            'border-bottom' => '0px',
                            'border-right' => '0px',
                            'border-left' => '0px'
                        ),
                        'hint'      => array(
                            'title'     => 'Border',
                            'content'   => 'Change contributors biography border color, size for (right, left, top, bottom) or all. <small><i>Default: none</i></small>.',
                        ),
                    ),
                    array(
                        'id'             => 'bio-padding',
                        'title'          => __('Padding', 'mgpc'),
                        'desc'          => __('<small><i>Default: 0px 0px 0px 0px</i></small>', 'mgpc'),
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array('em', 'px'),
                        'units_extended' => 'false',
                        'output'  => array('#mgpc p.description'),
                        'default'            => array(
                            'padding-top'     => '0px',
                            'padding-right'   => '0px',
                            'padding-bottom'  => '0px',
                            'padding-left'    => '0px',
                            'units'          => 'px',
                        ),
                        'hint'      => array(
                            'title'     => 'Padding',
                            'content'   => 'Change contributors biography Padding for (right, left, top, bottom) or all. <small><i>Default: 0px 0px 0px 0px</i></small>.',
                        ),
                    ),
                    array(
                        'id'             => 'bio-margin',
                        'title'          => __('Margin', 'mgpc'),
                        'desc'          => __('<small><i>Default: 0px 0px 0px 0px</i></small>', 'mgpc'),
                        'type'           => 'spacing',
                        'output'  => array('#mgpc p.description'),
                        'mode'           => 'margin',
                        'units'          => array('px', 'em'),
                        'units_extended' => 'false',
                        'default'            => array(
                            'margin-top'     => '0px',
                            'margin-right'   => '0px',
                            'margin-bottom'  => '5px',
                            'margin-left'    => '0px',
                            'units'          => 'px',
                        ),
                        'hint'      => array(
                            'title'     => 'Margin',
                            'content'   => 'Change contributors biography Margin for (right, left, top, bottom) or all. <small><i>Default: 0px 0px 0px 0px</i></small>.',
                        ),
                    ),
                    //  .bio

                    array(
                        'id'   =>'divider-mgpc-meta-email',
                        'type' => 'divide'
                    ),
                    array(
                        'id'        => 'mgpc-info-styling-email',
                        'type'      => 'info',
                        'notice'    => true,
                        'style'     => 'info',
                        'icon'      => 'el-icon-envelope',
                        'title'     => __('EMAIL', 'mgpc'),
                        'desc'      => __('Change authar name typography, background-color, border, margin, padding etc.', 'mgpc')
                    ),
                    array(
                        'id'        => 'enable-email-iconfont',
                        'type'      => 'switch',
                        'title'     => __('Icon Font:', 'mgpc'),
                        'subtitle'  => __('Hide / Show Icon Font.', 'mgpc'),
                        'desc'      => __('<small><i>Default: Show</i></small>', 'mgpc'),
                        'default'   => 1,
                        'on'        => 'Show',
                        'off'       => 'Hide',
                        'hint'      => array(
                            'title'     => 'Icon Font',
                            'content'   => 'If Hide, Disappear Icon Font of email. <small><i>Default: Show</i></small>', 'mgpc',
                        ),
                    ),
                    array(
                        'id'        => 'email-typography',
                        'type'      => 'typography',
                        'title'     => __('Typography', 'mgpc'),
                        'output'  => array('#mgpc p.email'),
                        'word-spacing'  => true,
                        'letter-spacing'    => true,
                        'text-transform'    => true,
                        'default'     => array(
                              'color' => '#666',
                              'font-family'  => 'open sans',
                              'font-size'  => '12px',
                              'font-style'  => 'italic',
                              'font-weight'  => 'normal',
                              'line-height'  => '15px',
                              'text-align'  => 'left'
                        ),
                        'hint'      => array(
                            'title'     => 'Typography',
                            'content'   => 'Change typography of the author email.</small>.',
                        ),
                    ),
                    array(
                        'id'       => 'email-color',
                        'type'     => 'color',
                        'mode'     => 'background',
                        'title'    => __('Background Color', 'mgpc'),
                        'desc' => __('<small><i>Default: none.</i></small>', 'mgpc'),
                        'validate' => 'color',
                        'output'  => array('#mgpc p.email'),
                        'hint'      => array(
                            'title'     => 'Background Color',
                            'content'   => 'Pick a background color for the author email. <small><i>Default: none.</i></small>.',
                        ),
                    ),
                    array(
                        'id'        => 'email-border',
                        'title'     => __('Border', 'mgpc'),
                        'type'      => 'border',
                        'output'  => array('#mgpc p.email'),
                        'all'       => false,
                        'desc'      => __('<small><i>Default: none</i></small>', 'mgpc'),
                        'default'   => array(
                            'border-top'    => '0px', 
                            'border-bottom' => '0px',
                            'border-right' => '0px',
                            'border-left' => '0px'
                        ),
                        'hint'      => array(
                            'title'     => 'Border',
                            'content'   => 'Change contributors email border color, size for (right, left, top, bottom) or all. <small><i>Default: none</i></small>.',
                        ),
                    ),
                    array(
                        'id'             => 'email-padding',
                        'title'          => __('Padding', 'mgpc'),
                        'desc'          => __('<small><i>Default: 0px 0px 0px 0px</i></small>', 'mgpc'),
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array('em', 'px'),
                        'units_extended' => 'false',
                        'output'  => array('#mgpc p.email'),
                        'default'            => array(
                            'padding-top'     => '0px',
                            'padding-right'   => '0px',
                            'padding-bottom'  => '0px',
                            'padding-left'    => '0px',
                            'units'          => 'px',
                        ),
                        'hint'      => array(
                            'title'     => 'Padding',
                            'content'   => 'Change contributors email Padding for (right, left, top, bottom) or all. <small><i>Default: 0px 0px 0px 0px</i></small>.',
                        ),
                    ),
                    array(
                        'id'             => 'email-margin',
                        'title'          => __('Margin', 'mgpc'),
                        'desc'          => __('<small><i>Default: 0px 0px 0px 0px</i></small>', 'mgpc'),
                        'type'           => 'spacing',
                        'output'  => array('#mgpc p.email'),
                        'mode'           => 'margin',
                        'units'          => array('px', 'em'),
                        'units_extended' => 'false',
                        'default'            => array(
                            'margin-top'     => '0px',
                            'margin-right'   => '0px',
                            'margin-bottom'  => '5px',
                            'margin-left'    => '0px',
                            'units'          => 'px',
                        ),
                        'hint'      => array(
                            'title'     => 'Margin',
                            'content'   => 'Change contributors email Margin for (right, left, top, bottom) or all. <small><i>Default: 0px 0px 0px 0px</i></small>.',
                        ),
                    ),
                    //  .email
                    
                    array(
                        'id'   =>'divider-mgpc-meta-website',
                        'type' => 'divide'
                    ),
                    array(
                        'id'        => 'mgpc-info-styling-website',
                        'type'      => 'info',
                        'notice'    => true,
                        'style'     => 'info',
                        'icon'      => 'el-icon-globe',
                        'title'     => __('WEB SITE', 'mgpc'),
                        'desc'      => __('Change authar name typography, background-color, border, margin, padding etc.', 'mgpc')
                    ),
                    array(
                        'id'        => 'enable-website-iconfont',
                        'type'      => 'switch',
                        'title'     => __('Icon Font:', 'mgpc'),
                        'subtitle'  => __('Hide / Show Icon Font.', 'mgpc'),
                        'desc'      => __('<small><i>Default: Show</i></small>', 'mgpc'),
                        'default'   => 1,
                        'on'        => 'Show',
                        'off'       => 'Hide',
                        'hint'      => array(
                            'title'     => 'Icon Font',
                            'content'   => 'If Hide, Disappear Icon Font of website. <small><i>Default: Show</i></small>', 'mgpc',
                        ),
                    ),
                    array(
                        'id'        => 'website-typography',
                        'type'      => 'typography',
                        'title'     => __('Typography', 'mgpc'),
                        'output'  => array('#mgpc p.website'),
                        'word-spacing'  => true,
                        'letter-spacing'    => true,
                        'text-transform'    => true,
                        'default'     => array(
                              'color' => '#666',
                              'font-family'  => 'open sans',
                              'font-size'  => '12px',
                              'font-style'  => 'italic',
                              'font-weight'  => 'normal',
                              'line-height'  => '15px',
                              'text-align'  => 'left'
                        ),
                        'hint'      => array(
                            'title'     => 'Typography',
                            'content'   => 'Change typography of the author website.</small>.',
                        ),
                    ),
                    array(
                        'id'       => 'website-color',
                        'type'     => 'color',
                        'mode'     => 'background',
                        'title'    => __('Background Color', 'mgpc'),
                        'desc' => __('<small><i>Default: none.</i></small>', 'mgpc'),
                        'validate' => 'color',
                        'output'  => array('#mgpc p.website'),
                        'hint'      => array(
                            'title'     => 'Background Color',
                            'content'   => 'Pick a background color for the author website. <small><i>Default: none.</i></small>.',
                        ),
                    ),
                    array(
                        'id'        => 'website-border',
                        'title'     => __('Border', 'mgpc'),
                        'type'      => 'border',
                        'output'  => array('#mgpc p.website'),
                        'all'       => false,
                        'desc'      => __('<small><i>Default: none</i></small>', 'mgpc'),
                        'default'   => array(
                            'border-top'    => '0px', 
                            'border-bottom' => '0px',
                            'border-right' => '0px',
                            'border-left' => '0px'
                        ),
                        'hint'      => array(
                            'title'     => 'Border',
                            'content'   => 'Change contributors website border color, size for (right, left, top, bottom) or all. <small><i>Default: none</i></small>.',
                        ),
                    ),
                    array(
                        'id'             => 'website-padding',
                        'title'          => __('Padding', 'mgpc'),
                        'desc'          => __('<small><i>Default: 0px 0px 0px 0px</i></small>', 'mgpc'),
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array('em', 'px'),
                        'units_extended' => 'false',
                        'output'  => array('#mgpc p.website'),
                        'default'            => array(
                            'padding-top'     => '0px',
                            'padding-right'   => '0px',
                            'padding-bottom'  => '0px',
                            'padding-left'    => '0px',
                            'units'          => 'px',
                        ),
                        'hint'      => array(
                            'title'     => 'Padding',
                            'content'   => 'Change contributors website Padding for (right, left, top, bottom) or all. <small><i>Default: 0px 0px 0px 0px</i></small>.',
                        ),
                    ),
                    array(
                        'id'             => 'website-margin',
                        'title'          => __('Margin', 'mgpc'),
                        'desc'          => __('<small><i>Default: 0px 0px 0px 0px</i></small>', 'mgpc'),
                        'type'           => 'spacing',
                        'output'  => array('#mgpc p.website'),
                        'mode'           => 'margin',
                        'units'          => array('px', 'em'),
                        'units_extended' => 'false',
                        'default'            => array(
                            'margin-top'     => '0px',
                            'margin-right'   => '0px',
                            'margin-bottom'  => '5px',
                            'margin-left'    => '0px',
                            'units'          => 'px',
                        ),
                        'hint'      => array(
                            'title'     => 'Margin',
                            'content'   => 'Change contributors website Margin for (right, left, top, bottom) or all. <small><i>Default: 0px 0px 0px 0px</i></small>.',
                        ),
                    ),
                    //  .website

                    array(
                        'id'   =>'divider-mgpc-meta-social-links',
                        'type' => 'divide'
                    ),
                    array(
                        'id'        => 'mgpc-info-styling-social-links',
                        'type'      => 'info',
                        'notice'    => true,
                        'style'     => 'info',
                        'icon'      => 'el-icon-link',
                        'title'     => __('Social Links', 'mgpc'),
                        'desc'      => __('Change authar name typography, background-color, border, margin, padding etc.', 'mgpc')
                    ),
                    array(
                        'id'        => 'social-links-typography',
                        'type'      => 'typography',
                        'title'     => __('Typography', 'mgpc'),
                        'output'  => array('#mgpc .social-links i.mgpc-icon'),
                        'font-style'=> false,
                        'font-weight'=> false,
                        'font-family'=>false,
                        'subsets'   => false,
                        'line-height'=> false,
                        'google'=> false,
                        'text-align'=> false,
                        'default'     => array(
                              'color' => '#AD974F',
                              'font-size'  => '12px',
                        ),
                        'hint'      => array(
                            'title'     => 'Typography',
                            'content'   => 'Change typography of the author email.</small>.',
                        ),
                    ),
                    //  .social-links

                ),
            );

            
            $this->sections[] = array(
                'icon'      => 'el-icon-adjust-alt',
                'title'     => __('Advanced Options', 'mgpc'),
                'heading'   => __('Advanced Options:', 'mgpc'),
                'desc'      => __('<p class="description">Add custom CSS, JS, PHP, HTML additional code as per requirement.</p>', 'mgpc'),
                'fields'    => array(
                    array(
                        'id'        => 'mgpc-additional-code-css',
                        'type'      => 'ace_editor',
                        'title'     => __('CSS', 'mgpc'),
/*                        'subtitle'  => __('Paste your CSS code here.', 'mgpc'),*/
                        'mode'      => 'css',
                        'theme'     => 'monokai',
/*                        'desc'      => 'Possible modes can be found at <a href="http://ace.c9.io" target="_blank">http://ace.c9.io/</a>.',*/
                        'default'   => "/* Custom CSS by MG POST CONTRIBUTORS */",
                        'hint'      => array(
                            'title'     => 'CSS',
                            'content'   => 'Here, You can add custom CSS code as per user requirement. <small><i>Default: empty</i></small>.',
                        ),

                    ),
/*                    array(
                        'id'        => 'mgpc-additional-code-css-print-at',
                        'type'      => 'button_set',
                        'title'     => __('Add CSS on section:', 'mgpc'),
                        'desc'      => __('<small><i>Default: Header</i></small>', 'mgpc'),
                        'options'   => array(
                            '1' => 'Header', 
                            '2' => 'Footer'
                        ), 
                        'default'   => '1'
                    ),
*/                    array(
                        'id'   =>'divider-mgpc-additional-code-css',
                        'type' => 'divide'
                    ),

                    array(
                        'id'        => 'mgpc-additional-code-js',
                        'type'      => 'ace_editor',
                        'title'     => __('JS', 'mgpc'),
                        'mode'      => 'javascript',
                        'theme'     => 'chrome',
                        'default'   => "jQuery(document).ready(function(){\n\n});",
                        'hint'      => array(
                            'title'     => 'JS',
                            'content'   => 'Here, You can add custom JS code as per user requirement. <small><i>Default: empty</i></small>.',
                        ),
                    ),
                    /*array(
                        'id'        => 'mgpc-additional-code-js-print-at',
                        'type'      => 'button_set',
                        'title'     => __('Add JS on section:', 'mgpc'),
                        'desc'      => __('<small><i>Default: Footer</i></small>', 'mgpc'),
                        'options'   => array(
                            '1' => 'Header', 
                            '2' => 'Footer'
                        ), 
                        'default'   => '2'
                    ),*/
                    array(
                        'id'   =>'divider-mgpc-additional-code-js',
                        'type' => 'divide'
                    ),

                    array(
                        'id'        => 'mgpc-additional-code-html-before',
                        'type'      => 'ace_editor',
                        'title'     => __('HTML - Before', 'mgpc'),
                        'mode'      => 'html',
                        'theme'     => 'chrome',
                        'hint'      => array(
                            'title'     => 'HTML - Before',
                            'content'   => 'Here, You can add some useful custom HTML code BEFORE Contributors List. <small><i>Default: empty</i></small>.',
                        ),
                    ),

                    array(
                        'id'   =>'divider-mgpc-additional-code-html-before',
                        'type' => 'divide'
                    ),

                    array(
                        'id'        => 'mgpc-additional-code-html-after',
                        'type'      => 'ace_editor',
                        'title'     => __('HTML - After', 'mgpc'),
                        'mode'      => 'html',
                        'theme'     => 'chrome',
                        'hint'      => array(
                            'title'     => 'HTML - After',
                            'content'   => 'Here, You can add some useful custom HTML code AFTER Contributors List. <small><i>Default: empty</i></small>.',
                        ),
                    ),
                ),
            );

            $this->sections[] = array(
                'icon'      => 'dashicons dashicons-admin-plugins',
                'title'     => __('Our Plugins', 'mgpc'),
                'heading'   => __('Our Plugins:', 'mgpc'),
                'desc'      => __('<p class="description">Check our another usefull wordpress Plugins & Themes...</p>', 'mgpc'),
                'fields'    => array(
                    array(
                            'id'        => 'mgms-our-plugin-list',
                            'type'      => 'callback',
                            'title'     => __('Check our useful Plugins:', 'mgpc'),
                            'subtitle'  => __('We think these are also helpful for you.', 'mgpc'),
                            'callback'  => 'mgms_our_plugin_list'
                    ),
                    array(
                        'id'    => 'opt-divide',
                        'type'  => 'divide'
                    ),
                    array(
                            'id'        => 'mgms-our-theme-list',
                            'type'      => 'callback',
                            'title'     => __('Comming Soon :)', 'mgpc'),
                            'subtitle'  => __('<i>Introducing <strong>Shivaji The Theme 1.0.</strong></i>', 'mgpc'),
                            'callback'  => 'mgms_our_theme_list'
                    ),
                ),
            );

            /*$this->sections[] = array(
                'icon'      => 'el-icon-idea',
                'title'     => __('Support & Reviews', 'mgpc'),
                'heading'   => __('Support & Reviews:', 'mgpc'),
                'desc'      => __('<p class="description">Check our another usefull wordpress plugins...</p>', 'mgpc'),
                'fields'    => array(
                    array(
                            'id'        => 'mgms-dynamic-enqueue-stat',
                            'type'      => 'callback',
                            'title'     => __('Your Dynamic Enqueue Styles:', 'mgpc'),
                            'subtitle'  => __('This is a completely unique field type', 'mgpc'),
                            'desc'      => __('This is created with a callback function, so anything goes in this field. Make sure to define the function though.', 'mgpc'),
                            'callback'  => 'mgpc_submit_reviews'
                    ),
                ),
            );*/
            
            $this->sections[] = array(
                'title'     => __('Import / Export', 'mgpc'),
                'desc'      => __('Import and Export your Redux Framework settings from file, text or URL.', 'mgpc'),
                'icon'      => 'el-icon-refresh',
                'fields'    => array(
                    array(
                        'id'            => 'opt-import-export',
                        'type'          => 'import_export',
                        'title'         => 'Import Export',
                        'subtitle'      => 'Save and restore your Redux options',
                        'full_width'    => false,
                    ),
                ),
            );
        }

        public function setHelpTabs() {

            // Custom page help tabs, displayed using the help API. Tabs are shown in order of definition.
            $this->args['help_tabs'][] = array(
                'id'        => 'mgpc-1',
                'title'     => __('Theme Information 1', 'mgpc'),
                'content'   => __('<p>This is the tab content, HTML is allowed.</p>', 'mgpc')
            );

            $this->args['help_tabs'][] = array(
                'id'        => 'mgpc-2',
                'title'     => __('Theme Information 2', 'mgpc'),
                'content'   => __('<p>This is the tab content, HTML is allowed.</p>', 'mgpc')
            );

            // Set the help sidebar
            $this->args['help_sidebar'] = __('<p>This is the sidebar content, HTML is allowed.</p>', 'mgpc');
        }

        /**

          All the possible arguments for Redux.
          For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments

         * */
        public function setArguments() {

            $this->args = array(
                // TYPICAL -> Change these values as you need/desire
                'opt_name'          => 'mgpc',            // This is where your data is stored in the database and also becomes your global variable name.
                'display_name'      => 'MG Post Contributors',     // Name that appears at the top of your panel
                'display_version'   => '1.3.',  // Version that appears at the top of your panel
                'menu_type'         => 'menu',                  //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                'allow_sub_menu'    => true,                    // Show the sections below the admin menu item or not
                'menu_title'        => __('MGPC Settings', 'mgpc'),
                'page_title'        => __('MGPC Settings', 'mgpc'),
                
                // You will need to generate a Google API key to use this feature.
                // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                'google_api_key' => '', // Must be defined to add google fonts to the typography module
                
                'async_typography'  => false,                    // Use a asynchronous font on the front end or font string
                'admin_bar'         => true,                    // Show the panel pages on the admin bar
                'global_variable'   => '',                      // Set a different name for your global variable other than the opt_name
                'dev_mode'          => false,                    // Show the time the page took to load, etc
                'customizer'        => false,                    // Enable basic customizer support
                //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
                //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

                // OPTIONAL -> Give you extra features
                'page_priority'     => null,                    // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                'page_parent'       => 'themes.php',            // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
                'page_permissions'  => 'manage_options',        // Permissions needed to access the options panel.
                'menu_icon'         => '',                      // Specify a custom URL to an icon
                'last_tab'          => '',                      // Force your panel to always open to a specific tab (by id)
                'page_icon'         => 'icon-themes',           // Icon displayed in the admin panel next to your menu_title
                'page_slug'         => 'mgpc_options',              // Page slug used to denote the panel
                'save_defaults'     => true,                    // On load save the defaults to DB before user clicks save or not
                'default_show'      => false,                   // If true, shows the default value next to each field that is not the default value.
                'default_mark'      => '',                      // What to print by the field's title if the value shown is default. Suggested: *
                'show_import_export' => true,                   // Shows the Import/Export panel when not used as a field.
                
                // CAREFUL -> These options are for advanced use only
                'transient_time'    => 60 * MINUTE_IN_SECONDS,
                'output'            => true,                    // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                'output_tag'        => true,                    // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.
                
                // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                'database'              => '', // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                'system_info'           => false, // REMOVE

                // HINTS
                'hints' => array(
                    'icon'          => 'icon-question-sign',
                    'icon_position' => 'right',
                    'icon_color'    => 'lightgray',
                    'icon_size'     => 'normal',
                    'tip_style'     => array(
                        'color'         => 'light',
                        'shadow'        => true,
                        'rounded'       => false,
                        'style'         => '',
                    ),
                    'tip_position'  => array(
                        'my' => 'top left',
                        'at' => 'bottom right',
                    ),
                    'tip_effect'    => array(
                        'show'          => array(
                            'effect'        => 'slide',
                            'duration'      => '500',
                            'event'         => 'mouseover',
                        ),
                        'hide'      => array(
                            'effect'    => 'slide',
                            'duration'  => '500',
                            'event'     => 'click mouseleave',
                        ),
                    ),
                )
            );


            // SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
            $this->args['share_icons'][] = array(
                'url'   => 'https://github.com/maheshwaghmare',
                'title' => 'Visit us on GitHub',
                'icon'  => 'el-icon-github'
                //'img'   => '', // You can use icon OR img. IMG needs to be a full URL.
            );
            $this->args['share_icons'][] = array(
                'url'   => 'https://www.facebook.com/mgwebthemes',
                'title' => 'Like us on Facebook',
                'icon'  => 'el-icon-facebook'
            );
            $this->args['share_icons'][] = array(
                'url'   => 'https://twitter.com/mwaghmare7',
                'title' => 'Follow us on Twitter',
                'icon'  => 'el-icon-twitter'
            );
            $this->args['share_icons'][] = array(
                'url'   => 'http://in.linkedin.com/in/mwaghmare7',
                'title' => 'Find us on LinkedIn',
                'icon'  => 'el-icon-linkedin'
            );

            

            $this->args['intro_text'] = __('<p>Welcome to <strong>MG Post Contributors</strong>...! Select multiple contributors of single post simply selecting those from post editor window.</p>', 'mgpc');
            

            // Add content after the form.
            $this->args['footer_text'] = __('<p>Thanks for using "MG Post Contributors". Special Thanks to Redux Team.</p>', 'mgpc');
        }

    }
    
    global $reduxConfig;
    $reduxConfig = new mgpc();
}


/*
 * Add scripts ( JS/CSS ) Dynamically
 *
 * @function mgpc_submit_reviews
 */
/*if (!function_exists('mgpc_submit_reviews')):

    function mgpc_submit_reviews($field, $value) { 
    ?>
        <div class="rating-wrapper">
            <div class="rating">
                <ul>
                    <li class="star star-one el-icon-star-empty mg-icon"></li>
                    <li class="star star-two el-icon-star-empty mg-icon"></li>
                    <li class="star star-three el-icon-star-empty mg-icon"></li>
                    <li class="star star-four el-icon-star-empty mg-icon"></li>
                    <li class="star star-five el-icon-star-empty mg-icon"></li>
                </ul>
            </div>
            <div class="result">
                <i class="smily">:)</i>
            </div>
            <div class="form">
                <input type=""
            </div>
        </div>
    <?php
    }

endif;*/




/*
 *  Plugin List
 *
 *--------------------------------------------------*/
if(!function_exists('mgms_our_plugin_list')):

    function mgms_our_plugin_list() { ?>
    <div class="plugin-list-wrapper">
        <ul class="plugin-list">
            <li class="plugin">
                <img src="<?php echo plugins_url( "../../images/intro-mg-parallax-slider.png", __FILE__ ); ?>" />
                <h2 class="title">MG Post Contributors</h2>
                <p class="description">Use this plugin to set multiple contributors for single post. Simply selecting contributors check boxes at Post Editor. It show list of users with checkboxes and show them at POST.</p>
                <P class="links">
                    <a style="float: left;" href="http://wordpress.org/plugins/mg-parallax-slider">Wordpress Repository</a>
                    <a style="float: right;" href="http://wordpress.org/plugins/mg-parallax-slider">Plugin Home</a>
                </p>
            </li>
            <li class="plugin">
                <img src="<?php echo plugins_url( "../../images/intro-mg-post-contributors.png", __FILE__ ); ?>" />
                <h2 class="title">MG Parallax Slider</h2>
                <p class="description">Create parallax slider for your website. It provide ultimate admin panel for slide customization. It has same admin panel for customization.</p>
                <P class="links">
                    <a style="float: left;" href="http://wordpress.org/plugins/mg-parallax-slider">Wordpress Repository</a>
                    <a style="float: right;" href="http://wordpress.org/plugins/mg-parallax-slider">Plugin Home</a>
                </p>
            </li>
        </ul>
    <?php }
endif;



 
/*
 *  Theme List
 *
 *--------------------------------------------------*/
if(!function_exists('mgms_our_theme_list')):

    function mgms_our_theme_list() { ?>
    <div class="theme-list-wrapper">
        <ul class="theme-list">
            <li class="theme">
                <img src="<?php echo plugins_url( "../../images/shivaji-the-theme-1.0.png", __FILE__ ); ?>" style="width: 100% ! important;" />
            </li>
        </ul>
    </div>

    <?php }
endif;





/*
 *  Help - Skip Roles
 *
 *--------------------------------------------------*/
if(!function_exists('help_exclude_roles')):

    function help_exclude_roles() { ?>
    <div class="theme-list-wrapper">
        <ul class="theme-list">
            <li class="theme">
                <img src="<?php echo plugins_url( "../../images/help-tab-exclude-roles.png", __FILE__ ); ?>" style="width: 100% ! important;" />
            </li>
        </ul>
    </div>

    <?php }
endif;


/*
 *  Help - Skip Roles
 *
 *--------------------------------------------------*/
if(!function_exists('help_structure_settings')):

    function help_structure_settings() { ?>
    <div class="theme-list-wrapper">
        <ul class="theme-list">
            <li class="theme">
                <img src="<?php echo plugins_url( "../../images/help-tab-structure-settings.png", __FILE__ ); ?>" style="width: 100% ! important;" />
            </li>
        </ul>
    </div>

    <?php }
endif;

/*
 *  Help - Skip Roles
 *
 *--------------------------------------------------*/
if(!function_exists('help_styling')):

    function help_styling() { ?>
    <div class="theme-list-wrapper">
        <ul class="theme-list">
            <li class="theme">
                <img src="<?php echo plugins_url( "../../images/help-tab-styling.png", __FILE__ ); ?>" style="width: 100% ! important;" />
            </li>
        </ul>
    </div>

    <?php }
endif;


/*
 *  Help - Skip Roles
 *
 *--------------------------------------------------*/
if(!function_exists('show_all_social_profile_links')):

    function show_all_social_profile_links() {
        /*global $mgpc;
        $p = $mgpc['opt-check-sortable'];*/
//        print_r($mgpc['opt-check-sortable']);

        //apply_filters('mgpc_social_profiles', $p );

/*        $mgpc_profiles = array(
            "facebook",
            "twitter",
            "google-plus",
            "wordpress",
            "linkedin",
            "youtube",
            "pinterest",
            "instagram",
            "tumblr",
            "flickr",
            "skype"
        );
*/
        /*if(has_action('mgpc_show_all_social_profile_links')) {
            do_action("mgpc_show_all_social_profile_links" );
        }
        else {
            echo "No profiles found.";
        }*/
    }
endif;


?>