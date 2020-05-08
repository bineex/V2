<?php

 /**
 * Check if a given ip is in a network
 * @param  string $ip    IP to check in IPV4 format eg. 127.0.0.1
 * @param  string $range IP/CIDR netmask eg. 127.0.0.0/24, also 127.0.0.1 is accepted and /32 assumed
 * @return boolean true if the ip is in this range / false if not.
 */
function ip_blocked(){
    $ip_restriction = $GLOBALS['company']['ip_restriction'];
    $realIP = get_client_ip_server();

    if (!$ip_restriction){
        return FALSE;
    }
    else {
        $blocked= TRUE;
        foreach ($ip_restriction as $ip_range) {
            if (ip_in_range($realIP, $ip_range)){
                $blocked = FALSE;
            }
        }
        if (!$blocked){
            return FALSE;
        } else {
            return $realIP;
        }
    }
}

function ip_in_range( $ip, $range ) {
	if ( strpos( $range, '/' ) === false ) {
		$range .= '/32';
	}
	// $range is in IP/CIDR format eg 127.0.0.1/24
	list( $range, $netmask ) = explode( '/', $range, 2 );
	$range_decimal = ip2long( $range );
	$ip_decimal = ip2long( $ip );
	$wildcard_decimal = pow( 2, ( 32 - $netmask ) ) - 1;
	$netmask_decimal = ~ $wildcard_decimal;
	return ( ( $ip_decimal & $netmask_decimal ) == ( $range_decimal & $netmask_decimal ) );
}

function get_client_ip_server() {
    $ipaddress = '';
    if ($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else
        $ipaddress = 'UNKNOWN';
 
    return $ipaddress;
}
function debug_to_console( $data ) {
    $output = $data;
    if ( is_array( $output ) )
        $output = implode( ',', $output);

    echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
}

function get_server_name ($id_company) {
    $db = DB::getInstance();
    $sql="SELECT server_name FROM portals where id_company = $id_company ";
    $db->query($sql);
    
    $result = $db->results();        
    $server_name = $result[0]->server_name;
    
    return $server_name;
}
function insert_event_log($id_event,$id_user){
    $db = DB::getInstance();
    $portal = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    $query = $db->insert("event_view_log",
                ['id_event'=>$id_event, 'id_user'=>$id_user,'portal'=>$portal],TRUE);
    return $query;
    
}
    