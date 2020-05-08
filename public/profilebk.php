<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../../vendor/autoload.php';

//Ajax Librairy ----
// Get the core singleton object
// and the Response class

use Jaxon\Jaxon;
use Jaxon\Response\Response;

require_once '../init.php';

require_once("../includes/fct_account.php");
require_once("../includes/class_account.php");
require_once("../includes/fct_display.php");
require_once("../includes/class_freshdesk.php");

$jaxon = jaxon();
$jaxon->processRequest();// Call the Jaxon processing engine

require_once '../includes/header.php';

$idLang = $_SESSION['lang']['id'];
$libLang = $_SESSION['lang']['lib'];
$codeLang = $_SESSION['lang']['code'];

require_once("../includes/fct_security.php");
if (ip_blocked()){
    $user->logout();
    Redirect::to('blocked.php');        
}

if(isset($_POST['-fd_id']) && !empty($_POST['-fd_id'])){   

    //Log user in          
    $tab['active']=true;
    foreach ($_POST as $key => $value) {
        if(isset($value) && !empty($value)){                                
            if (substr($key,0,1) == '_'){
                $k=substr($key, 1);
                $customField[$k]=$value;

            } elseif (substr($key,0,1)=='-'){
                //nada
            } elseif (substr($key,0,3)=='tag'){
                $tags[] = $value;
            } else {
                $tab[$key]=$value;
            }
        }
    }                        
        if (isset($_POST['-emailcustom']) && !empty($_POST['-emailcustom'])){
            $emailc = $_POST['-emailcustom'];
        }
        else {
            $emailc = $_POST['-email'];
        }
        
        $tab['email'] = $emailc;
        $tab['language'] = $codeLang;
        if (isset($tags) && !empty($tags)){
            $tab['tags'] = $tags;
        }
        $tab['name'] = createName($_POST['_first_name_kanjiromaji'], $_POST['_family_name_kanjiromaji'], $codeLang);

        $birthday = isDate($_POST['-rand981955']);
        if ($birthday!=false){
            $customField['rand981955']=$birthday;
        } else {
            $customField['birthdaytxt']=$_POST['-rand981955'];
        }
        $customField['signedup'] = true;
        $tab['custom_fields']=$customField;        
        $fd_id = $_POST['-fd_id'];        
        //$content= json_encode($tab); 
        
        $fdUsers= new freshdesk();    
        $fd_values = $fdUsers->updateContact($fd_id,$tab);
        if(isset($fd_values['id'])){
            
            $email=$emailc;
            $first_name = $_POST['_first_name_kanjiromaji'];
            $last_name = $_POST['_family_name_kanjiromaji'];
            
            $account = new account();
            //$result = $account->editMail($fd_id,$_POST['email']);
            
            $result = $account->editProfile($fd_id, $first_name, $last_name, $email);
        }
        Redirect::to('index.php');

}

if(isset($user) && $user->isLoggedIn()){
    $account = $user->data();
    $fname=$account->fname;$lname=$account->lname;
    $email=$account->email;
    $uid=$account->fd_id;
    $Logged = createName($fname, $lname, $codeLang);
}
else {
    $Logged = false;
    Redirect::to('login.php');    
}

?>
<div id="wrapper">
<?php
$db = DB::getInstance();
    $sql = "SELECT id,email,username FROM users where fd_id=35014105836";

    $db->query($sql);
    $r = $db->results();
    
    foreach ($r as $value) {
        echo ($value->email).' - ';
        $email = $value->email;
        $id=$value->id;
        
        $freshUsers= new freshdesk();
        $tabUsers = $freshUsers->getUsers($email);
    
        if (isset($tabUsers)){
            foreach ($tabUsers as $values){               
                $fd_id=$values['id'];
                echo ($fd_id).'<br>';
                
                $sql2 = "update users set fd_id=".$fd_id." where id=".$id;
                $db->query($sql2);
            }
        }
    }
?>

<!-- Place any per-page javascript here -->
<?php
    echo $jaxon->getJs();
    echo $jaxon->getScript();
?>

<?php require_once '../includes/html_footer.php'; // currently just the closing /body and /html ?>
