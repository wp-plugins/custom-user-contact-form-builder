<?php
/*Used during custom field creation - Cross checks if the custom field being created already exists or not*/
global $wpdb;
$cfp_forms =$wpdb->prefix."cfp_forms";
$textdomain = 'contact-form-pro';
$cfp_fields =$wpdb->prefix."cfp_fields";

/*if (!preg_match('/^[a-zA-Z0-9 ]+$/', $_POST['name'])) {
    //Rejected String
	echo '<div style=" color:red">'.__('Warning! Special characters are not allowed in Form Labels.',$textdomain ).'</div>';	
}*/
if(isset($_POST['name']) && trim($_POST['name'])=="")
{
	echo '<div style=" color:red">'.__('Warning! Form label is required. Please enter a unique label.',$textdomain ).'</div>';		
}

if($_POST['prev']!='new')
{
	$qry = "select count(*) from $cfp_forms where form_name ='".$_POST['name']."' and form_name !='".$_POST['prev']."'";
	$result = $wpdb->get_var($qry);
	if($result!=0)
	{
		echo '<div style=" color:red">'.__('Warning! Form label already exists. Please choose a unique label.',$textdomain).'</div>';	
	}
}
else
{
	$qry = "select count(*) from $cfp_forms where form_name ='".$_POST['name']."'";
	$result = $wpdb->get_var($qry);
	if($result!=0)
	{
		echo '<div style=" color:red">'.__('Warning! Form label already exists. Please choose a unique label.',$textdomain).'</div>';	
	}
}

?>