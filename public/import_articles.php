<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

chdir(dirname(__FILE__));
$_SERVER['HTTP_HOST'] = '';
$NOSESSION = 1;

require_once ('../vendor/autoload.php');
require_once ('../init.php');
require_once ('../includes/class_freshdesk.php');
require_once ('../includes/class_articles.php');
require_once ('../includes/class_account.php');
$db = DB::getInstance();

function truncateSolutions(){
    global $db;
    
    $db->query("TRUNCATE TABLE fd_articles_img");
    $db->query("TRUNCATE TABLE fd_articles_tags");
    $db->query("TRUNCATE TABLE fd_articles");
    $db->query("TRUNCATE TABLE fd_folders_companies");
    $db->query("TRUNCATE TABLE fd_folders");
    $db->query("TRUNCATE TABLE fd_categories_portals");
    $db->query("TRUNCATE TABLE fd_categories");

    //$db->query("TRUNCATE TABLE fd_portals");
    
    return true;
}

function importSolutions(){
    global $db;
    
    //$r0 = truncateSolutions();
    
    $freshObj= new freshdesk();    
    $tabC = $freshObj->getCategories();

    foreach ($tabC as $keyC=>$vC){
        $query2 = $db->insert("fd_categories",
                ['id'=>$vC['id'], 'name'=>$vC['name'],'description'=>$vC['description'],'created_at'=>$vC['created_at'],'updated_at'=>$vC['updated_at']],TRUE);

        $r = importFolders_byCategory($vC['id']);

    }
     return true;
}
function importSolutionsEvents(){
    global $db;
    
    $freshObj= new freshdesk();    
    $vC = $freshObj->getCategoriesEvents();
        echo("id: ".$vC['name']);
        $query2 = $db->insert("fd_categories",
                ['id'=>$vC['id'], 'name'=>$vC['name'],'description'=>$vC['description'],'created_at'=>$vC['created_at'],'updated_at'=>$vC['updated_at']],TRUE);

        $r = importFolders_byCategory($vC['id']);

     return true;
}
function importFolders_byCategory($id_category){
    global $db;
    $freshObj= new freshdesk();    
    $tabF = $freshObj->getFolders($id_category);
   
    foreach ($tabF as $keyF=>$vF){
         $query = $db->insert("fd_folders",
                ['id'=>$vF['id'], 'name'=>$vF['name'],'description'=>$vF['description'],'created_at'=>$vF['created_at'],'updated_at'=>$vF['updated_at'],'visibility'=>$vF['visibility']],TRUE);
         
         //print_r($vF);
         echo '<br>';
        if (isset($vF['company_ids'])){
            foreach($vF['company_ids'] as $companies){
                $query3 = $db->insert("fd_folders_companies",
                    ['id_folder'=>$vF['id'], 'id_company'=>$companies]);
            }
        }
        $r1 = importArticles_byFolder($vF['id'],'en');
        $r2 = importArticles_byFolder($vF['id'],'ja-JP');
    }
     return true;

}

function importArticles_byFolder($id_folder,$lang){
   global $db;
    $freshObj= new freshdesk();   
    $tabA = $freshObj->getArticles($id_folder,$lang);
   
    
    foreach ($tabA as $keyA=>$vA){
        
        $tagText='';
        $article = $freshObj->getArticle($vA['id'],$lang);
        if (isset($article['tags']) && count($article['tags'])>0){
            $first=true;
            foreach ($article['tags'] as $tag){
                if (!$first){$tagText.=" | ";}
                $tagText.="$tag";
                $first=false;
                $query = $db->insert("fd_articles_tags",
                ['article_id'=>$vA['id'], 'tag'=>$tag
                ],TRUE);
            }
        }
        $attch="";
        if (isset($article['attachments']) && count($article['attachments'])>0){
            
            date_default_timezone_set("UTC");
            
            foreach ($article['attachments'] as $attachment){
                $attch=$attachment['attachment_url'];
                $query = $db->insert("fd_articles_img",
                ['article_id'=>$vA['id'], 'img_url'=>$attachment['attachment_url'], 'updated_at'=>date("Y-m-d H:i:s")
                ],TRUE);
            }
        }

         $query = $db->insert("fd_articles",
                ['article_id'=>$vA['id'], 'category_id'=>$vA['category_id'], 'folder_id'=>$vA['folder_id'],
                    'language'=>$lang,'description'=>$vA['description'],'description_text'=>$vA['description_text'],
                    'hits'=>$vA['hits'],'tags'=>$tagText,'status'=>$vA['status'],'title'=>$vA['title'],'status'=>$vA['status'],
                    'agent_id'=>$vA['agent_id'],'type'=>$vA['type'],
                    'created_at'=>$vA['created_at'],'updated_at'=>$vA['updated_at']
                 ],TRUE);

         echo $vA['id']. ' - '. $vA['title']. ' -> '. $tagText. ' - '. $attch;
         echo "<br>";
         //print_r($db->errorInfo());
         echo "<br>";
         
         
    }
     return true;

}
function importArticle($id_article,$lang){
    global $db;

    $tagText='';
    $freshObj= new freshdesk();
    $article = $freshObj->getArticle($id_article,$lang);

    if (isset($article['id']) && $article['id'] == $id_article){

        if (isset($article['tags']) && count($article['tags'])>0){
            $first=true;
            foreach ($article['tags'] as $tag){
                if (!$first){$tagText.=" | ";}
                $tagText.="$tag";
                $first=false;
                $query = $db->insert("fd_articles_tags",
                ['article_id'=>$id_article, 'tag'=>$tag
                ],TRUE);
            }
        }
        $attch="";
        if (isset($article['attachments']) && count($article['attachments'])>0){
            
            date_default_timezone_set("UTC");
            
            foreach ($article['attachments'] as $attachment){
                $attch=$attachment['attachment_url'];
                $query = $db->insert("fd_articles_img",
                ['article_id'=>$id_article, 'img_url'=>$attachment['attachment_url'], 'updated_at'=>date("Y-m-d H:i:s")
                ],TRUE);
            }
        }
 
          $query = $db->insert("fd_articles",
                 ['article_id'=>$id_article, 'category_id'=>$article['category_id'], 'folder_id'=>$article['folder_id'],
                     'language'=>$lang,'description'=>$article['description'],'description_text'=>$article['description_text'],
                     'hits'=>$article['hits'],'tags'=>$tagText,'status'=>$article['status'],'title'=>$article['title'],
                     'agent_id'=>$article['agent_id'],'type'=>$article['type'],
                     'created_at'=>$article['created_at'],'updated_at'=>$article['updated_at']
                  ],TRUE);
 
          echo $article['id']. ' - '. $article['title']. ' -> '. $tagText. ' - '. $attch;
          echo "<br>";
          return true;
    }
    else {
        echo 'Article '.$id_article.' does not exist';
    }
}

function importPortals(){
    global $db;
    $freshObj= new freshdesk();    
    $tabP = $freshObj->getProducts();
   
    foreach ($tabP as $keyP=>$vP){
         $query = $db->insert("fd_portals",
                ['id'=>$vP['id'], 'name'=>$vP['name']],TRUE);
    }
     return true;

}
function updateArticles_Img(){
   global $db;
   
   $sql= 'select min(update_at) as min_date from fd_articles_img';
   
   $db->query($sql);
   $r = $db->results();
   
            foreach ($r as $key => $value) {
                $tab[$key] = $value->id_event_schedule;
            }
            return $tab;
    foreach ($tabA as $keyA=>$vA){
        
        $tagText='';
        $article = $freshObj->getArticle($vA['id'],$lang);
        if (isset($article['tags']) && count($article['tags'])>0){
            $first=true;
            foreach ($article['tags'] as $tag){
                if (!$first){$tagText.=" | ";}
                $tagText.="$tag";
                $first=false;
                $query = $db->insert("fd_articles_tags",
                ['article_id'=>$vA['id'], 'tag'=>$tag
                ],TRUE);
            }
        }
        $attch="";
        if (isset($article['attachments']) && count($article['attachments'])>0){
            
            date_default_timezone_set("UTC");
            
            foreach ($article['attachments'] as $attachment){
                $attch=$attachment['attachment_url'];
                $query = $db->insert("fd_articles_img",
                ['article_id'=>$vA['id'], 'img_url'=>$attachment['attachment_url'], 'updated_at'=>date("Y-m-d H:i:s")
                ],TRUE);
            }
        }

         $query = $db->insert("fd_articles",
                ['article_id'=>$vA['id'], 'category_id'=>$vA['category_id'], 'folder_id'=>$vA['folder_id'],
                    'language'=>$lang,'description'=>$vA['description'],'description_text'=>$vA['description_text'],
                    'hits'=>$vA['hits'],'tags'=>$tagText,'status'=>$vA['status'],'title'=>$vA['title'],'status'=>$vA['status'],
                    'agent_id'=>$vA['agent_id'],'type'=>$vA['type'],
                    'created_at'=>$vA['created_at'],'updated_at'=>$vA['updated_at']
                 ],TRUE);

         echo $vA['id']. ' - '. $vA['title']. ' -> '. $tagText. ' - '. $attch;
         echo "<br>";
         //print_r($db->errorInfo());
         echo "<br>";
         
         
    }
     return true;

}
function importCompanies(){
    global $db;
    
    $db->query("TRUNCATE TABLE fd_companies");
    
    $freshObj= new freshdesk();    
    $tabC = $freshObj->getCompanies();
   //`id`, `name`, `description`, `note`, `renewal_date`, `industry`, `created_at`, `updated`
    foreach ($tabC as $keyC=>$vP){
         $query = $db->insert("fd_companies",
                ['id'=>$vP['id'], 'name'=>$vP['name'],'description'=>$vP['description'],
                    'note'=>$vP['note'],'renewal_date'=>$vP['renewal_date'],'industry'=>$vP['industry'],'created_at'=>$vP['created_at'],'updated_at'=>$vP['updated_at']]);
    }
     return true;

}
function displayArticle($id_article,$lang){
    global $db;
    $freshObj= new freshdesk();   
    $tabA = $freshObj->getArticle($id_article,$lang);
    print_r($tabA);
    return true;
    foreach ($tabA as $keyA=>$vA){
        
        $tagText='';
        echo '<br>';        
         print_r($vA);
         echo '<br>';
        if (isset($vA['tags']) && count($vA['tags'])>0){
            $first=true;
            foreach ($vA['tags'] as $tag){
                if (!$first){$tagText.="|";}
                $tagText.="$tag";
                $first=false;
            }
        }

         echo $vA['id']. ' - '. $vA['title']. ' -> '. $tagText;
         echo "<br>";
         //print_r($db->errorInfo());
         echo "<br>";
         
         
    }
     return true;

}
function updateUser_signup(){
    global $db;
    $sql="SELECT * FROM users where fd_id is not null and email like '%@cartier.com'";
    
    $db->query($sql);
    $users = $db->results();
    $i = 0;
    
    foreach ($users as $user) {
        $id = $user->fd_id;        
        $freshUsers= new freshdesk();           
        $values = $freshUsers->getUser($id);
        if (isset($values) and !empty ($values)){
            echo $id." => ".$values['active']." => ".$values['custom_fields']['signedup']."<br>";
            
            if ($values['active'] == 0){                
                $content['active']=TRUE;
                
                $customField['signedup'] = TRUE; 
                $content['custom_fields']=$customField;
                $i++;
            }
        }
    
    }
    echo $i;
    return true;
}

function updateTicket_category(){
    global $db;
    $sql="select * from fd_ticket_category where state = 'C'";
    
    $db->query($sql);
    $tickets = $db->results();
    
    foreach ($tickets as $ticket) {
        $id = $ticket->id_ticket;
        echo $id . " => ". $ticket->category."<br>";
        
        $customField['cf_category'] = $ticket->category; 
        $content['custom_fields']=$customField;

        $freshUsers= new freshdesk();    
        $result = $freshUsers->updateTicket($id,$content);
        
        $result_txt = $result['custom_fields']['cf_category'];
        echo $result_txt."<br><br>";
        
        $db->query("update fd_ticket_category set state='$result_txt' where id_ticket = $id");
        
    }
    return true;

}

function importAgents($email_agent = NULL){
    global $db;
    $freshUsers= new freshdesk();           
    $listAgents = $freshUsers->getAgents();
    
    foreach ($listAgents as $key => $agent){
        
        if (isset($agent['contact']['email']) && !empty($agent['contact']['email']) && isset($agent['contact']['name']) && !empty($agent['contact']['name'])){
            if ($email_agent != NULL && $agent['contact']['email'] != $email_agent){
                continue;
            }
            $email = $agent['contact']['email'];
            $tname = explode(" ",$agent['contact']['name']);
            
            if ($agent['contact']['active']){$active = "[ON]";} else {$active = "[OFF]";}
            
            $account = new account();
            $existUser = $account->getAccount_byEmail($email);
            if (count($existUser) > 0){
                echo "[Duplicated] ".$agent['id']." ".$tname[0]." - ".$tname[1]." - ".$agent['contact']['email']." ".$active;
                echo "<br>";
                continue;
            }
            
            
            echo $agent['id']." ".$tname[0]." - ".$tname[1]." - ".$agent['contact']['email']." ".$active;
            echo "<br>";
            
            $user = new User();
            $vericode = randomstring(15);
            $vericode_expiry=date("Y-m-d H:i:s");
            
            $user->create(array(
                    'username' => $email,
                    'fd_id' => $agent['id'],
                    'fname' => $tname[0],
                    'lname' => $tname[1],
                    'email' => $email,
                    'email_work' => $email,
                    'password' => password_hash($tname[1], PASSWORD_BCRYPT, array('cost' => 12)),
                    'permissions' => 2,
                    'account_owner' => 1,
                    'join_date' => $vericode_expiry,
                    'email_verified' => 1,
                    'active' => 1,
                    'vericode' => $vericode,
                    'vericode_expiry' => $vericode_expiry
            ));
            
        }
            
        
        
    }
}

if(isset($_GET['article']) && !empty($_GET['article'])){
    $id_article = $_GET['article'];
    $r_en = importArticle($id_article,'en');
    $r_jp = importArticle($id_article,'ja-JP');
}
elseif (isset($_GET['events'])){
    $r3 = importSolutionsEvents();
}
