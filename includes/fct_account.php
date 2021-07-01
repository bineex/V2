<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once ('class_freshdesk.php');
require_once ('class_account.php');

use Jaxon\Jaxon;
use Jaxon\Response\Response;

//Ajax Librairy ----
// and the Response class
// Get the core singleton object
$jaxon = jaxon();  
$jaxon->register(Jaxon::USER_FUNCTION, 'SignUp_step2'); 
$jaxon->register(Jaxon::USER_FUNCTION, 'SignUp_step3'); 
$jaxon->register(Jaxon::USER_FUNCTION, 'SignUp');
$jaxon->register(Jaxon::USER_FUNCTION, 'displayProfile');
$jaxon->register(Jaxon::USER_FUNCTION, 'saveUserChallenge');
$jaxon->register(Jaxon::USER_FUNCTION, 'createCustomer');
$jaxon->register(Jaxon::USER_FUNCTION, 'removeCustomerFromGuests');
$jaxon->register(Jaxon::USER_FUNCTION, 'displayFormSponsorshipRequest');
$jaxon->register(Jaxon::USER_FUNCTION, 'sendSponsorshipRequest');
$jaxon->register(Jaxon::USER_FUNCTION, 'displaySubscription');
$jaxon->register(Jaxon::USER_FUNCTION, 'cancelSubscription');
$jaxon->register(Jaxon::USER_FUNCTION, 'registerSubscriptionResult');
$jaxon->register(Jaxon::USER_FUNCTION, 'SignupOnSubmit',array('mode' => "'synchronous'"));

function isDate($date, $format = 'Y-m-d'){
    $timestamp=strtotime($date);
    if ($timestamp){
        return date($format, $timestamp);
    }
    return false;    
}
function createName($first_name,$last_name,$codelang='ja-JP'){
    if ($codelang == 'ja-JP'){
        $name = $last_name." ".$first_name;
    } else {
        $name = $first_name." ".$last_name;
    }
    return $name;
}
function domain_allowed($value){
    global $company;
    $pos = (strpos($value, '@') == FALSE ? 0 : strpos($value, '@') + 1);
    $email_domain = substr($value, $pos);
    
    if ($company['domain'] == '*' || $company['domain'] == 'select'){ 
        return TRUE;        
    }
    elseif ($company['domain'] == 'tenant') {        
        $company_id = $company['id'];

        $account = new account();
        $company_tenant = $account->getTenant_walkin($company_id,$email_domain);
        
        if (count($company_tenant) > 0){
            return $company_tenant[0]->company_tenant_id;
        } else {
            return FALSE;
        }        
    }
    elseif ($company['domain'] == $email_domain) {
        return TRUE;        
    } else {
        return FALSE;
    }    
}
function SignupOnSubmit($formValues){
    global $lang;global $company;
    $response = new Response();
    $script = "";
    $pass_pattern = '/^.{8,}$/';

    if (strcmp($formValues['-cpassword'], $formValues['-password']) !== 0 ){
        $response->setReturnValue(0);
        $response->alert($lang['PASSWORDS_NOT_MATCHING']);
        return $response;
    }
    if (preg_match($pass_pattern, $formValues['-password']) != 1){
        $response->setReturnValue(0);
        $response->alert($lang['PASSWORDS_PATTERN']);
        return $response;
    }
    if (isset($formValues['-emailcustom_required']) && $formValues['-emailcustom_required'] == 'required' && !filter_var($formValues['-emailcustom'], FILTER_VALIDATE_EMAIL)){
    
        $response->setReturnValue(0);
        $response->alert($lang['FILL_EMAIL']);
        return $response;
    }
    if (isset($formValues['-emailcustom']) && !empty($formValues['-emailcustom'])){
        if (filter_var($formValues['-emailcustom'], FILTER_VALIDATE_EMAIL)){
            $email = $formValues['-emailcustom'];

            $account = new account();
            $existUser = $account->getAccount_byEmail($email);

            if (count($existUser) > 0){
                $res = $lang['ACCOUNT_EXISTS'];
                $user_sel= $existUser[0];
                $id = $user_sel->id;

                $response->alert($res);
                return $response;
            }

            $freshUsers= new freshdesk();
            $tabUsers = $freshUsers->getUsers($email);
        
            if (isset($tabUsers) && count($tabUsers)>0 && is_array($tabUsers)){
                foreach ($tabUsers as $values){
                    $script = "";
                    $fd_id=$values['id'];

                    $account = new account();
                    $user_fd = $account->getAccount_byFDId($fd_id);

                    if (count($user_fd) > 0){
                        $res = $lang['ACCOUNT_EXISTS']." - ".$fd_id;
                        $response->alert($res);
                        return $response;
                    }
                    else {
                        $script .="$('#-fd_id').val('".$fd_id."');";
                    }
                }
            }
            
            $script .= "$('#-email').val('".$email."');";
            $response->script($script);
            $response->setReturnValue(1);
            return $response;
        }
        else{
            $response->setReturnValue(0);
            $response->alert($lang['FILL_EMAIL']."..");
            return $response;
            }
    }

    $response->setReturnValue(1);
    return $response;
}
function SignUp($formValues){
    global $lang; 
    global $company;
    Global $scriptJQueryLoad;
    $response = new Response();
   
   
   if (isset($GLOBALS['company']['SignUp_lite']) && $GLOBALS['company']['SignUp_lite']){
       $form = SignUp_lite($formValues);
   }
   else {    
    if ($lang['FRM_EMPLOYEE_ID']){
        $id = $formValues['employee_id'];
        if (empty($formValues['email']) || empty($formValues['employee_id'])){
            $response->alert($lang['FRM_EMPLOYEE_ID']);
            return $response;
        }
    } else{
        if (empty($formValues['email'])){
            $response->alert($lang['FILL_EMAIL']);
            return $response;
        }
    }
    if(filter_var($formValues['email'], FILTER_VALIDATE_EMAIL)){
    
        $email = $formValues['email'];
        $emailcustom_required = "";
        
        $account = new account();
        $existUser = $account->getAccount_byEmail($email);

        if (count($existUser) > 0){

            $res = $lang['ACCOUNT_EXISTS'];
            $user_sel= $existUser[0];
            $id = $user_sel->id;

            $response->alert($res);
            return $response;
        }

        $freshUsers= new freshdesk();
        $tabresult = $freshUsers->getUsers($email);
        $tabUsers = ((is_array($tabresult)) && (count($tabresult)>0) ? $tabresult : FALSE);

        $emailcustom_required = '';
        $labelCustom = $lang['FRM_EMAIL'];
        $emailcustom=$email;
    }
    else {

        $email = FALSE;
        $tabUsers = FALSE;
        $emailcustom_required = 'required';
        $labelCustom = $lang['FRM_EMAIL']."*";
        $emailcustom="";
    }
    
    $domain = $formValues['email'];
    //$tabUsers == FALSE || count($tabUsers)<1 || !is_array($tabUsers)
    if ($tabUsers == FALSE ){
        $domain_ok = domain_allowed($domain);
                
        if ($company['walk_in'] and $domain_ok){
            $fd_id = "new";            
        }
        else {
            $res = $lang['UNKNOWN_EMAIL'];
            $response->alert($res);
            return $response;
        }        
    }
    else {
        foreach ($tabUsers as $values){
            $fd_id=$values['id'];
            $domain_ok = "";
        break;
        }
    }
        
    $step3_content = SignUp_step3_content();
    $step2_content = SignUp_step2_content();

    $form = "<form class='m-0' id='form_account' name='form_account' action='signup.php' method='post' onsubmit='return jaxon_SignupOnSubmit(jaxon.getFormValues(\"form_account\"))'>
            <div class='clearfix'>
        <div class='row'>
            <p><span style='opacity: .5;'>".$lang['MANDATORY_FIELD']."</span></p>
                <div class='col-md-12 box-light'>
                    <div class='form-row'>";
                        if ($email){
                        $form.= "<div class='col'>                
                                <label for='-emailcompagny'>".$lang['FRM_EMAIL']." </label>
                                <input type='email' class='form-control' id='-emailcompagny' name='-emailcompagny' placeholder='".$lang['FRM_EMAIL']."' value='".$email."' disabled >
                                </div>";
                        }
                        if ($lang['FRM_EMPLOYEE_ID']){
                        $form.= "<div class='col'>
                                       <label for='_employee_id'>".$lang['FRM_EMPLOYEE_ID']."</label>
                                       <input type='text' class='form-control' id='_employee_id' name='_employee_id' value='".$id."' required >
                                   </div>";
                        }
                        if ($company['email_customize']){
                              $form.= "<div class='col'>
                                  <label for='-emailcustom'>".$labelCustom."</label>
                                  <input type='email' class='form-control' id='-emailcustom' value='".$emailcustom."' name='-emailcustom' ".$emailcustom_required.">
                              </div>";
                          }
            $form.= "</div>
                    <br>
                    ".$step2_content."
                    <div class='form-row'>
                    <div class='col'>
                          <label for='-password'>".$lang['FRM_PASSWORD']."</label>
                          <input type='password' class='form-control' id='-password' name='-password' aria-describedby='passwordHelpBlock' required>
                          <small id='passwordHelpBlock' class='form-text text-muted'>".$lang['FRM_PASSWORD_HELP']."</small>
                        </div>
                        <div class='col'>
                          <label for='-cpassword'>".$lang['FRM_C_PASSWORD']."</label>
                          <input type='password' class='form-control' id='-cpassword' name='-cpassword' required>
                        </div>
                    </div>
                </div>

        </div>
        <div class='row'>            
                <div class='col-md-12'>".$step3_content."</div>
        </div>
        <hr>
        <div class='row'>      
            <div class='col-md-12 text-center'>";
            if (isset($domain_ok) && $domain_ok === '35001261628'){
                //Salesforce
                $GLOBALS['company']['t&c_doc'] = "(OWP)Teams of Service 2020.11_.pdf";
                $form.= "<div class='row text-left'><h4>".$lang['FRM_AGREE_SALESFORCE']."</h4></div>
                <div class='row'>";
                    if ($GLOBALS['company']['privacy_doc']){
                        $privacy_doc = $GLOBALS['company']['privacy_doc'];                      
                        $form.= "<label class='checkbox m-0'><a href='documents/".$GLOBALS['company']['id']."/".$privacy_doc."' target='_blank' rel='nofollow'>".$lang['FRM_AGREE_LINK']."</a></label>";
                    }
                    if ($GLOBALS['company']['t&c_doc']){
                        $form.= "<label class='checkbox m-0'><a href='documents/".$GLOBALS['company']['id']."/".$GLOBALS['company']['t&c_doc']."' target='_blank' rel='nofollow'>".$lang['FRM_AGREE_LINK2']."</a></label>";
                    }
               $form.= " </div>
                <div class='row text-center'>
                    <label class='checkbox m-0'><input class='form-control checked-agree' type='checkbox' id='-agree' name='-agree' required><i></i>".$lang['FRM_AGREE']."</label>
                </div>
                <div class='row text-left'>
                    <label class='checkbox m-0'><input class='form-control checked-agree' type='checkbox' id='-agreesalesforce' name='-agreesalesforce' required><i></i>".$lang['FRM_AGREE_SALESFORCE_2']."</label>
                </div>";
            }
            elseif (isset($domain_ok) && $domain_ok === '35001057364'){
                //CITI                
                $GLOBALS['company']['privacy_citi'] = "CIiti_Service_Policy_YourConcierge.pdf";
                $form.= "<div class='row text-left'><h4>".$lang['FRM_AGREE_TITLE']."</h4></div>
                <div class='row'>";
                    if ($GLOBALS['company']['privacy_doc']){
                        $privacy_doc ='Citigroup_Privacy policy_200430.pdf';                      
                        $form.= "<label class='checkbox m-0'><a href='documents/".$GLOBALS['company']['id']."/".$privacy_doc."' target='_blank' rel='nofollow'>".$lang['FRM_AGREE_LINK']."</a></label>";
                    }
                    if ($GLOBALS['company']['privacy_citi']){
                        $form.= "<label class='checkbox m-0'><a href='documents/".$GLOBALS['company']['id']."/".$GLOBALS['company']['privacy_citi']."' target='_blank' rel='nofollow'>".$lang['FRM_AGREE_CITI']."</a></label>";
                    }
                $form.= " </div>
                <div class='row text-center'>
                    <label class='checkbox m-0'><input class='form-control checked-agree' type='checkbox' id='-agree' name='-agree' required><i></i>".$lang['FRM_AGREE']."</label>
                </div>";                
            
            }else{
                $form.= "<div class='row'><h4>".$lang['FRM_AGREE_TITLE']."</h4></div>
                <div class='row'>";
                    if ($GLOBALS['company']['privacy_doc']){
                        $privacy_doc = $GLOBALS['company']['privacy_doc'];                      
                        $form.= "<label class='checkbox m-0'><a href='documents/".$GLOBALS['company']['id']."/".$privacy_doc."' target='_blank' rel='nofollow'>".$lang['FRM_AGREE_LINK']."</a></label>";
                    }
                    if ($GLOBALS['company']['t&c_doc']){
                        $form.= "<label class='checkbox m-0'><a href='documents/".$GLOBALS['company']['id']."/".$GLOBALS['company']['t&c_doc']."' target='_blank' rel='nofollow'>".$lang['FRM_AGREE_LINK2']."</a></label>";
                    }
               $form.= " </div>
                <div class='row text-center'>
                    <label class='checkbox m-0'><input class='form-control checked-agree' type='checkbox' id='-agree' name='-agree' required><i></i>".$lang['FRM_AGREE']."</label>
                </div>";
            }
            $form.= "<div class='row'>
                    <button type='submit' class='btn btn-primary'><i class='fa fa-check'></i>".$lang['SIGNUP_TEXT']."</button>
                </div>
                <div class='invisible' >
                    <input type='text' name='-emailcustom_required' id='-emailcustom_required' class='form-control' value='".$emailcustom_required."'>
                </div>
                <div class='invisible' >
                    <input type='text' name='-fd_id' id='-fd_id' class='form-control' value='".$fd_id."'>
                </div>
                <div class='invisible' >
                    <input type='text' name='-email' id='-email' class='form-control' value='".$email."'>
                </div>";
               
               if ($company['domain'] != 'select'){
                    $form.= "<div class='invisible' >
                                <input type='text' name='-company_additional' id='-company_additional' class='form-control' value='".$domain_ok."'>
                            </div>";
               }
            $form.= "</div>
      </div></div></form>";
   }

    $response->assign('contentForm', 'innerHTML', $form);
    if (!empty($formValues['building'])){
        $scriptBuilding = '$("#_building").val("'.$formValues['building'].'");';
        $response->script($scriptBuilding);
    }
    
    $response->script($scriptJQueryLoad);
    
    //$response->alert('Yeah');
    return $response;
}

function SignUp_lite($formValues){
    global $lang; 
    global $company;
    //$response = new Response();
    
    if ($lang['FRM_EMPLOYEE_ID']){
        $id = $formValues['employee_id'];
        if (empty($formValues['email']) || empty($formValues['employee_id'])){
            $response->alert($lang['FRM_EMPLOYEE_ID']);
            return $response;
        }
    } else{
        if (empty($formValues['email'])){
            $response->alert($lang['FILL_EMAIL']);
            return $response;
        }
    }
        $email = $formValues['email'];    

    $account = new account();
    $existUser = $account->getAccount_byEmail($email);
        
    if (count($existUser) > 0){
        
        $res = $lang['ACCOUNT_EXISTS'];
        $user_sel= $existUser[0];
        $id = $user_sel->id;
        
        $response->alert($res);
        return $response;
    }
    
    $freshUsers= new freshdesk();    
    $tabUsers = $freshUsers->getUsers($email);
    
    if (!isset($tabUsers) || count($tabUsers)<1 ){
        
        $domain_ok = domain_allowed($email); 
        if ($company['walk_in'] and $domain_ok){
            $fd_id = "new";            
        }
        else {
            $res = $lang['UNKNOWN_EMAIL'];
            $response->alert($res);
            return $response;
        }        
    }
    else {
        foreach ($tabUsers as $values){
            $fd_id=$values['id'];
            
            $account = new account();
            $user_fd = $account->getAccount_byFDId($fd_id);
            
            if (count($user_fd) > 0){
                $res = $lang['ACCOUNT_EXISTS']." - ".$values['email'];
                
                $response->alert($res);
                return $response;
            }
            $domain_ok = "";
        break;
        }
    }
        
    $step2_content = SignUp_step2_content_lite();
    
    $form = "<form class='m-0' id='form_account' name='form_account' action='signup.php' method='post'>
            <div class='clearfix'>
        <div class='row'>
            <p><span style='opacity: .5;'>".$lang['MANDATORY_FIELD']."</span></p>
                <div class='col-md-12 box-light'>
                    <div class='form-row'>            
                        <div class='col'>                
                                <label for='-emailcompagny'>".$lang['FRM_EMAIL']." </label>
                                <input type='email' class='form-control' id='-emailcompagny' name='-emailcompagny' placeholder='".$lang['FRM_EMAIL']."' value=".$email." disabled >
                        </div>";
                        if ($lang['FRM_EMPLOYEE_ID']){
                 $form.= "<div class='col'>
                                <label for='_employee_id'>".$lang['FRM_EMPLOYEE_ID']."</label>
                                <input type='text' class='form-control' id='_employee_id' name='_employee_id' placeholder='".$lang['FRM_EMPLOYEE_ID']."*' value='".$id."' required >
                            </div>";
                        }
            $form.= "</div>
                    <br>
                    ".$step2_content."
                    <div class='form-row'>
                    <div class='col'>
                          <label for='-password'>".$lang['FRM_PASSWORD']."</label>
                          <input type='password' class='form-control' id='-password' name='-password' aria-describedby='passwordHelpBlock' required>
                          <small id='passwordHelpBlock' class='form-text text-muted'>".$lang['FRM_PASSWORD_HELP']."</small>
                        </div>
                        <div class='col'>
                          <label for='-cpassword'>".$lang['FRM_C_PASSWORD']."</label>
                          <input type='password' class='form-control' id='-cpassword' name='-cpassword' required>
                        </div>
                    </div>
                </div>

        </div>
        
        <hr>
        <div class='row'>      
            <div class='col-md-12 text-center'>
                    <div class='row'>
                    <h4>".$lang['FRM_AGREE_TITLE']."</h4>
                </div>
                <div class='row'>";
                    if ($GLOBALS['company']['privacy_doc']){
                        $form.= "<label class='checkbox m-0'><a href='documents/".$GLOBALS['company']['id']."/".$GLOBALS['company']['privacy_doc']."' target='_blank' rel='nofollow'>".$lang['FRM_AGREE_LINK']."</a></label>";
                    }            
                    $form.= "<label class='checkbox m-0'><a href='documents/".$GLOBALS['company']['id']."/".$GLOBALS['company']['t&c_doc']."' target='_blank' rel='nofollow'>".$lang['FRM_AGREE_LINK2']."</a></label>

                </div>
                <div class='row text-center'>
                    <label class='checkbox m-0'><input class='form-control checked-agree' type='checkbox' id='-agree' name='-agree' required><i></i>".$lang['FRM_AGREE']."</label>
                </div>
                <div class='row'>           
                        <button type='submit' class='btn btn-primary'><i class='fa fa-check'></i>".$lang['SIGNUP_TEXT']."</button>

                </div>
                <div class='invisible' >
                    <input type='text' name='-fd_id' id='-fd_id' class='form-control' value='".$fd_id."'>
                </div>
                <div class='invisible' >
                    <input type='text' name='-email' id='-email' class='form-control' value='".$email."'>
                </div>
                <div class='invisible' >
                    <input type='text' name='-company_additional' id='-company_additional' class='form-control' value='".$domain_ok."'>
                </div>

            </div>
      </div></div></form>";
    
                    
    //$response->assign('contentForm', 'innerHTML', $form);    
    
    //$response->alert('Yeah');
    return $form;
}

function displayProfile($id_user){
    global $lang; global $company; Global $scriptJQueryLoad;
    $response = new Response();
    
    $freshUsers= new freshdesk();    
    $userValues = $freshUsers->getUser($id_user);
    
   
    $id = $userValues['custom_fields']['employee_id'];
    $email = $userValues['email'];
    
    $tags = $userValues['tags'];
    $ProfileTags = listProfileTags();
    $FamilyTags = listFamilyTags();
    
    $step3_content = SignUp_step3_content();
    $step2_content = SignUp_step2_content();
    
    
    $form = "<form class='m-0' id='form_account' name='form_account' action='profile.php' method='post'>
        <div class='clearfix'>
        <div class='row'>
            <p><span style='opacity: .5;'>".$lang['MANDATORY_FIELD']."</span></p>
                <div class='col-md-12 box-light'>
                    <div class='form-row'>            
                        <div class='col'>                
                            <label for='-emailcompagny'>".$lang['FRM_EMAIL']." </label>
                            <input type='email' class='form-control' id='-emailcompagny' name='-emailcompagny' placeholder='".$lang['FRM_EMAIL']."' value=".$email." disabled >
                        </div>";
                        if ($lang['FRM_EMPLOYEE_ID']){
                $form.= "<div class='col'>
                                <label for='_employee_id'>".$lang['FRM_EMPLOYEE_ID']."</label>
                                <input type='text' class='form-control' id='_employee_id' name='_employee_id' placeholder='".$lang['FRM_EMPLOYEE_ID']."*' value='".$id."' required >
                        </div>";
                        }
                /*
                if ($company['email_customize']){
                            $form.= "<div class='col'>
                                <label for='-emailcustom'>".$lang['FRM_EMAIL']."</label>
                                <input type='email' class='form-control' id='-emailcustom' name='-emailcustom' value='".$email."' >
                            </div>";
                        }
                 * 
                 */
                        if ($lang['FRM_EMPLOYEE_ID']){
                            $form.= "<div class='col'>
                                <label for='-emailcustom'>".$lang['FRM_EMAIL']."</label>
                                <input type='email' class='form-control' id='-emailcustom' name='-emailcustom' value='".$email."' >
                            </div>";
                        }
            $form.= "</div>
                    <br>
                    ".$step2_content."

            </div>
        </div>
        <div class='row'>
            <div class='col-md-12'>".$step3_content."</div>
        </div>
        
        <div class='row'>
            <div class='col-md-12 text-center'>
                <div class='row'>           
                        <button type='submit' class='btn btn-primary'><i class='fa fa-check'></i>".$lang['FRM_UPDATE']."</button>
                </div>
                <div class='invisible' >
                    <input type='text' name='-fd_id' id='-fd_id' class='form-control' value='".$id_user."'>
                </div>
                <div class='invisible' >
                    <input type='text' name='-email' id='-email' class='form-control' value='".$email."'>
                </div>
            </div>
      </div>
      </div></form>";
            
    if($userValues['custom_fields']['rand981955'] == null){
        $birthday = $userValues['custom_fields']['birthdaytxt'];
    } else {
        $birthday = $userValues['custom_fields']['rand981955'];
    }
    
    $gender = $userValues['custom_fields']['rand349877'];
    $kids = $userValues['custom_fields']['kids'];
    
    $script = '$("#_rand349877").val("'.$gender.'");$("#_kids").val("'.$kids.'");';
    $script .= '$("#_family_name_kanjiromaji").val("'.$userValues['custom_fields']['family_name_kanjiromaji'].'");';
    $script .= '$("#_first_name_kanjiromaji").val("'.$userValues['custom_fields']['first_name_kanjiromaji'].'");';
    $script .= '$("#_family_name_kana").val("'.$userValues['custom_fields']['family_name_kana'].'");';
    $script .= '$("#_first_name_kana").val("'.$userValues['custom_fields']['first_name_kana'].'");';
    $script .= '$("#_office_location").val("'.$userValues['custom_fields']['office_location'].'");';
    $script .= '$("#_rand333246").val("'.$userValues['custom_fields']['rand333246'].'");';
    $script .= '$("#_title").val("'.$userValues['custom_fields']['title'].'");';
    $script .= '$("#_family_details").val("'.$userValues['custom_fields']['family_details'].'");';
    $script .= '$("#-rand981955").val("'.$birthday.'");';
    $script .= '$("#phone").val("'.$userValues['phone'].'");';
    
    $last_names = array_column($ProfileTags, 'en', 'id');
    $idF = array_column($FamilyTags, 'en', 'id');
    
    foreach ($tags as $tag){
        $idTag = array_search($tag, $last_names);        
        if ($idTag){
            $script .= '$("#tag_'.$idTag.'").prop("checked", "true");';
        }
        $idTagF = array_search($tag, $idF);        
        if ($idTagF){
            $script .= '$("#tag_f_'.$idTagF.'").prop("checked", "true");';
        }
    }

    $response->assign('contentForm', 'innerHTML', $form);
    $response->script($script);
    $response->script($scriptJQueryLoad);
    return $response;
}

function SignUp_step2_content(){
    global $lang; global $company;
    //
    $form = "<div class='form-row'>
                <div class='col'>
                  <label for='_family_name_kanjiromaji'>".$lang['FRM_FAMILY_NAME']."</label>
                  <input type='text' class='form-control' id='_family_name_kanjiromaji' name='_family_name_kanjiromaji' required >
                </div>
                <div class='col'>
                  <label for='_first_name_kanjiromaji'>".$lang['FRM_FIRST_NAME']."</label>
                  <input type='text' class='form-control' id='_first_name_kanjiromaji' name='_first_name_kanjiromaji' required >
                </div>
            </div>
            <br>
            <div class='form-row'>
                <div class='col'>
                  <label for='_family_name_kana'>".$lang['FRM_FAMILY_NAME_K']."</label>
                  <input type='text' class='form-control' id='_family_name_kana' name='_family_name_kana' >
                </div>
                <div class='col'>
                  <label for='_first_name_kana'>".$lang['FRM_FIRST_NAME_K']."</label>
                  <input type='text' class='form-control' name='_first_name_kana' id='_first_name_kana' >
                </div>
            </div>
            <br>";
            if ($company['domain'] == 'select'){
                $listTenants = getCompanyTenantList($company['id']);
                $form .= "<div class='form-row'>
                    <div class='col'>
                      <label for='-company_additional'>".$lang['FRM_COMPANY']."</label>
                      <select class='form-control' id='-company_additional' name='-company_additional' required >";
                        foreach ($listTenants as $tenant){
                            $form .= "<option value='".$tenant->company_tenant_id."'>".$tenant->label."</option>";
                        }
            $form .= "</select>
                    </div>
                </div>
                <br>";
            }
            if ($lang['FRM_GENDER'] || $lang['FRM_BIRTHDAY']){
                $form .= "<div class='form-row'>
                    <div class='col'>
                    <label for='_rand349877'>".$lang['FRM_GENDER']."</label>
                    <select class='form-control' id='_rand349877' name='_rand349877'>
                        <option value='女性 (Female)'>女性(Female)</option>
                        <option value='男性 (Male)'>男性(Male)</option>
                        <option value='その他 (Other)'>その他(Other)</option>
                        <option value='回答したくない (Prefer not to say)'>回答したくない (Prefer not to say)</option>
                        </select>
                    </div>        
                    <div class='col'>
                    <label for='-rand981955'>".$lang['FRM_BIRTHDAY']."</label>
                    <input type='text' class='form-control datepicker' data-format='yyyy-mm-dd' data-lang='en' data-RTL='false' id='-rand981955' name='-rand981955' aria-describedby='-rand981955Help' ".$lang['FRM_BIRTHDAY_RQD'].">
                    <small id='-rand981955Help' class='form-text text-muted'>".$lang['FRM_BIRTHDAY_HELP']."</small>
                    </div>
                </div><br>";
            }
            $form .= "<div class='form-row'>";
                
                if ($lang['FRM_OFFICE_LOCATION']){
                  $form .= "<div class='col'>
                  <label for='_office_location'>".$lang['FRM_OFFICE_LOCATION']."</label>
                  <input type='text' class='form-control' id='_office_location' name='_office_location'>
                  </div>";
                }
                
                if ($lang['FRM_BUILDING_LOCATION']){
                    $listBuilding = getCompanyBuildingList($company['id']);                    
                    $form .= "<div class='col'>
                        <label for='_building'>".$lang['FRM_BUILDING_LOCATION']."</label>
                        <select class='form-control' id='_building' name='_building' required >";
                            foreach ($listBuilding as $building){
                                $form .= "<option value='".$building->label."'>".$building->label."</option>";
                            }
                    $form .= "</select>
                            </div>";
                }
                $form .= "<div class='col'>
                  <label for='phone'>".$lang['FRM_MOBILE']."</label>
                  <input type='text' class='form-control' id='phone' name='phone' ".$lang['FRM_MOBILE_RQD'].">
                </div>
            </div>
            <br><br>";
    return $form;

}
function SignUp_step2_content_lite(){
    global $lang; global $company;
    
    $form = "<div class='form-row'>
                <div class='col'>
                  <label for='_family_name_kanjiromaji'>".$lang['FRM_FAMILY_NAME']."</label>
                  <input type='text' class='form-control' id='_family_name_kanjiromaji' name='_family_name_kanjiromaji' required >
                </div>
                <div class='col'>
                  <label for='_first_name_kanjiromaji'>".$lang['FRM_FIRST_NAME']."</label>
                  <input type='text' class='form-control' id='_first_name_kanjiromaji' name='_first_name_kanjiromaji' required >
                </div>
            </div>
            <br>
            <div class='form-row'>
                <div class='col'>
                  <label for='_family_name_kana'>".$lang['FRM_FAMILY_NAME_K']."</label>
                  <input type='text' class='form-control' id='_family_name_kana' name='_family_name_kana' >
                </div>
                <div class='col'>
                  <label for='_first_name_kana'>".$lang['FRM_FIRST_NAME_K']."</label>
                  <input type='text' class='form-control' name='_first_name_kana' id='_first_name_kana' >
                </div>
            </div>
            <br>";
            
            $form .= "<br>
            <div class='form-row'>
                <div class='col'>
                  <label for='phone'>".$lang['FRM_MOBILE']."</label>
                  <input type='text' class='form-control' id='phone' name='phone'>
                </div>
            </div>
            <br><br>
            ";
    return $form;

}

function SignUp_step3_content(){
    global $lang;
    $ProfileTags = listProfileTags();
    $FamilyTags = listFamilyTags();
    
    $form = "<hr><h3>".$lang['FRM_TITLE_STEP3']."</h3>";
    $form .= "<div class='form-row justify-content-center'>";
    
    foreach ($ProfileTags as $key => $value) {        
            $form .= "<div class='col-4 col-sm-2 col-lg-auto ml-10 mb-20 text-center'>
                            
                                <input type='checkbox' class='form-control css-checkbox' id='tag_".$value['id']."' name='tag_".$value['id']."' value='".$value['en']."'>
                                <label class='css-label' style='background-image: url(images/tags/tag_".$value['id'].".png);' for='tag_".$value['id']."'>
                                </label>
                                <p>".$value[$lang['LANGUAGE_ID']]."</p>
                            
                        </div>";           
    }
    $form .= "</div>";    
    $form .= "<hr>";
    
    $form .= "<h3>".$lang['FRM_TITLE_STEP3_F']."</h3>";
    $form .= "<div class='form-row justify-content-center'>";
    
    foreach ($FamilyTags as $key => $value) {
        
            $form .= "<div class='col-4 col-sm-2 col-lg-auto ml-10 mb-20 text-center'>
                            <div class='box-icon box-icon-center box-icon-transparent box-icon-large'>
                                <input type='checkbox' class='form-control css-checkbox' id='tag_f_".$value['id']."' name='tag_f_".$value['id']."' value='".$value['en']."'>
                                <label class='css-label' style='background-image: url(images/tags/tag_f_".$value['id'].".png);' for='tag_f_".$value['id']."'>
                                </label>
                                <span>".$value[$lang['LANGUAGE_ID']]."</span>
                            </div>
                        </div>";
           
    }
    $form .= "</div>
    <br><br>
            <div class='form-row'>
                <div class='col'>
                  <label for='_family_details'>".$lang['FRM_FAMILY_DETAIL']."</label>
                  <input type='text' class='form-control' id='_family_details' name='_family_details' placeholder='' >
                </div>                
            </div>";
    return $form;
}
function listProfileTags() {
    $tags = array ((1) => array('id' => 1, 'en' => 'Sports','jp' => 'スポーツ/フィットネス',),
                    (2) => array('id' => 2, 'en' => 'Restaurants','jp' => '食べ歩き',),
                    (3) => array('id' => 3, 'en' => 'Drinks','jp' => 'お酒',),
                    (4) => array('id' => 4, 'en' => 'Travel','jp' => '旅行',),
                    (5) => array('id' => 5, 'en' => 'Art','jp' => '音楽・美術館・舞台等',),
                    (6) => array('id' => 6, 'en' => 'Studies','jp' => '資格/留学',),
                    (7) => array('id' => 7, 'en' => 'Hobbies','jp' => '習い事/趣味（ご自身）',),
                    (8) => array('id' => 8, 'en' => 'Relaxation','jp' => 'リラクゼーション',),
                    (9) => array('id' => 9, 'en' => 'Cooking','jp' => 'お料理・レシピ',),
                    (10) => array('id' => 10, 'en' => 'Outdoor','jp' => 'アウトドア',),
                    (11) => array('id' => 11, 'en' => 'Housekeeping','jp' => '家事代行/シッター',),
                    (12) => array('id' => 12, 'en' => 'Childcare','jp' => '子育て関連',),
                    (13) => array('id' => 13, 'en' => 'Pet','jp' => 'ペット',),
                    (14) => array('id' => 14, 'en' => 'Wedding','jp' => '恋愛/結婚',),
                    (15) => array('id' => 15, 'en' => 'Pregnancy','jp' => '妊娠/出産',),
                    (16) => array('id' => 16, 'en' => 'Elderly care','jp' => '介護',),
                    (17) => array('id' => 17, 'en' => 'Health care','jp' => 'ヘルスケア',),
                    (18) => array('id' => 18, 'en' => 'Asset building','jp' => '資産形成',),
                    (19) => array('id' => 19, 'en' => 'Career','jp' => 'キャリア',),
                    (20) => array('id' => 20, 'en' => 'Others','jp' => 'その他',),
   );
    return $tags;
}
function listFamilyTags() {
    $tags = array ((1) => array('id' => 1, 'en' => 'Single','jp' => '1人暮らし',),
                    (2) => array('id' => 2, 'en' => 'Parents','jp' => 'ご両親と同居',),
                    (3) => array('id' => 3, 'en' => 'Partner','jp' => 'パートナーと同居',),
                    (4) => array('id' => 4, 'en' => 'Kids','jp' => 'お子様あり',),
                    (5) => array('id' => 5, 'en' => 'Pets','jp' => 'ペットと同居',),
                    (6) => array('id' => 6, 'en' => 'Other','jp' => 'その他',),
                    (7) => array('id' => 7, 'en' => 'No answer','jp' => '回答しない',),                    
   );
    return $tags;
}

function saveUserChallenge($user_id,$form_values){
    Global $lang;
    
   $weight = $form_values['weight'];
   $val = floatval($weight);
   $user = new account();
   $result = $user->addChallengeValue($user_id, $val);
    
    $response = new Response();
    $response->alert($lang['CHALLENGE_WEIGHT_ADDED']);
    $response->script('$("#modalp").modal("hide");');
    $response->redirect('login.php');
    return $response;
}
function getGuestCompanyId_byName($company_name){
    $db = DB::getInstance();
    $sql = "SELECT customer_company_id FROM customer_companies where company_name = '".$company_name."'";
    
    $GuestCompany = $db->query($sql);
    $idCompany = $GuestCompany->first();
    
    if (isset($idCompany->company_id)){
        return $idCompany->company_id;
    }
    else {
        $user = new account();
        $newCompany = $user->createCustomerCompany($company_name);
        return $newCompany;
    }
}
function createGuest($formCustomer){
    $db = DB::getInstance();
        
        $customer_company_name = Input::sanitize($formCustomer['customer_company_name']);
        $customer_department = Input::sanitize($formCustomer['customer_department']);
        $customer_name = Input::sanitize($formCustomer['customer_name']);
        $customer_title = Input::sanitize($formCustomer['customer_title']);
        
        $customer_company_id = getGuestCompanyId_byName($customer_company_name);
        
        $db->insert("company_customers", ["company_id"=>$company_id,
                                        "customer_company_name"=>$customer_company_name,
                                        "customer_department"=>$customer_department,
                                        "customer_name"=>$customer_name,
                                        "customer_title"=>$customer_title]);
        $customer_id = $db->lastId();
        $input_guest = $customer_company_name." ".$customer_department." - ".$customer_name;
        
        $response = new Response();
        $response->assign('input_guest', 'value', $input_guest);
        $response->assign('customer_id', 'value', $customer_id);
        $response->script('$("#modalp").modal("hide");');
        return $response;
}
function createCustomer($formCustomer){
    $db = DB::getInstance();
        
        $company_id = $GLOBALS['company']['id'];
        $customer_company_name = Input::sanitize($formCustomer['customer_company_name']);
        $customer_department = Input::sanitize($formCustomer['customer_department']);
        $customer_name = Input::sanitize($formCustomer['customer_name']);
        $customer_title = Input::sanitize($formCustomer['customer_title']);
        
        $db->insert("company_customers", ["company_id"=>$company_id,
                                        "customer_company_name"=>$customer_company_name,
                                        "customer_department"=>$customer_department,
                                        "customer_name"=>$customer_name,
                                        "customer_title"=>$customer_title]);
        $customer_id = $db->lastId();
        $input_guest = $customer_company_name." ".$customer_department." - ".$customer_name;
        
        $response = new Response();
        $response->assign('input_guest', 'value', $input_guest);
        $response->assign('customer_id', 'value', $customer_id);
        $response->script('$("#modalp").modal("hide");');
        return $response;
}
function removeCustomerFromGuests($customer_id){
    
    unset($_SESSION['Guests'][$customer_id]);
    $display = '<ul class="list-group col-md-6">';
        
        foreach ($_SESSION['Guests'] as $key => $value) {
            $display .= '<li onClick="jaxon_removeCustomerFromGuests('.$key.'); return false;" class="list-group-item"><i class="fa fa-times"></i>'.$value.'</li>';
        }

        $display .= '</ul>';
    
    $response = new Response();   
    $response->assign('contentGuestList', 'innerHTML', $display);
    $response->assign('input_guest', 'value', '');
    //$response->alert(print_r($_SESSION['Guests'],TRUE));
    return $response;
}
function syncSubscription($subscription_id){
    Global $company;
    $db = DB::getInstance();

    $confStripe = $company['stripe'];
    
    \Stripe\Stripe::setApiKey($confStripe['stripe_secret_key']);
    try {
        $subscription = \Stripe\Subscription::retrieve($subscription_id);
    } catch (Exception $e) {
        return FALSE;
    }
    $db->update('subscriptions',
                ['id' => $subscription_id],
                ['status' => $subscription->status,
                'current_period_start' => date('Y-m-d H:i:s ',$subscription->current_period_start),
                'current_period_end' => date('Y-m-d H:i:s ',$subscription->current_period_end),
                'date_canceled'=> (is_null($subscription->cancel_at) ? NULL : date('Y-m-d H:i:s ',$subscription->cancel_at)),
                'cancel_at_period_end' => $subscription->cancel_at_period_end,
                ]);
    if($db->error()) {
        return FALSE;
    } else {
        return date('Y-m-d H:i:s ',$subscription->current_period_end);
    }
   
}
function getSubscriptionStatus($fd_id,$plan_id){
    $db = DB::getInstance();
    $sql = "SELECT * FROM subscriptions where fd_id = $fd_id and plan='$plan_id'";
    
    $subscription_list = $db->query($sql);
    if($subscription_list->count() > 0){

        $subscription = $subscription_list->first();

        $id = $subscription->id;
        $current_status = $subscription->status;
        $current_period_end = $subscription->current_period_end;

        return (strtotime ($current_period_end) > time() ? 'active' : FALSE);

    }else {
        return 'FALSE';
    }
}
function checkSubscriptionStatus($fd_id,$plan_id){
    $db = DB::getInstance();
    $sql = "SELECT * FROM subscriptions where fd_id = $fd_id and plan='$plan_id'";
    
    $subscription_list = $db->query($sql);
    if($subscription_list->count() > 0){

        $subscription = $subscription_list->first();
        $subscription_id = $subscription->id;

        $current_period_end = syncSubscription($subscription_id);

        return (strtotime ($current_period_end) > time() ? 'active' : FALSE);

    }else {
        return FALSE;
    }
}
function send_subscription_welcome($to_mail,$to_name){
    global $lang;

    ob_start();
    include 'welcome_subscription_'.$lang['LANGUAGE_ID'].'.html';
    $body = ob_get_clean();

    $body = str_replace('{{Name}}', $to_name, $body);

    $mail = new PHPMailer;
    $mail->SMTPDebug = 0; // 2 SMTP::DEBUG_SERVER; 0 SMTP::DEBUG_OFF
    $mail->CharSet = 'UTF-8';
    $mail->setFrom("noreply@tpo.me", "Your Concierge");               
    $mail->isHTML(true);        
    $mail->IsSMTP();
    $mail->SMTPKeepAlive = true;
    $mail->SMTPSecure = 'ssl';
    $mail->Host = 'ssl://smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Port = 465;
    //gmail address/password as user name
    $mail->Username = 'noreply@tpo.me';
    $mail->Password = 'TPOnoreply2018!';

    $mail->Body = $body;
    $mail->Subject = 'Welcome to YourConcierge & You';
        
    $mail->addAddress($to_mail,$to_name);
    try {
        $mail->send();
        //$msg = " Sent - ".htmlspecialchars($to_mail);

    } catch (Exception $e) {

        $mail->getSMTPInstance()->reset();
        return FALSE;
    }
        //Clear all addresses for the next iteration
        $mail->clearAddresses();

        //file_put_contents('../public/log/log-notification_'.date("y-m-d").'.log', "\xEF\xBB\xBF".date('y-m-d H:i:s').' - '.$values->id_user.' - '.$values->id_event_schedule.' - '.$msg.PHP_EOL, FILE_APPEND);
        //echo ($msg."\n\r");
    
     return TRUE;
}
function send_subscription_unsubscribe($to_mail,$to_name,$date_end){
    global $lang;

    ob_start();
    include 'subscription_unsubscribe_'.$lang['LANGUAGE_ID'].'.html';
    $body = ob_get_clean();

    $body = str_replace('{{Renewal Date}}', $date_end, $body);

    $mail = new PHPMailer;
    $mail->SMTPDebug = 0; // 2 SMTP::DEBUG_SERVER; 0 SMTP::DEBUG_OFF
    $mail->CharSet = 'UTF-8';
    $mail->setFrom("noreply@tpo.me", "Your Concierge");               
    $mail->isHTML(true);        
    $mail->IsSMTP();
    $mail->SMTPKeepAlive = true;
    $mail->SMTPSecure = 'ssl';
    $mail->Host = 'ssl://smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Port = 465;
    //gmail address/password as user name
    $mail->Username = 'noreply@tpo.me';
    $mail->Password = 'TPOnoreply2018!';

    $mail->Body = $body;
    $mail->Subject = $lang['UNSUBSCRIBE_MAIL_SUBJECT'];
    $mail->addAddress($to_mail,$to_name);
    try {
        $mail->send();
        //$msg = " Sent - ".htmlspecialchars($to_mail);

    } catch (Exception $e) {

        $mail->getSMTPInstance()->reset();
        return FALSE;
    }
        //Clear all addresses for the next iteration
        $mail->clearAddresses();

        //file_put_contents('../public/log/log-notification_'.date("y-m-d").'.log', "\xEF\xBB\xBF".date('y-m-d H:i:s').' - '.$values->id_user.' - '.$values->id_event_schedule.' - '.$msg.PHP_EOL, FILE_APPEND);
        //echo ($msg."\n\r");
    
     return TRUE;
}
function getSubscriptionDetail($fd_id,$plan_id){
        $db = DB::getInstance();
        
        $sql = "SELECT id as subscription_id,customer, fd_id,status,date(date_created) as date_created,date(current_period_start) as current_period_start,date(current_period_end) as current_period_end  
        FROM subscriptions where fd_id = $fd_id and plan='$plan_id'";
        
        $subscription_list = $db->query($sql);
        if($subscription_list->count() > 0){
    
            $subscription = $subscription_list->first();
    
            return $subscription;

        } else {
            return FALSE;
        }
    
}
function getCompanyTenantList($company_id){
    $db = DB::getInstance();
    $sql = "SELECT * FROM company_tenant where company_primary_id = $company_id";
    $db->query($sql);
    return $db->results();
}
function getCompanyBuildingList($company_id){
    $db = DB::getInstance();
    $sql = "SELECT * FROM company_building where company_primary_id = $company_id";
    $db->query($sql);
    return $db->results();
}
function getSubscriptionCustomer($fd_id){
    $db = DB::getInstance();
    $sql = "SELECT * FROM subscriptions where fd_id = $fd_id";
    
    $subscription_list = $db->query($sql);
     if($subscription_list->count() > 0){ 

        $subscription = $subscription_list->first();
        return $subscription->customer;
    }else {
        return NULL;
    }
}
function displaySubscription(){
    Global $lang;Global $user;Global $company;

    $fd_id=$user->data()->fd_id;
    $plan_id = $company['subscription_id'];

    $checkSubscriptionStatus = checkSubscriptionStatus($fd_id,$plan_id);

    $subscription_detail = getSubscriptionDetail($fd_id,$plan_id);

    if ($subscription_detail){
        $display = ' <ul class="list-group list-group-flush">                                            
                    <li class="list-group-item">
                        <i class="fa fa-calendar-o"></i> 
                        <span class="font-lato">'.$lang['SUBSCRIPTION_DATE_CREATION'].': '.formatDateJP($subscription_detail->date_created).'</span>                                               
                    </li>
                    <li class="list-group-item">
                        <i class="fa fa-hourglass-start"></i> 
                        <span class="font-lato">'.$lang['SUBSCRIPTION_CURRENT_START'].': '.formatDateJP($subscription_detail->current_period_start).'</span>                                               
                    </li>
                    <li class="list-group-item">
                        <i class="fa fa-hourglass-end"></i> 
                        <span class="font-lato">'.$lang['SUBSCRIPTION_CURRENT_END'].': '.formatDateJP($subscription_detail->current_period_end).'</span>                                           
                    </li>              
                </ul>
                <p>'.$lang['SUBSCRIPTION_DISCLAMER'].'</p>';
    }
    $submitButton = "<button type='button' class='btn btn-default' data-dismiss='modal'>".$lang['BUTTON_CANCEL']."</button>
        <button type='button' class='btn btn-primary' onclick='var answer = confirm (\"".$lang['SUBSCRIPTION_UNSUBSCRIBE_CONFIRM']."\");if (answer){ jaxon_cancelSubscription(\"".$subscription_detail->subscription_id."\"); return false;}'>".$lang['SUBSCRIPTION_UNSUBSCRIBE']."</button>";  
    
    $body = $display;
    $footer = '';
    
    $response = new Response();
    $response->assign('contentModal', 'innerHTML', $body);
    $response->assign('modal-footer', 'innerHTML',$submitButton);
    $response->script('$("#modal-title").html("Subscription");$("#modalp").modal({"show":true});');
    
    return $response;
}
function cancelSubscription($subscription_id){
    Global $lang; Global $company; Global $user;
    $response = new Response();
    $contact = $user->data();

    $confStripe = $company['stripe'];
    
    \Stripe\Stripe::setApiKey($confStripe['stripe_secret_key']);
    try {
        $subscription = \Stripe\Subscription::update(
            $subscription_id,
            [
            'cancel_at_period_end' => true,
            ]
        );
    } catch (Exception $e) {
        //$response->alert(print_r($e,true));
        $response->alert($lang['STH_WENT_WRONG']);
        return $response;
    }
    $db = DB::getInstance();
    $db->update('subscriptions',
                ['id' => $subscription_id],
                ['status' => $subscription->status,
                'current_period_start' => date('Y-m-d H:i:s ',$subscription->current_period_start),
                'current_period_end' => date('Y-m-d H:i:s ',$subscription->current_period_end),
                'date_canceled'=> (is_null($subscription->cancel_at) ? NULL : date('Y-m-d H:i:s ',$subscription->cancel_at)),
                'cancel_at_period_end' => $subscription->cancel_at_period_end,
                ]);
    if($db->error()) {
        $response->alert($db->errorString());
    } else {
        $date_end = date('Y-m-d',$subscription->current_period_end);
        send_subscription_unsubscribe($contact->email,$contact->fname,$date_end);
        $response->alert('Cancelation successful');
        $response->redirect('index.php');
    }
   
    return $response;
}
function registerSubscriptionResult($session_id){
    Global $lang;Global $user;Global $company;
    $db = DB::getInstance();

    $confStripe = $company['stripe'];
    \Stripe\Stripe::setApiKey($confStripe['stripe_secret_key']);
    
    $checkout_session = \Stripe\Checkout\Session::retrieve($session_id);
    $subscription = \Stripe\Subscription::retrieve($checkout_session->subscription);

    if (!empty($subscription->status)){
        $fd_id = $checkout_session->client_reference_id;

        $query = $db->insert("subscriptions",
                    ['id' => $checkout_session->subscription,
                     'fd_id'=>$fd_id, 
                     'customer'=>$checkout_session->customer,
                     'email' => $checkout_session->customer_email,
                     'plan' => $subscription->items->data[0]->plan->id,
                     'status'=>$subscription->status,
                     'date_start' => date('Y-m-d H:i:s ',$subscription->start_date),
                     'date_created'=>date('Y-m-d H:i:s ',$subscription->created),
                     'current_period_start' =>  date('Y-m-d H:i:s ',$subscription->current_period_start),
                     'current_period_end'=> date('Y-m-d H:i:s ',$subscription->current_period_end),
                     'date_canceled'=> (is_null($subscription->cancel_at) ? NULL : date('Y-m-d H:i:s ',$subscription->cancel_at)),
                    ],TRUE);

        $msg = $lang['SUBSCRIPTION_SUCCEED'];
                    
        $account = $user->data();
        $fname=$account->fname;$lname=$account->lname;
        $email=$account->email;
        
        $name = createName($fname, $lname, $lang['CODE']);
        
        $r = send_subscription_welcome($email,$name);
    }
    else {
        $msg = $lang['SUBSCRIPTION_FAILED'];
    }
    $response = new Response();
    $response->alert($msg);
    $response->redirect('index.php');
    return $response;
}
function getMembershipStatus($fd_id){
    $freshUsers= new freshdesk();    
    $values = $freshUsers->getUser($fd_id);
    
        $membership_status = $values['custom_fields']['membership'];
        
    return $membership_status;
}
function displayFormSponsorshipRequest(){
    Global $lang;Global $user;

    $fd_id=$user->data()->fd_id;

    $form = '<p>'.$lang['FRM_SPONSORSHIP_BODY'].'</p>
            <form class="sponsorshipForm" id="sponsorship" onsubmit="return jaxon_sendSponsorshipRequest(jaxon.getFormValues(\'sponsorship\'));">    
            <div class="form-row">
                <div class="col">
                    <div class="form-group">
                        <label for="family_name_kanjiromaji">'.$lang['FRM_FAMILY_NAME'].'</label>
                        <input type="text" class="form-control" id="family_name_kanjiromaji" name="family_name_kanjiromaji" required >
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="first_name_kanjiromaji">'.$lang['FRM_FIRST_NAME'].'</label>
                        <input type="text" class="form-control form-control-sm" id="first_name_kanjiromaji" name="first_name_kanjiromaji" required >                    
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col">
                    <div class="form-group">                
                        <label for="email">'.$lang['FRM_EMAIL'].'*</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
            </div>
            <div class="form-group"> 
                    <button type="button" class="btn btn-default" data-dismiss="modal">'.$lang['BUTTON_CANCEL'].'</button>              
                    <button type="submit" class="btn btn-primary">'.$lang['REQUEST_SEND_BUTTON'].'</button>
            </div>            
            <p>'.$lang['FRM_SPONSORSHIP_FOOTER'].'</p>
            <div class="invisible" >
                <input type="text" name="fd_id" id="fd_id" class="form-control" value="'.$fd_id.'">
            </div>                    
            </form>';
    $body = '<p>'.$lang['FRM_SPONSORSHIP_BODY'].'</p>'.$form.'<p>'.$lang['FRM_SPONSORSHIP_FOOTER'].'</p>';    
    $footer = '';
    
    $response = new Response();
    $response->assign('contentModal', 'innerHTML', $form);
    $response->assign('modal-footer', 'innerHTML',$footer);
    $response->script('$("#modal-title").html("'.$lang['FRM_SPONSORSHIP_TITLE'].'");$("#modalp").modal({"show":true});');
    
    return $response;
}
function sendSponsorshipRequest($formValues){
    global $lang;
    $mailBody = 'Requester: '.$formValues['fd_id'].'<br><br>
    Family Member:<br>
    First name: '.$formValues['first_name_kanjiromaji'].'<br>
    Last name: '.$formValues['family_name_kanjiromaji'].'<br>
    Email: '.$formValues['email'].'<br>';

    $mailAddress = 'familyaccount@tpo.me';

    $mail = new PHPMailer();
    try {
        $mail->SMTPDebug = 0;        
        $mail->IsSMTP();
        $mail->Host = 'ssl://smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->CharSet = 'UTF-8';
        //gmail address/password as user name
        $mail->Username = 'noreply@tpo.me';
        $mail->Password = 'TPOnoreply2018!';
        
        $mail->setFrom("noreply@tpo.me", "Your Concierge");
        $mail->addAddress($mailAddress);
        
        $mail->isHTML(true);
        $mail->Subject = $lang['FRM_SPONSORSHIP_TITLE']." - ".$formValues['email'];
        $mail->Body = $mailBody;   
        
        $mail->send();
        $msg = $lang['FRM_SPONSORSHIP_TITLE']. ' request sent';
    }catch (Exception $e) {
        echo "Request could not be sent. Error: ".$mail->ErrorInfo;
        $msg = $lang['STH_WENT_WRONG'];
    }

    $response = new Response();
    $response->alert($msg);
    $response->assign('contentModal', 'innerHTML', '');
    $response->assign('modalp', 'style.display', 'none');
    $response->redirect('index.php');
    return $response;

}
