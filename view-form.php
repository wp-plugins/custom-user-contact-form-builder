<?php
/*Controls registration form behavior on the front end*/
global $wpdb;
$cfp_option=$wpdb->prefix."cfp_option";
$qry="select `value` from $cfp_option where fieldname='cfp_theme'";
$cfp_theme = $wpdb->get_var($qry);
include 'view-form-'.$cfp_theme.'.php';

