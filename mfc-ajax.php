<?php
function mfc_AjaxData(){
global $wpdb, $wp_version;

$size=$_REQUEST['size'];
$perpage=$_REQUEST['perpage'];
$options=$_REQUEST['options'];
$carmusing_userid= $_REQUEST['carmusing_userid'];
$carmusing_activation= $_REQUEST['carmusing_activation'];

   // create curl resource
       


echo '<div style="display:none">$options</div>';
	try {
    if($options=='1' || $options==''){
	 $ch = curl_init('http://www.carmusing.com/features/xml/favorites.php?wpuserid='.$carmusing_userid.'&activation='.$carmusing_activation);

	}
	if($options=='2'){
	$ch = curl_init('http://www.carmusing.com/features/xml/latest.php?wpuserid='.$carmusing_userid.'&activation='.$carmusing_activation.'&perpage='.$perpage);
	}
	if($options=='3'){
	$ch = curl_init('http://www.carmusing.com/features/xml/popular.php?wpuserid='.$carmusing_userid.'&activation='.$carmusing_activation.'&perpage='.$perpage);
	}

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, 0);
    $queryResults = curl_exec($ch);
        // close curl resource to free up system resources
        curl_close($ch);   
	 
		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser, $queryResults, $vals);
		xml_parser_free($xml_parser);
		$mfc_i=0; $c_links='';
		$sizeclass="mfc_img_wrap_".$size;
	
		$xml_i=0;
		foreach ($vals as $xml_elem) {
		
		 $x_tag=$xml_elem['tag']; 
		$x_level=$xml_elem['level'];
        $x_type=$xml_elem['type'];  $x_value=$xml_elem['value'];
		//echo $x_tag.' '.$x_level.' '.$x_type.' '.$x_value.'<br>';
			if($x_tag=='TYPE') $x_type_ad=$x_value;
		if($x_tag=='HREF') $x_href=$x_value;
		if($x_tag=='DEALERADS_DESCRIPTION') $x_d_description = $x_value;
		if($x_tag=='DEALERADS_TITLE') $x_d_title = $x_value;
		if($x_tag=='MAKE') $x_make = $x_value; if($x_tag=='MODEL') $x_model = $x_value; if($x_tag=='PRICE') $x_price = $x_value;
		
		if($x_type_ad=='f' || $x_type_ad=='new'){
		if($x_tag=='THUMB' && $x_value!='' && $size=='small')
		$c_links[] = '<div><a class="mfc_img_wrap_small" href="'.$x_href.'"  target="_blank" onmouseover="mfc_comment_show(\'mfc_comment_'.$xml_i.'\')" onmouseout="mfc_comment_hide(\'mfc_comment_'.$xml_i.'\')"><img src="http://carmusing.com/'.$x_value.'" border="0"/></a><div class="mfc_comment" id="mfc_comment_'.$xml_i.'"  onmouseover="mfc_comment_show(\'mfc_comment_'.$xml_i.'\')" onmouseout="mfc_comment_hide(\'mfc_comment_'.$xml_i.'\')"><div>'. substr($x_make.' '.$x_model.' $'.$x_price.'', 0, 35).'.. <a  href="'.$x_href.'"  target="_blank" >View</a></div></div></div>';
		if($x_tag=='THUMBLARGE' && $x_value!='' && $size=='medium')
		$c_links[] = '<div><a class="mfc_img_wrap_medium" href="'.$x_href.'" target="_blank"  onmouseover="mfc_comment_show(\'mfc_comment_'.$xml_i.'\')" onmouseout="mfc_comment_hide(\'mfc_comment_'.$xml_i.'\')"><img src="http://carmusing.com/'.$x_value.'" border="0"/></a><div class="mfc_comment" id="mfc_comment_'.$xml_i.'"  onmouseover="mfc_comment_show(\'mfc_comment_'.$xml_i.'\')" onmouseout="mfc_comment_hide(\'mfc_comment_'.$xml_i.'\')"><div>'. substr($x_make.' '.$x_model.' $'.$x_price.'', 0, 35).'.. <a  href="'.$x_href.'"  target="_blank" >View</a></div></div></div>';
		if($x_tag=='IMAGE' && $x_value!='' && $size=='large')
		$c_links[] = '<div><a class="mfc_img_wrap_large" href="'.$x_href.'" target="_blank"  onmouseover="mfc_comment_show(\'mfc_comment_'.$xml_i.'\')" onmouseout="mfc_comment_hide(\'mfc_comment_'.$xml_i.'\')"><img src="http://carmusing.com/'.$x_value.'" border="0"/></a><div class="mfc_comment" id="mfc_comment_'.$xml_i.'"  onmouseover="mfc_comment_show(\'mfc_comment_'.$xml_i.'\')" onmouseout="mfc_comment_hide(\'mfc_comment_'.$xml_i.'\')"><div>'. substr($x_make.' '.$x_model.' $'.$x_price.'', 0, 35).'.. <a  href="'.$x_href.'"  target="_blank" >View</a></div></div></div>';
		
		}
		
		if($x_type_ad=='fd' || $x_type_ad=='new-d'){
		 $dealerads_description = $x_d_title.' '.$x_d_description;
		if($x_tag=='THUMB2' && $x_value!='') $c_links[] =  '<div><a class="'.$sizeclass.'" href="'.$x_href.'" target="_blank"  onmouseover="mfc_comment_show(\'mfc_comment_'.$xml_i.'\')" onmouseout="mfc_comment_hide(\'mfc_comment_'.$xml_i.'\')"><img src="'.$x_value.'" border="0"/></a><div class="mfc_comment" id="mfc_comment_'.$xml_i.'"  onmouseover="mfc_comment_show(\'mfc_comment_'.$xml_i.'\')" onmouseout="mfc_comment_hide(\'mfc_comment_'.$xml_i.'\')"><div>'. substr($dealerads_description, 0, 35).'.. <a  href="'.$x_href.'"  target="_blank" >View</a></div></div></div>';
		
		}
		
		$xml_i++;
		}
		
		if($c_links!=NULL){
		foreach ($c_links as $c_link) {
		echo $c_link;
		$mfc_i++; //echo $mfc_i;
		}
		
		}
		else{
		//echo '<iframe frameborder="0" scrolling="no" style="min-height:250px; width:100%" src="http://www.carmusing.com/features/xml/favorites.php?wpuserid='.$carmusing_userid.'&activation='.$carmusing_activation.'"></iframe>';
		echo '<p style="color:#999; font-size:11px;">Nothing found..</p>';
		}
	}
	catch (Exception $e)  
	{  
	// throw new Exception( 'Something really gone wrong', 0, $e);  
	}  
 
}
?>