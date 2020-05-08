<?php
/*
UserSpice 4
An Open Source PHP User Management System
by the UserSpice Team at http://UserSpice.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
//echo "helpers included";

require_once("us_helpers.php");


require_once("../vendor/autoload.php");

// Readeable file size
function size($path) {
    $bytes = sprintf('%u', filesize($path));

    if ($bytes > 0) {
        $unit = intval(log($bytes, 1024));
        $units = array('B', 'KB', 'MB', 'GB');

        if (array_key_exists($unit, $units) === true) {
            return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
        }
    }

    return $bytes;
}

//escapes strings and sets character set
function sanitize($string) {
	return htmlentities($string, ENT_QUOTES, 'UTF-8');
}

function currentPage() {
	$uri = $_SERVER['PHP_SELF'];
	$path = explode('/', $uri);
	$currentPage = end($path);
	return $currentPage;
}

function currentFolder() {
	$uri = $_SERVER['PHP_SELF'];
	$path = explode('/', $uri);
	$currentFolder=$path[count($path)-2];
	return $currentFolder;
}

function format_date($date,$tz){
	//return date("m/d/Y ~ h:iA", strtotime($date));
	$format = 'Y-m-d H:i:s';
	$dt = DateTime::createFromFormat($format,$date);
	// $dt->setTimezone(new DateTimeZone($tz));
	return $dt->format("m/d/y ~ h:iA");
}

function abrev_date($date,$tz){
	$format = 'Y-m-d H:i:s';
	$dt = DateTime::createFromFormat($format,$date);
	// $dt->setTimezone(new DateTimeZone($tz));
	return $dt->format("M d,Y");
}

function money($ugly){
	return '$'.number_format($ugly,2,'.',',');
}

function name_from_id($id){
	$db = DB::getInstance();
	$query = $db->query("SELECT username FROM users WHERE id = ? LIMIT 1",array($id));
	$count=$query->count();
	if ($count > 0) {
		$results=$query->first();
		return ucfirst($results->username);
	} else {
		return "-";
	}
}

function display_errors($errors = array()){
	$html = '<ul class="bg-danger">';
	foreach($errors as $error){
		if(is_array($error)){
			//echo "<br>"; Patch from user SavaageStyle - leaving here in case of rollback
			$html .= '<li class="text-danger">'.$error[0].'</li>';
			$html .= '<script>jQuery("#'.$error[0].'").parent().closest("div").addClass("has-error");</script>';
		}else{
			$html .= '<li class="text-danger">'.$error.'</li>';
		}
	}
	$html .= '</ul>';
	return $html;
}

function display_successes($successes = array()){
	$html = '<ul>';
	foreach($successes as $success){
		if(is_array($success)){
			$html .= '<li>'.$success[0].'</li>';
			$html .= '<script>jQuery("#'.$success[1].'").parent().closest("div").addClass("has-error");</script>';
		}else{
			$html .= '<li>'.$success.'</li>';
		}
	}
	$html .= '</ul>';
	return $html;
}

function inputBlock($type,$label,$id,$divAttr=array(),$inputAttr=array(),$helper=''){
	$divAttrStr = '';
	foreach($divAttr as $k => $v){
		$divAttrStr .= ' '.$k.'="'.$v.'"';
	}
	$inputAttrStr = '';
	foreach($inputAttr as $k => $v){
		$inputAttrStr .= ' '.$k.'="'.$v.'"';
	}
	$html = '<div'.$divAttrStr.'>';
	$html .= '<label for="'.$id.'">'.$label.'</label>';
	if($helper != ''){
		$html .= '<button class="help-trigger"><span class="glyphicon glyphicon-question-sign"></span></button>';
	}
	$html .= '<input type="'.$type.'" id="'.$id.'" name="'.$id.'"'.$inputAttrStr.'>';
  if($helper != ''){
		$html .= '<div class="helper-text">'.$helper.'</div>';
	}
	$html .= '</div>';
    return $html;
}

//preformatted var_dump function
function dump($var,$adminOnly=false,$localhostOnly=false){
    if($adminOnly && isAdmin() && !$localhostOnly){
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
    }
    if($localhostOnly && isLocalhost() && !$adminOnly){
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
    }
    if($localhostOnly && isLocalhost() && $adminOnly && isAdmin()){
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
    }
    if(!$localhostOnly && !$adminOnly){
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
    }
}

//preformatted dump and die function
function dnd($var,$adminOnly=false,$localhostOnly=false){
    if($adminOnly && isAdmin() && !$localhostOnly){
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
        die();
    }
    if($localhostOnly && isLocalhost() && !$adminOnly){
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
        die();
    }
    if($localhostOnly && isLocalhost() && $adminOnly && isAdmin()){
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
        die();
    }
    if(!$localhostOnly && !$adminOnly){
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
        die();
    }
}

function bold($text){
	echo "<text padding='1em' align='center'><h4><span style='background:white'>";
	echo $text;
	echo "</h4></span></text>";
}

function err($text){
	echo "<span><text padding='1em' align='center'><font color='red'><h4></span>";
	echo $text;
	echo "</h4></span></font></text>";
}

function redirect($location){
	header("Location: {$location}");
}

function output_message($message) {
return $message;
}
