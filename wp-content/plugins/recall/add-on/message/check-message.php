<?php
require_once( '../../load-rcl.php' );

if($_GET['lk']){
	$user_lk = $_GET['lk'];
	$user_ID = $_GET['user'];
	
	$mess_ID = $wpdb->get_var("SELECT ID FROM ".$wpdb->prefix."rcl_private_message WHERE author_mess='$user_lk' AND adressat_mess = '$user_ID' AND status_mess ='0' OR author_mess = '$user_lk' AND adressat_mess = '$user_ID' AND status_mess = '4'");
	
	$no_read_mess = $wpdb->get_var("SELECT COUNT(ID) FROM ".$wpdb->prefix."rcl_private_message 
	WHERE author_mess = '$user_ID' AND adressat_mess = '$user_lk' AND status_mess = '0' 
	OR author_mess = '$user_ID' AND adressat_mess = '$user_lk' AND status_mess = '4'");
	
	if(!$mess_ID) $mess_ID = 0;

	echo $mess_ID.'|'.$no_read_mess; exit;
			
	echo json_encode($log);	
	
}else{
	$mess_ID = $wpdb->get_var("SELECT ID FROM ".RCL_PREF."private_message WHERE adressat_mess = '$user_ID' AND status_mess ='0'");
	if(!$mess_ID) exit;
	echo $mess_ID;
}


exit;							
	
?>