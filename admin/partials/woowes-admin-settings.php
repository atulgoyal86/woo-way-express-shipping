<div class="wrap">
   <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
   
   <form action="options.php" method="post">
   		<?php settings_fields( 'woowes_setting' ); ?>
        <h2 class="title" style="">General Settings</h2>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">Test Mode</th>
                    <td>
                        <input type="checkbox" id="woowes_setting_options_test_mode" name="woowes_setting_options[test_mode]" value="1" <?php echo ($woowes_options['test_mode'] == 1)?'checked':''; ?>>
                    </td>
                </tr>

                <tr>
                    <th scope="row">Authorization</th>
                    
                </tr>
                <tr>
                    <th scope="row">User Name</th>
                    <td>
                        <input type="text" id="woowes_settings_auth_username" style="width:50%" name="woowes_setting_options[auth_user_name]" value="<?php echo $woowes_options['auth_user_name']??''; ?>">
                    </td>
                </tr>

                <tr>
                    <th scope="row">Password</th>
                    <td>
                        <input type="password" id="woowes_settings_auth_password" style="width:50%" name="woowes_setting_options[auth_password]" value="<?php echo $woowes_options['auth_password']??''; ?>">
                    </td>
                </tr>

                <tr>
                    <th scope="row"></th>
                    <td>
                        <button type="button" id="woowes_settings_auth_login" class="button button-primary">Login</button>
                    </td>
                </tr>

                <tr>
                    <th scope="row">Access Token</th>
                    <td>
                        <input type="text" id="woowes_settings_auth_token" readonly style="width:50%" name="woowes_setting_options[auth_access_token]" value="<?php echo $woowes_options['auth_access_token']??''; ?>">
                    </td>
                </tr>

                
                
            </tbody>
        </table>

        <?php 
        $other_attributes = array( 'id' => 'woowes_settings_submit' );
        submit_button( 'Save Settings','primary', 'submit', true, $other_attributes ); ?>
   </form>
</div>