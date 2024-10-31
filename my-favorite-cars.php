<?php

/*
Plugin Name: My Favorite Cars
Plugin URI: http://carmusing.com/developers/wordpress-plugins/my-favorite-car
Description: Select and display a list of your favorite cars.
Author: Dimitar Atanasov
Version: 1.0
Author URI: http://carmusing.com/meet-the-team
Tags: cars, favorite cars, list, cool cars, used cars 

*/


global $wpdb, $wp_version;

require('recaptchalib.php');
require('mfc-ajax.php');

if($_REQUEST['size']!='' && $_REQUEST['perpage']!='' && $_REQUEST['carmusing_userid']!='') {
mfc_AjaxData();
}
else {
$publickey = "6LeLcdkSAAAAAFDwq3zxEG80yJBNq2qnJvgfUFir";
function mfc_Show($size,$perpage,$options)
{
	global $wpdb;
	$userid = get_option('carmusing_userid'); 
	$activation = get_option('carmusing_activation'); 
	if($size=='') $size='small';
	?>
	<style>
	.mfc_img_wrap_small, .mfc_img_wrap_medium, .mfc_img_wrap_large {overflow:hidden; opacity:0.9; display:block; background: url('<?php echo  plugins_url('my-favorite-cars/images/not-available.png'); ?>') no-repeat center center;  position:relative; font-weight:bold; text-decoration:none;  border:1px solid #eee; padding:3px; margin:0 2px 2px 0;}
	.mfc_comment {margin:0px; position:absolute !important; padding:7px;  top:-15px; right:-120px; display:none;   z-index:100;
	color:#ddd; font-size:10px; 
	 background:url('<?php echo plugins_url('my-favorite-cars/images/arrow.png');?>') no-repeat scroll 0px 25px transparent; width: 120px; max-height:45px; }
	
	</style>
	<script>
	function mfc_comment_show(mfcid){
	document.getElementById(mfcid).style.display='inherit';
	}
	function mfc_comment_hide(mfcid){
	document.getElementById(mfcid).style.display='none';
	}
	</script>
	<script>
	$(document).ready(function() {
		$.post("<?php echo plugins_url('my-favorite-cars.php?size='.$size.'&perpage='.$perpage.'&options='.$options.'&carmusing_userid='.$userid.'&carmusing_activation='.$activation, __FILE__); ?>",function(data,status){
			$('#mfc_wrap').html(data);
		});
	});
	</script>
	<div class="mfc_wrap" id="mfc_wrap">

  </div>
  
  <?php
echo '<div style="clear:both"></div>';
  }

class mfc_widgets extends WP_Widget {
  function mfc_widgets() {
		$widget_ops = array('classname' => 'mfc', 'description' => 'My Favorite Cars' );
		$this->WP_Widget('mfc', 'My Favorite Cars Widget', $widget_ops);
  }
  function widget($args, $instance) {
    extract($args, EXTR_SKIP);
    echo $before_widget;
    $mfc_title = empty($instance['mfc_title']) ? ' ' :  $instance['mfc_title'];
	$mfc_size = empty($instance['mfc_size']) ? ' ' :  $instance['mfc_size'];
	$mfc_perpage = empty($instance['mfc_perpage']) ? ' ' :  $instance['mfc_perpage'];
	$mfc_options= empty($instance['mfc_options']) ? ' ' :  $instance['mfc_options'];
	echo '<h3>'.$mfc_title.'</h3>';
	mfc_Show($mfc_size,$mfc_perpage,$mfc_options);
    echo $after_widget;
  }
	
  function update($new_instance, $old_instance) {
    $instance = array();
    $instance['mfc_title'] = strip_tags($new_instance['mfc_title']);
	$instance['mfc_size'] = strip_tags($new_instance['mfc_size']);
	$instance['mfc_perpage'] = strip_tags($new_instance['mfc_perpage']);
	$instance['mfc_options'] = strip_tags($new_instance['mfc_options']);
	return $instance;
  }
	
  function form($instance) {
    $instance = wp_parse_args( (array) $instance, array( 'mfc_title' => 'My Favorite Cars') );
    $mfc_title = strip_tags($instance['mfc_title']);
	 $mfc_size = strip_tags($instance['mfc_size']);
	  $mfc_perpage = strip_tags($instance['mfc_perpage']);
	   $mfc_options = strip_tags($instance['mfc_options']);
	?>
    <p><label for="<?php echo $this->get_field_id('mfc_title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('mfc_title'); ?>" name="<?php echo $this->get_field_name('mfc_title'); ?>" type="text" value="<?php echo attribute_escape($mfc_title); ?>" /></label></p>
    <p><label for="<?php echo $this->get_field_id('mfc_size'); ?>">Size: <select class="widefat" id="<?php echo $this->get_field_id('mfc_size'); ?>" name="<?php echo $this->get_field_name('mfc_size'); ?>" > 
																			<option value="small" <?php if( $mfc_size=='small' ||  $mfc_size==""){ ?>selected="selected" <?php } ?> >Thumbnails</option>
																			<option value="medium"  <?php if( $mfc_size=='medium'){ ?>selected="selected" <?php } ?> >Medium</option>
																			<option value="large"  <?php if( $mfc_size=='large'){ ?>selected="selected" <?php } ?> >Large</option>
																			</select></label></p>
    <p><label for="<?php echo $this->get_field_id('mfc_perpage'); ?>">Limit number of cars: <input class="widefat" id="<?php echo $this->get_field_id('mfc_perpage'); ?>" name="<?php echo $this->get_field_name('mfc_perpage'); ?>" type="text" value="<?php if(attribute_escape($mfc_perpage)!='') echo attribute_escape($mfc_perpage); else echo '15'; ?>" /></label></p>
     <p><label for="<?php echo $this->get_field_id('mfc_options'); ?>">Options: <select class="widefat" id="<?php echo $this->get_field_id('mfc_options'); ?>" name="<?php echo $this->get_field_name('mfc_options'); ?>" > 
																			<option value="1" <?php if( $mfc_options=='1' ||  $mfc_options==""){ ?>selected="selected" <?php } ?> >Load my favorite cars</option>
																			<option value="2"  <?php if( $mfc_options=='2'){ ?>selected="selected" <?php } ?> >Load latest cars</option>
																			<option value="3"  <?php if( $mfc_options=='3'){ ?>selected="selected" <?php } ?> >Load most popular for the last week</option>
																			</select></label></p>
    
	<?php
  }
}

function mfc_register_widgets() {
	register_widget( 'mfc_widgets' );
}

add_action( 'widgets_init', 'mfc_register_widgets' );



function mfc_Install() 
{
	global $wpdb;
	$datetime= date('Y-m-d H:i:s');
	add_option('carmusing_userid',''); add_option('carmusing_activation','');
}


function mfc_Admin_Options() 
{
global $wpdb;

 $mfc_userid = $_REQUEST['userid'];
$mfc_activation = $_REQUEST['activation'];
if($mfc_userid!='') {
 update_option('carmusing_userid',$mfc_userid); update_option('carmusing_activation',$mfc_activation);
}

$userid = get_option('carmusing_userid');
 $activation = get_option('carmusing_activation'); 
 ?>
<div class="wrap">
 <h2  style="color:black">My Favorite Cars</h2>
<?php  
if($userid=='' || $activation==''){ 
?>
<style>
#content { width:100%; background: url('<?php echo plugins_url('my-favorite-cars/images/adminbg.jpg') ?>') no-repeat left top; !important; position:relative;}

</style>
<div id="content">
 
    
    <div id="content_in">
    <h2 >Sign Up For FREE and Select Your Favorites</h2>
 
   
   	      <div class="signUpForm" >
        <div class="Sheader"><strong>Sign Up Form</strong></div>	
  <script type="text/javascript">
 var RecaptchaOptions = {
    theme : 'custom',
    custom_theme_widget: 'recaptcha_widget'
 };
 </script>       	
<form enctype="multipart/form-data" method="post" action="http://carmusing.com/features/favorites-plugin/s1.php">
<input type="hidden" value="2" name="newVA" /><input type="hidden" value="1" name="account" /> <input type="hidden" value="Save" name="continue" />
        <div class="fieldLikeTwitter">
          	<label class="username-label" for="username" style="<?php if($_GET['e']!=NULL)echo 'display:none'; ?>" >Email</label>
			<input type="text" value="<?php echo $_GET['e']; ?>"  placeholder="Email" class="username" name="usr_email2"/>
        </div>
         <div class="fieldLikeTwitter">
         <label class="password-label" for="password" >Password</label>
		 <input type="password"  class="password"  placeholder="Password" name="pwd2" />
         </div>
         <div class="fieldLikeTwitter" style="margin-bottom:10px;">
         	<label class="repeatpassword-label" for="repeatpassword" > Repeat Password</label>
			<input type="password"  class="repeatpassword"  name="pwd3" placeholder="Repeat Password" />
         </div>
                  
 <div id="recaptcha_widget" style="display:none">

   <div id="recaptcha_image"></div>
   <div class="recaptcha_only_if_incorrect_sol" style="color:red">Incorrect please try again</div>

   <span class="recaptcha_only_if_image">Enter the words above:</span>
   <span class="recaptcha_only_if_audio">Enter the numbers you hear:</span>

   <input type="text" id="recaptcha_response_field" name="recaptcha_response_field" />

   <div><a href="javascript:Recaptcha.reload()">Get another CAPTCHA</a></div>

 </div>

<?php $publickey = "6LeLcdkSAAAAAFDwq3zxEG80yJBNq2qnJvgfUFir";?>
<script type="text/javascript" src="http://api.recaptcha.net/challenge?k=<?php echo $publickey;?>&lang=en"></script>

<noscript>
<iframe src="http://api.recaptcha.net/noscript?k=<?php echo $publickey;?>&lang=en" height="200" width="500" frameborder="0"></iframe>
<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
<input type="'hidden'" name="'recaptcha_response_field'" value="'manual_challenge'">
</noscript>

          <input type="submit" value="Try for FREE" class=" btn signup-btn" onclick=""  >
          <div class="clear"></div>  
                    <div id="locationBar" style="display:none;"><p>
          
          
          
              <input name="date" type="hidden" id="date" value=<?php echo '"'.date("Y-m-d").'"';?> />
        <div><label for="city">City :</label><input name="city" type="text" value=<?php echo '"'.$record->city.'"';?> /></div>
         <div><label for="state">State :</label> <input name="state" type="text" value=<?php 
		    $q = $record->region;
 foreach ($state_list as $abbr => $stateName){
 $datasplitState=str_ireplace("\"","",$abbr);
 if (strcasecmp($datasplitState, $q) == 0){
 $st= str_ireplace("\"","",$stateName);
 }
 }
		  echo '"'.$st.'"';?> /></div>
              <div>  <label for="country">Country :</label> <input name="country" type="text"  value=<?php echo '"'.$record->country_name .'"';?> /></div>
      </div> 
      <input name="rd" type="hidden"  value="<?php echo $_GET['rd']; ?>" />  <br>
      <?php if($_GET['rd']!=NULL) {?>You will be redirected to the post in a moment. Please sign up to continue.<?php } ?> 
</form>
        </div>
      
      
      <div style="margin-left:300px; min-height:70px; <?php if($login==1){echo 'display:none;';} ?>" >
      <h2 style="font-size:22px !important;" >Already have an account? <a href="javascript:void(0)" onclick="jQuery('#loginMenu').show('slow');" title="Log in">Log in</a></h2>
       
  <div id="loginMenu" style="display:none; top: 190px; right: auto; left:370px; " >
    <form action="http://carmusing.com/features/favorites-plugin/login.php" method="post" name="logForm" class="lmc" id="logForm"  >
      <table >
        <tr>
          <td ><label for="remember"  >Keep me logged in
            <input name="remember" type="checkbox" id="remember" value="1" checked="checked" />
          </label></td>
          <td ><a href="javascript:void(0)" onclick="document.getElementById('forgotP').style.display='inherit'">I forgot my password</a></td>
          <td ></td>
        </tr>
        <tr>
          <td  ><input name="usr_email" type="text" placeholder="Email"  /></td>
          <td  ><input name="pwd" type="password"   placeholder="Password" /></td>
          <td ><input name="login" type="submit" value="Login" /></td>
        </tr>
      </table>
    </form>
    <div id="forgotP" style="border-top:1px solid #dddddd;display:none;">
      <form action="http://carmusing.com/features/favorites-plugin/login.php?ref=3" method="post" enctype="multipart/form-data" name="loginFormF" id="loginFormF">
        <p style="text-align:left">After you press <strong>Send</strong>, we will send you a new random<br />
          password on your email.</p>
        <table>
          <tr>
            <td><label for="emailF">Email</label></td>
            <td><input name="emailF"  id="emailF" type="text" size="30" maxlength="30"/></td>
          </tr>
          <tr>
            <td></td>
            <td><input name="submitLF"  id="submitLF" type="submit" value="Send" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
      
      
      </div>
        </div>
        <div style="clear:both"></div>
      <a href="#" title="Provided by CarMusing"  style="position:absolute;bottom:5px;right:5px;"> <img width="120" src="http://carmusing.com/assets/images/logo.png" alt="Car Musing home" border="0"  /></a>

  
  <?php 
  } else{
  echo '<iframe frameborder="0" scrolling="no" style="height:800px; width:100%" src="http://carmusing.com/features/favorites-plugin/start.php?wpuserid='.$userid.'&activation='.$activation.'"></iframe>
		<p style="color:#999; font-size:11px;">User ID: '.$userid.' | Activation number: '.$activation.'</p>';
	?>
	<h3>Insert into website</h3>
		<p>Use the Appearance >> <a href="widgets.php">Widgets</a> page to insert the my favorite cars widget.</p>
		<p>You can use the following shortcode to insert it in a page/post:</p>
	<p><input type='text' class='widefat' style='width:100%; background:#eee;' onclick='this.select()' value='[my-favorite-cars size="small" perpage="15" options="1"]' /></p>
	<p>Attributes:
	<br>
	<strong>size</strong> - select the size of the car images, small, medium and large
	<br>
	<strong>perpage</strong> - number of cars per page/widget
	<br>
	<strong>options</strong> - 1 loads your favorite cars, 2 loads the latest cars, and 3 most popular last week
	</p>
	<?php
	}
	
	
  ?>
  

</div>
<?php
}

 
function mfc_Add_To_Menu() 
{
	add_menu_page('My Favorite Cars', 'My Favorite Cars', 'manage_options', 'my-favorite-cars/my-favorite-cars.php', 'mfc_Admin_Options', plugins_url('my-favorite-cars/images/mfc-icon.png'));
}


function mfc_scripts_method() {
    wp_enqueue_script( 'jquery' );
	    wp_register_style( 'mfc-style', plugins_url('mfc-style.css', __FILE__));
        wp_enqueue_style( 'mfc-style' );
}    
function mfc_scripts_admin() {
    wp_enqueue_script( 'jquery' );
	    wp_register_style( 'mfc-admin-style', plugins_url('mfc-admin-style.css', __FILE__));
        wp_enqueue_style( 'mfc-admin-style' );
}    
add_action('admin_enqueue_scripts', 'mfc_scripts_admin');
add_action('wp_enqueue_scripts', 'mfc_scripts_method');

// options like user favorites, country, dealership, latest, price-range etc 
function mfc_short( $atts ) {
	extract( shortcode_atts( array(
		'size' => 'small',
		'perpage' => '15',
		'options' =>'1'
		
	), $atts ) );

	mfc_Show($size,$perpage,$options);
}
add_shortcode( 'my-favorite-cars', 'mfc_short' );

function my_admin_notice(){
    echo '<div class="updated">
       <p><strong>You must <a href="admin.php?page=my-favorite-cars/my-favorite-cars.php">configure this plugin</a> to enable My Favorite Cars.</strong></p>
    </div>';
}
if(get_option('carmusing_userid')==''){ 
add_action('admin_notices', 'my_admin_notice');
}
// add_action("plugins_loaded", "mfc_Init");
register_activation_hook(__FILE__, 'mfc_Install');
register_deactivation_hook(__FILE__, 'mfc_Deactivation');
add_action('admin_menu', 'mfc_Add_To_Menu');
}
?>