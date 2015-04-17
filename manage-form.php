<?php
/*Controls custom field creation in the dashboard area*/
global $wpdb;
$textdomain = 'contact-form-pro';
$cfp_forms =$wpdb->prefix."cfp_forms";
$cfp_entries =$wpdb->prefix."cfp_entries";
$path =  plugin_dir_url(__FILE__); 
if(!empty($_POST['selected']) && isset($_POST['copy']))
{
	$retrieved_nonce = $_REQUEST['_wpnonce'];
	if (!wp_verify_nonce($retrieved_nonce, 'manage_cfp_form' ) ) die( 'Failed security check' );

	$ids = implode(',',$_POST['selected']);
	$query = "select * from $cfp_forms where id in($ids)";
	$results = $wpdb->get_results($query);
	if(!empty($results))
	{
	foreach($results as $entry)
	{
		$qry = "insert into $cfp_forms values('','".$entry->form_name."','".$entry->form_desc."','".$entry->form_type."','".$entry->custom_text."','".$entry->cfp_welcome_email_subject."','".$entry->success_message."','".$entry->cfp_welcome_email_message."','".$entry->redirect_option."','".$entry->redirect_page_id."','".$entry->redirect_url_url."','".$entry->send_email."','".$entry->form_option."')";	
		$wpdb->query($qry);	
	}
	}
}

if(!empty($_POST['selected']) && isset($_POST['delete']))
{
	$retrieved_nonce = $_REQUEST['_wpnonce'];
	if (!wp_verify_nonce($retrieved_nonce, 'manage_cfp_form' ) ) die( 'Failed security check' );

	$ids = implode(',',$_POST['selected']);
	$query = "delete from $cfp_forms where id in($ids)";
	$wpdb->get_results($query);
}

$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
$limit = 10; // number of rows in page
$offset = ( $pagenum - 1 ) * $limit;
$qry = "SELECT * FROM $cfp_forms";
$total = $wpdb->get_var( "SELECT count(*) FROM $cfp_forms" );
$num_of_pages = ceil( $total / $limit );
$entries = $wpdb->get_results( "SELECT * FROM $cfp_forms order by id asc LIMIT $offset, $limit" );
?>

<form name="forms" id="forms" method="post" action="admin.php?page=cfp_manage_forms" >
<?php wp_nonce_field('manage_cfp_form'); ?>
<div class="ucf_pro_banner" style="margin-bottom:0 !important; overflow:visible;">
<div class="analytics_banner"><a href="admin.php?page=cfp_analytics_demo"><img src="<?php echo $path;?>images/analytics_banner.jpg" /></a></div>

	<div class="banner" id="bannerclose">
       <a href="admin.php?page=cfp_Pro"><img src="<?php echo $path;?>images/ucf_form_banner.jpg" /></a>
     </div>


</div>

  <div class="cfp-main-form" style=" margin-top:15px;">
    <div class="cfp-main-form-top-area">
      <div class="cfp-form-name-heading">
        <h1><?php _e( 'All Forms', $textdomain ); ?></h1>
      </div>
      <div class="cfp-form-name-buttons">
        <div class="cfp-setting"><a href="admin.php?page=cfp_settings"><img src="<?php echo $path; ?>images/cfp-setting-button.png"></a></div>
      </div>
    </div>
    <div class="cfp-new-buttons"><span class="cfp-add-new-button"><a href="admin.php?page=cfp_add_form"><?php _e( 'Add New', $textdomain ); ?></a></span> <span class="cfp-duplicate-button" style="display:none;">
      <input type="submit" name="copy" id="copy" value="Duplicate" >
      </span> <span class="cfp-remove-button" style="display:none;">
      <input type="submit" name="delete" id="delete" value="Remove" onClick="return popup()">
      </span>
      
      
      </div>
  </div>
  <div class="cfp-row-result-main">
   <!--HTML when there is not create any form--> 
      <div class="cfp-row-result">
      <div class="add-new-form">
        <a href="admin.php?page=cfp_add_form">
          <div class="theme-screenshot">
            <span></span>
           
          </div>
           <h2 class="theme-name">Add A <br> New Form</h2>
          
        </a>
      </div>
      </div>
   
    <!--HTML when there is not create any form--> 
  
  
    <?php
if(empty($entries))
{
?>
    <!--HTML when there is not create any form-->  
    <ul id="sortable" class="cfp_entries">
      <li class="rows">
        <div class="cols"><?php _e( 'You have not created any forms yet. Once you have created a new form, it will appear here.', $textdomain ); ?></div>
      </li>
    </ul>
    <?php 
}
else
{
$i = 1;	
foreach($entries as $row)
{
	$qry = "select count(*) from $cfp_entries where form_id=".$row->id;
	$submission = $wpdb->get_var($qry);
	if($submission>100)
	{
		$submission='99+';
	}
?>
    <!--HTML when there are already custom fields associated with selected user role-->  
    <div class="cfp-row-result js-zeroclipboard-container">
      <div class="cfp-form-name">
        <?php 
$length = strlen($row->form_name);
if($length<=12){echo $row->form_name;}
else
{
$Valuehalf = substr($row->form_name, 0, 12);
echo $Valuehalf.'...';
}
?>
      </div>
      <div class="cfp-form-check">
        <input type="checkbox" name="selected[]" value="<?php echo $row->id;?>">
      </div>
      <div class="cfp-form-submissions"><?php _e( 'Submissions', $textdomain ); ?></div>
      <div class="cfp-form-rest"><a href="admin.php?page=cfp_entries&form_id=<?php echo $row->id;?>"><?php echo $submission;?></a></div>
      <div class="cfp-form-shortcode"><?php _e( 'Shortcode', $textdomain ); ?></div>
      <div class="cfp-form-shortcode-name cfp-copy" onMouseOver="cfptooltip1(this)" onClick="cfptooltip2(this)" title="Click to copy to clipboard." data-clipboard-text="[CFP_Form id='<?php echo $row->id;?>']">[CFP_Form id='<?php echo $row->id;?>']</div>
      <div class="cfp-form-shortcode-type" style="visibility:hidden;"><?php _e( 'Type', $textdomain ); ?></div>
      <div class="cfp-form-shortcode-type-name" style="visibility:hidden;">
        <?php if($row->form_type=='reg_form')echo 'Registration'; else echo 'Contact Form';?>
      </div>
      <div  class="cfp-row-result-button-area">
        <div class="cfp-row-result-edit-button"><a href="admin.php?page=cfp_add_form&id=<?php echo $row->id;?>"><?php _e( 'Edit', $textdomain ); ?></a></div>
        <div class="cfp-row-result-preview-button"><a href="admin.php?page=cfp_manage_form_fields&form_id=<?php echo $row->id;?>"><?php _e( 'Fields', $textdomain ); ?></a></div>
      </div>
    </div>
    <?php
		$i++;
}
}

$args = array(
	'base'               => add_query_arg( 'pagenum', '%#%' ),
	'format'             => '',
	'total'              => $num_of_pages,
	'current'            => $pagenum,
	'show_all'           => False,
	'end_size'           => 1,
	'mid_size'           => 2,
	'prev_next'          => True,
	'prev_text'          => __('Prev'),
	'next_text'          => __('Next'),
	'type'               => 'array',
	'add_args'           => False,
	'add_fragment'       => '',
	'before_page_number' => '',
	'after_page_number'  => ''
);

$page_links = paginate_links( $args );

$count	= count($page_links);
$prev = strpos($page_links[0],'Prev');
$next = strpos($page_links[$count-1],'Next');

if($prev!=false)
{
?>
    <div class="cfp-row-result pagination prev"> <?php echo $page_links[0]; ?> </div>
    <?php
}
if($next!=false)
{
?>
    <div class="cfp-row-result pagination next"> <?php echo $page_links[$count-1]; ?> </div>
    <?php
}
?>
  </div>
</form>
<script>
    jQuery(document).ready(function () {
        var a = jQuery('.cfp-row-result-main .cfp-row-result');
        for (var i = 0; i < a.length; i += 4) {
            a.slice(i, i + 4).wrapAll('<div class="cfp-form-cutom-row"></div>');
        }
    });
    jQuery('input[name="selected[]"]').click(function () {
        var atLeastOneIsChecked = jQuery('input[name="selected[]"]:checked').length > 0;
        if (atLeastOneIsChecked == true) {
            jQuery('.cfp-duplicate-button').show(500);
            jQuery('.cfp-remove-button').show(500);
        } else {
            jQuery('.cfp-duplicate-button').hide(500);
            jQuery('.cfp-remove-button').hide(500);
        }
    });
	jQuery('.cfp-row-result-main a').mouseover(function() {
	jQuery(this).children('h2').css('color','#fff');
}).mouseleave(function() {
	jQuery(this).children('h2').css('color','#ff6c6c');
});

jQuery('.add-new-form').mouseover(function() {
	jQuery(this).children('h2').css('color','#fff');
}).mouseleave(function() {
	jQuery(this).children('h2').css('color','#ff6c6c');
});
	
	
	jQuery(".add-new-form span").mouseover(function() {
	jQuery(this).children('h2').css('color','#fff');
}).mouseleave(function() {
	jQuery(this).children('h2').css('color','#ff6c6c');
});
	
</script>
<script>
    function popup() {
        a = confirm("<?php _e('This will delete the form(s) permanently. Please confirm.', $textdomain ); ?>");
        if (a == true) {
            return true;
        } else {
            return false;
        }
    }
	
function bannertoggle()
{
	a = jQuery('#bannerclose .close a').text();
	if(a=='Close'){jQuery('#bannerclose .close a').text('Open');}else{jQuery('#bannerclose .close a').text('Close');}
	jQuery('.bottem-shap').toggle(500);
	return false;
}
</script>
<script>
var swfPath = ZeroClipboard.config("swfPath");
var client = new ZeroClipboard( jQuery(".cfp-copy") );
</script>
<script>
function cfptooltip1(a)
{
	jQuery(a).tooltip({
		content:'Copy to Clipboard'		
		});	
}

function cfptooltip2(a)
{
	jQuery(a).tooltip({
		content:'Copied!'
		});	
}
</script>
<style>
.cfp-copy {
	cursor:pointer; 
  }
  .ui-tooltip {
    padding: 5px 10px;
    color: white;
	background:#000;
	border:none;
	box-shadow:none;
  }
</style>