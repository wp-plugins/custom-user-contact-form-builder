<?php
/*Controls custom field creation in the dashboard area*/
global $wpdb;
$textdomain = 'contact-form-pro';
$cfp_option=$wpdb->prefix."cfp_option";
$path =  plugin_dir_url(__FILE__); 
if(isset($_REQUEST['saveoption']))
{
	$retrieved_nonce = $_REQUEST['_wpnonce'];
	if (!wp_verify_nonce($retrieved_nonce, 'save_cfp_global_setting' ) ) die( 'Failed security check' );

	if(!isset($_REQUEST['enable_captcha'])) $_REQUEST['enable_captcha']='no';
	if(!isset($_REQUEST['admin_notification'])) $_REQUEST['admin_notification']='no';
	if(!isset($_REQUEST['userip'])) $_REQUEST['userip']='no';
	cfp_add_option( 'enable_captcha', $_REQUEST['enable_captcha']);
	cfp_add_option( 'public_key', $_REQUEST['publickey']);
	cfp_add_option( 'private_key', $_REQUEST['privatekey']);
	cfp_add_option( 'adminnotification', $_REQUEST['admin_notification']);
	cfp_add_option( 'adminemail', $_REQUEST['admin_email']);
	cfp_add_option( 'from_email', $_REQUEST['from_email']);	
	cfp_add_option( 'userip', $_REQUEST['userip']);	
	cfp_add_option( 'cfp_theme', $_REQUEST['cfp_theme']);
}

$qry="SELECT `value` FROM $cfp_option WHERE fieldname='public_key'";
$public_key = $wpdb->get_var($qry);
$qry="SELECT `value` FROM $cfp_option WHERE fieldname='private_key'";
$private_key = $wpdb->get_var($qry);
$qry="SELECT `value` FROM $cfp_option WHERE fieldname='adminemail'";
$admin_email = $wpdb->get_var($qry);
$qry="SELECT `value` FROM $cfp_option WHERE fieldname='from_email'";
$from_email = $wpdb->get_var($qry);
$qry="SELECT `value` FROM $cfp_option WHERE fieldname='cfp_theme'";
$cfp_theme = $wpdb->get_var($qry);
?>

<div class="cfp-main-form">
  <div class="cfp-form-heading">
    <h1><?php _e( 'Global Settings', $textdomain ); ?></h1>
  </div>
  <form method="post">
  <div class="option-main cfp-form-setting">
      <div class="user-group cfp-form-left-area">
        <div class="cfp-label">
          <?php _e( 'Theme:', $textdomain ); ?>
        </div>
      </div>
      <div class="user-group-option cfp-form-right-area">
        <select name="cfp_theme" id="cfp_theme">
        <option value="classic" <?php if($cfp_theme=='classic')echo 'selected';?>>Classic</option>
        <option value="default" <?php if($cfp_theme=='default')echo 'selected';?>>Default</option>
        </select>
      </div>
    </div>
    
    <div class="option-main cfp-form-setting">
      <div class="user-group cfp-form-left-area">
        <div class="cfp-label">
          <?php _e( 'Enable Recaptcha:', $textdomain ); ?>
        </div>
      </div>
      <div class="user-group-option cfp-form-right-area">
        <input name="enable_captcha" id="enable_captcha" type="checkbox" class="upb_toggle" value="yes" <?php if (checkfieldname("enable_captcha","yes")==true){ echo "checked";}?> style="display:none;"/>
        <label for="enable_captcha"></label>
      </div>
    </div>
    <div class="option-main ">
      <div id="captcha_fun" <?php if (checkfieldname("enable_captcha","yes")==true){ echo 'style="display:block"';}else{echo 'style="display:none"';}?>>
        <div class="option-main cfp-form-setting">
          <div class="user-group cfp-form-left-area">
            <div class="cfp-label">
              <?php _e( 'Public Key:', $textdomain ); ?>
            </div>
          </div>
          <div class="user-group-option cfp-form-right-area">
            <input type="text" name="publickey" id="publickey" value="<?php if(isset($public_key)) echo $public_key; ?>" />
          </div>
        </div>
        <div class="option-main cfp-form-setting">
          <div class="user-group cfp-form-left-area">
            <div class="cfp-label">
              <?php _e( 'Private Key:', $textdomain ); ?>
            </div>
          </div>
          <div class="user-group-option cfp-form-right-area">
            <input type="text" name="privatekey" id="privatekey" value="<?php if(isset($private_key)) echo $private_key; ?>" />
          </div>
        </div>
      </div>
    </div>
    
    <div class="option-main cfp-form-setting">
      <div class="user-group cfp-form-left-area">
        <div class="cfp-label">
          <?php _e( 'Capture IP and Browser Info:', $textdomain ); ?>
        </div>
      </div>
      <div class="user-group-option cfp-form-right-area">
        <input name="userip" id="userip" type="checkbox" class="upb_toggle" value="yes" <?php if (checkfieldname("userip","yes")==true){ echo "checked";}?> style="display:none;" />
        <label for="userip"></label>
      </div>
    </div>
    
    <div class="option-main cfp-form-setting">
      <div class="user-group cfp-form-left-area">
        <div class="cfp-label">
          <?php _e( 'Admin Email Notification:', $textdomain ); ?>
        </div>
      </div>
      <div class="user-group-option cfp-form-right-area">
        <input name="admin_notification" id="admin_notification" type="checkbox" class="upb_toggle" value="yes" <?php if (checkfieldname("adminnotification","yes")==true){ echo "checked";}?> style="display:none;"/>
        <label for="admin_notification"></label>
      </div>
    </div>
         <div id="notification_fun" <?php if (checkfieldname("adminnotification","yes")==true){ echo 'style="display:block"';}else{echo 'style="display:none"';}?>>
    <div class="option-main cfp-form-setting">
      <div class="user-group cfp-form-left-area">
        <div class="cfp-label">
          <?php _e( 'Admin Email:', $textdomain ); ?>
        </div>
      </div>
      <div class="user-group-option cfp-form-right-area">
        <input type="text" name="admin_email" id="admin_email" value="<?php if(isset($admin_email)) echo $admin_email; ?>" />
      </div>
    </div>
    </div>
    
    <div class="option-main cfp-form-setting">
      <div class="user-group cfp-form-left-area">
        <div class="cfp-label">
          <?php _e( 'From Email:', $textdomain ); ?>
        </div>
      </div>
      <div class="user-group-option cfp-form-right-area">
        <input type="text" name="from_email" id="from_email" value="<?php if(isset($from_email)) echo $from_email; ?>" />
      </div>
    </div>
    
    <br>
    <br>
    <div class="cfp-form-footer">
      <div class="cfp-form-button">
      <?php wp_nonce_field('save_cfp_global_setting'); ?>
        <input type="submit"  class="button-primary" value="Save" name="saveoption" id="saveoption" />
        <a href="admin.php?page=cfp_manage_forms" class="cancel_button">Cancel</a>
      </div>
    </div>
  </form>
</div>
<script>
jQuery( "#enable_captcha" ).click(function() {
 a = jQuery(this).is(':checked'); 
 if(a==true)
 {
	jQuery("#captcha_fun").show(500); 
 }
 else
 {
	jQuery("#captcha_fun").hide(500); 
 }
});
</script>

<script>
jQuery( "#admin_notification" ).click(function() {
 a = jQuery(this).is(':checked'); 
 if(a==true)
 {
	jQuery("#notification_fun").show(500); 
 }
 else
 {
	jQuery("#notification_fun").hide(500); 
 }
});
</script>

<?php
function cfp_add_option($fieldname,$value)
{
  global $wpdb;
  $cfp_option=$wpdb->prefix."cfp_option";
  $update="update $cfp_option set `value`='".$value."' where fieldname='".$fieldname."'";
  $wpdb->query($update);
}

function checkfieldname($fieldname,$value)
{
	global $wpdb;
	$cfp_option=$wpdb->prefix."cfp_option";
	$select="select `value` from $cfp_option where fieldname='".$fieldname."' and `value`='".$value."'";
	$data = $wpdb->get_var($select);
	
	if($data==$value)
	{
		return true;
	}
	else
	{
		return false;
	}
}
?>