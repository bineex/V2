<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set("UTC");

chdir(dirname(__FILE__));
$_SERVER['HTTP_HOST'] = '';
$NOSESSION = 1;

require_once ('../vendor/autoload.php');
require_once ('../init.php');
require_once ('../includes/class_freshdesk.php');
require_once ('../includes/class_articles.php');
require_once ('../includes/class_account.php');
$db = DB::getInstance();

function UpdateTableArticleImg(){
    global $db;

    $sql= 'select distinct article_id from fd_articles_img order by updated_at';
    $db->query($sql);
    $r = $db->results();
   
    foreach ($r as $key=>$value) {
        $id_article = $value->article_id;

        $freshObj= new freshdesk();
        $article = $freshObj->getArticle($id_article,'ja-JP');

        if (isset($article['id']) && $article['id'] == $id_article){
            $attch="";
            if (isset($article['attachments']) && count($article['attachments'])>0){
                
                foreach ($article['attachments'] as $attachment){
                    $attch=$attachment['attachment_url'];

                    date_default_timezone_set("UTC");
                    $query = $db->insert("fd_articles_img",
                    ['article_id'=>$id_article, 'img_url'=>$attachment['attachment_url'], 'updated_at'=>date("Y-m-d H:i:s")
                    ],TRUE);
                }
                echo ($id_article."- updated"."\n");
            }
            else {
                echo ($id_article."- no image"."\n");
            }
        }
        else {
            echo ($id_article."- not existing"."\n");
        }
          
    }
    return true;
}

$r_jp = UpdateTableArticleImg();