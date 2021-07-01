<?php

class account {
    
    function getLangValue($key){
        Global $lang;
        return $lang['SECTION_TEAM_TITLE'];
    }
    
    function getAccount_byEmail($email){
        $db = DB::getInstance();
        $sql="SELECT id, email, account_id, password FROM users where (email='".$email."' or username='".$email."')";
        
        $db->query($sql);
        return $db->results();
    }
    
    function addChallengeValue($user_id,$val){
        $db = DB::getInstance();
        $sql="INSERT INTO user_challenge (fd_id, value) VALUES ('$user_id', '$val')";

        $result = $db->query($sql);
        return $result;
    
    }
    
    function getAccount_byFDId($id){
        $db = DB::getInstance();        
        $sql="SELECT id, email, account_id FROM users where fd_id='$id'";

        $db->query($sql);
        return $db->results();
    }
    
    function getTenant_walkin($company_id,$domain){
        $db = DB::getInstance();
        $search = ";".$domain.";";
        $sql="SELECT company_tenant_id FROM company_tenant where company_primary_id='$company_id' and domain like'%$search%' and walk_in = 1";
        $db->query($sql);        
        return $db->results();
    }
    
    function createKey($length = 32){       
        $length= MIN($length,32);
        $pchars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";		
        $key = '';

        for($i = 0; $i < $length; $i++) {
            $rand = rand(0, strlen($pchars) - 1);
            $key .= substr($pchars, $rand, 1);
        }
        return $key;
    }
    
    function updateVericode($idUser){
        $db = DB::getInstance();
        $key = $this->createKey();
        
        $sql = "update users set vericode = '$key', vericode_expiry = DATE_ADD(UTC_TIMESTAMP(), interval 1 HOUR) where id=".$idUser;
        $db->query($sql);
        return $key;
    }
    
    function editMail($fd_id,$newEmail){
        $db = DB::getInstance();
        $sql = "update users set email = '$newEmail' where fd_id=".$fd_id;
        $db->query($sql);
        return true;
    }
    
    function editProfile($fd_id,$first_name,$last_name,$newEmail){
        $db = DB::getInstance();
        $sql = "update users set fname = '$first_name',lname = '$last_name',email='$newEmail' where fd_id=".$fd_id;        
        return $db->query($sql);        
    }
    
    function isValidKey($email,$key){
        $db = DB::getInstance();
        $sql="SELECT id, email, account_id, password FROM users 
              where vericode_expiry > UTC_TIMESTAMP() and email='$email' and vericode = '$key'";
        
        $db->query($sql);
        return $db->count();
    }
    
    function updatePassword($email, $password){
        $db = DB::getInstance();
        $secure_pass = password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));
        $sql = "update users set password = '$secure_pass' where email='".$email."'";
        $query = $db->query($sql);
        file_put_contents('log/log_resetpass_'.date("y-n-j").'.log', "\xEF\xBB\xBF".date('y-m-d H:i:s').' - [SQL] '. $sql.PHP_EOL, FILE_APPEND);
        //file_put_contents('log/log_resetpass_'.date("y-n-j").'.log', "\xEF\xBB\xBF".date('y-m-d H:i:s').' - [SQL] '. $query.PHP_EOL, FILE_APPEND);
        return true;
    }
    
    function createCustomer($customer_company_name, $customer_department,$customer_name, $customer_title){
        $db = DB::getInstance();
        $customer_company_name = Input::sanitize($customer_company_name);
        
        $db->insert("company_customers", ["customer_company_name"=>$customer_company_name,
                                        "customer_department"=>$customer_department,
                                        "customer_name"=>$customer_name,
                                        "customer_title"=>$customer_title]);
        return $db->lastId;
    }
    
    function createCustomerCompany($customer_company_name){
        Global $user;
        $db = DB::getInstance();
        $customer_company_name = Input::sanitize($customer_company_name);
        
        $db->insert("customer_companies", ["company_name"=>$customer_company_name,
                                        "fd_company_id"=>$GLOBALS['company']['id'],
                                        "created_by"=>$user->data()->fd_id]);
        return $db->lastId;
    }
    
    function dologin_tobedeleted($mail, $password, $remember = false){

        $user = $this->getAccount_byEmail($mail);
        if(count($user) > 0){
            if (password_verify($password,$user['password'])) {
                $_SESSION['last_confirm']=date("Y-m-d H:i:s");
                $_SESSION['user']=$user['id'];
                return true;
            }            
        }
        return false;
    }
    function getAccount_byId_tobedeleted($id){
        Global $db2;
        
        $sql="SELECT id, email, account_id FROM users where account_id='$id'";
        $result = $db2->query($sql);
        $row = $db2->get_row($result);
        return $row;    
    }
    function isNewUser_tobedeleted($email){
        
        $existUser = $account->getAccount_byEmail($email);
   
    

           if( !empty($us['email']) && $us['email'] == $email){ 
               return true;
           }
           else{
           return false;
           } 
    }
}
