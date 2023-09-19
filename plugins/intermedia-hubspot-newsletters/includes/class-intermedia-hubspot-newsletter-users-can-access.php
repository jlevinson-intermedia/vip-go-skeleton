<?php 
class IntermediaHubspotUserAccess 
{
    public static function allow_access_hubspot_plugin_field( $user )
    {
        $allow = esc_attr( get_user_meta( $user->ID, 'hubspot_newsletter_plugin_access', true ) );
        if($allow == 'allow') {
            $checked = 'checked';
        }else {
            $checked = '';
        }
?>
    <table class="form-table">
        <tr>
            <th>
                <label for="hubspot_newsletter_plugin_access">Allow access to Hubspot Newsletter Plugin</label>
            </th>
            <td>
                <input type="checkbox"
                       class="regular-text ltr"
                       id="hubspot_newsletter_plugin_access"
                       name="hubspot_newsletter_plugin_access"
                       value="allow" <?php echo $checked;?>>
            </td>
        </tr>
        </table>
<?php 
    }

    public static function save_allow_access_hubspot_plugin_field( $user_id ) 
    {
        if(!current_user_can('manage_options', $user_id)) {
            return false; 
        }

        return update_user_meta(
            $user_id, 
            'hubspot_newsletter_plugin_access', 
            $_POST['hubspot_newsletter_plugin_access']
        );
    }
}

add_action('edit_user_profile', array('IntermediaHubspotUserAccess', 'allow_access_hubspot_plugin_field'));
add_action('edit_user_profile_update', array('IntermediaHubspotUserAccess', 'save_allow_access_hubspot_plugin_field'));
?>