<?php
class ce21_el_extend_quick_edit{

    private static $instance = null;

    public function __construct(){

        add_action('quick_edit_custom_box', array($this, 'display_quick_edit_custom'), 10, 2); //output form elements for quickedit interface
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts_and_styles')); //enqueue admin script (for prepopulting fields with JS)
        add_action('save_post', array($this, 'save_post'), 10, 2); //call on save, to update metainfo attached to our metabox

    }



    //enqueue admin js to pre-populate the quick-edit fields
    public function enqueue_admin_scripts_and_styles(){
        wp_enqueue_script('quick-edit-script', plugin_dir_url(__FILE__) . '../admin/js/post-quick-edit-script.js', array('jquery','inline-edit-post' ));
    }
    //Display our custom content on the quick-edit interface, no values can be pre-populated (all done in JS)
    public function display_quick_edit_custom($column,$post_type){

        $html = '';
        wp_nonce_field('post_metadata', 'post_metadata_field');

        global $post;
        global $wpdb;
        $table_name_ce21 		=  $wpdb->prefix . 'membership_types_ce21';
        $Memberships_Data_ce21 	=  $wpdb->get_results("SELECT * FROM ".$table_name_ce21." ORDER BY id ASC");
       // $details 				=  get_post_meta( $post->ID, '_post_authentication_custom_metabox_ce21', true );
        //print_r($details);
        //output post featured checkbox
        $CEI=1;
        if($column == 'membership_plan_ce21'){
            /*echo '<fieldset class="inline-edit-col-center"><pre>';
                print_r( $post->ID );
                echo '<br>';
                print_r( $Memberships_Data_ce21 );
                echo '<br>';
                //print_r( $details );
                echo '<br>';
            echo '</pre></fieldset>';*/

            $html .= '<fieldset class="inline-edit-col-center"  style="max-width:400px;">';
                $html .= '<div class="inline-edit-group wp-clearfix">';
                    $html .= '<span class="title inline-edit-categories-label">Groups</span>';
                    $html .= '<ul class="cat-checklist category-checklist custom_group_listing">';
                    $html .= '<label><input type="checkbox" id="ckbCheckAll" /> Select All </label>';
                        foreach ($Memberships_Data_ce21 as $key => $value) {
                            $html .= '<label>';
                                $html .= '<input id="qbe_group'. esc_attr($CEI) .'" class="qbe_group be_group" type="checkbox" class="radio" value="'. esc_attr($value->membershipTypeId) .'" name="_post_authentication_custom_metabox_ce21[]" />';
                                $html .= '  '.esc_html($value->membershipName);
                            $html .= '</label>';
                            $CEI++;
                        }
                    $html .= '</ul>';
                $html .= '</div>';
            $html .= '</fieldset>';
        }

        echo $html;
    }

    //saving meta info (used for both traditional and quick-edit saves)
    public function save_post($post_id){

        $post_type = get_post_type($post_id);

        if($post_type == 'post'){

            //check nonce set
            if(!isset($_POST['post_metadata_field'])){
                return false;
            }

            //verify nonce
            if(!wp_verify_nonce($_POST['post_metadata_field'], 'post_metadata')){
                return false;
            }


            //if not autosaving
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return false;
            }

            //all good to save
            $new_values = ( isset( $_POST[ '_post_authentication_custom_metabox_ce21' ] ) && !empty( $_POST[ '_post_authentication_custom_metabox_ce21' ] ) ) ? $_POST[ '_post_authentication_custom_metabox_ce21' ] : NULL;
            update_post_meta( $post_id, '_post_authentication_custom_metabox_ce21',$new_values );
        }

    }
    //gets singleton instance
    public static function getInstance(){
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }


}
$el_extend_quick_edit = ce21_el_extend_quick_edit::getInstance();