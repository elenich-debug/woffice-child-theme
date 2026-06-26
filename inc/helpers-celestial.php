<?php
/**
 * This file contains all functions used by Woffice.
 * They're available in any PHP script loaded when the Woffice theme is enabled
 * All functions can be overwritten by a child theme
 * @author Xtendify
 */

 if(!function_exists('woffice_celestial_filter_xprofile_fields')) {

    function woffice_celestial_filter_xprofile_fields($user_ID) {
        
        if (!woffice_bp_is_active('xprofile')) {
            return;
        }

        $html = '';
        global $members_template;

        if (!isset($members_template->member) && !empty($members_template)) {
            $members_template->member = get_userdata($user_ID);
        }

        // We fetch all the BuddyPress fields
        bp_get_member_profile_data(array('user_id' => $user_ID));

        $fields_values = array();
        if (isset($members_template->member) && $members_template->member->profile_data) {
            $fields_values = $members_template->member->profile_data;
        }

        //Add wordpress email to the array of fields fields
        $wordpress_email_field = array();
        $wordpress_email_field['field_id'] = null;
        $wordpress_email_field['name'] = 'wordpress_email';
        $wordpress_email_field['field_type'] = 'email';
        $wordpress_email_field['field_data'] = '';


        $fields_values = array_merge(array('wordpress_email' => $wordpress_email_field), $fields_values);

	    $formatted_social_items = array();

	    $social_fields_available = woffice_get_social_fields_available();

        $fields_list_items = array();
       
        foreach ($fields_values as $field_name => $field) {

            if ($field_name == 'user_login' || $field_name == 'user_nicename' || $field_name == 'user_email')
                continue;

            // Skip display name used by BuddyPress
            if ($field['field_id'] == 1 && !apply_filters('woffice_include_display_name_in_members_loop_fields', false))
                continue;

            $field_type = $field['field_type'];
            $field_show = woffice_get_theming_option('buddypress_' . $field_name . '_display');
            $field_icon = woffice_get_theming_option('buddypress_' . $field_name . '_icon');

            // We check if the field have to be displayed
            if (!$field_show)
                continue;

            if ($field_name != 'wordpress_email') {
                $field_value = bp_get_profile_field_data('field=' . $field_name . '&user_id=' . $user_ID);
            } else {
                $user_info = get_userdata($user_ID);
                if(!empty($user_info->user_email)){
                    $field_value = "<a href='mailto:" . $user_info->user_email . "' rel='nofollow'>$user_info->user_email</a>";
                }
            }

            // We check if the field is empty
            if (empty($field_value))
                continue;

	        // Try to understand if the field is a social link
	        $social_field     = false;
	        $field_name_lower = strtolower( $field_name );

	        foreach ( $social_fields_available as $socials_detectable_key => $socials_detectable_field ) {

		        if ( strpos( $field_name_lower, $socials_detectable_key ) !== false ) {

			        if ( empty( $field_icon ) ) {
				        $field_icon = $socials_detectable_field['icon'];
			        }

			        $social_field = true;
			        break;
		        }

	        }

             // We try to set a default icon
            if (empty($field_icon) && !$social_field) {
                $field_icon = 'fa-arrow-right';

                if ($field_type == 'datebox') {
                    $field_icon = 'fa-calendar';
                } elseif ($field_type == 'email') {
                    $field_icon = 'fa-envelope';
                }
            }

            $str_field_html = '<li class="profile-item">';

	        // We format the field
	        if ( ! $social_field ) {
		        if ( $field_type == 'url' || $field_type == 'web' || $field_type == 'email' || is_array( $field_value ) ) {

                    $str_field_html .= '<span class="field-icon" ><i class="' . woffice_convert_fa4_to_fa5($field_icon) . '"></i></span>';
                    $str_field_html .= '<span class="field-detail">';

			        if ( is_array( $field_value ) ) {
                        $str_field_html .= implode( ", ", $field_value );
			        } else {
                        $str_field_html .= $field_value;
			        }
                    $str_field_html .= '</span>';
                    $str_field_html .= '</li>';
                    $html .= $str_field_html;

		        } else {
                    $str_field_html .= '<span class="field-icon"><i class="'. woffice_convert_fa4_to_fa5($field_icon) .'"></i></span>';
                    $str_field_html .= '<span class="field-detail">';
                    $str_field_html .= woffice_auto_link( $field_value, $field_name );
                    $str_field_html .= '</span>';
                    $str_field_html .= '</li>';
                    $html .= $str_field_html;
		        }
                array_push($fields_list_items,$str_field_html);
	        } else {
		        $field_string = '<a href="' . $field_value . '" target="_blank" class="wo_social_icon '.$field_name.' "><i class="'. woffice_convert_fa4_to_fa5($field_icon) .' wo_icon"></i></a>';
		        $formatted_social_items[ $field_name ] = $field_string ;
	        }
        }
        

        $result = array(
            "general_field" => $fields_list_items,
            "social_field" => $formatted_social_items,
        );

        return $result;
    }
 }

 if(!function_exists('woffice_list_celestial_xprofile_fields')) {
    /**
     * List of BuddyPress fields for the icons in the main members page
     *
     * @param int $user_ID
     * @param bool $is_printable
     *
     * @return string (HTML markup)
     */
    function woffice_list_celestial_xprofile_fields($user_ID, $is_printable = true){
        
        if (!woffice_bp_is_active('xprofile')) {
            return;
        }
        $updated_fields = woffice_celestial_filter_xprofile_fields($user_ID);
       
        $html = '';
        
        if (isset($updated_fields['general_field']) && !empty( $updated_fields ) ) {
            $first_genlist_list = array_splice($updated_fields['general_field'], 0, 3);
            $html .= '<ul class="woffice-xprofile-list">';
                foreach ($first_genlist_list as $field_key => $field_item) {
                    $html .=$field_item;
                }
            
                $remaining_members = $updated_fields['general_field'];
                if($remaining_members){
                    $html .= '<a class="wo_remaining_member_toggle"> +'. count($remaining_members).'</a>';
                }
                $html .= '<div class="remaining_m_container woffice-xprofile-list-remaining list-group">';
                    foreach ($updated_fields['general_field'] as $field_key => $field_item) {
                        $html .=$field_item;
                    }
                $html .= '</div>';
            
            $html .= '</ul>';
        }

         // We render the list of social fields
	    if (isset($updated_fields['social_field']) && !empty( $updated_fields ) ) {

            $first_social_list = array_splice($updated_fields['social_field'], 0, 3);
            $html .= '<div class="member-xprofile-social-items">';

		    foreach ( $first_social_list as $field ) {
			    // Already escaped by BuddyPress multiple times
			    $html .= $field;
		    }

            $remaining_social = $updated_fields['social_field'];

            if($remaining_social){
                $html .= '<a class="wo_remaining_member_toggle"> +'. count($remaining_social).'</a>';
            }

                $html .= '<div class="member-xprofile-social-remainingitems remaining_m_container">';
                    foreach ( $updated_fields['social_field'] as $field ) {
                        // Already escaped by BuddyPress multiple times
                        $html .= $field;
                    }
                $html .= '</div>';
            $html .= '</div>';
	    }

        if ($is_printable) {
	        if(function_exists('woffice_echo_output')){
                woffice_echo_output($html);
            }
        }
	    else {
	        return addslashes($html);
        }
        
    }
}
