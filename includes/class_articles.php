<?php

class articles {
    
    function getArticle_tobedeleted($idArticle, $codeLang = 'ja-JP'){
        Global $db2;
  
        $sql="SELECT * FROM fd_articles            
            where article_id=$idArticle and language='$codeLang'";

        $result = $db2->query($sql);
        $row = $db2->get_row($result);
        return $row;
    }
    function getNews($idCompany,$codeLang = 'ja-JP'){
        Global $db;
        
        /*
         * fd_articles.status => 1: draft, 2: published
         * fd_articles.category_id => 35000132430 : Live News
         * fd_folders.visibility => 1: All Users, 2: Logged In Users, 4: Selected Companies
        */
        
        $sql="SELECT fd_articles.*,fd_articles_img.img_url FROM fd_articles
            join fd_folders on fd_articles.folder_id = fd_folders.id
            left join fd_folders_companies on fd_folders.id=fd_folders_companies.id_folder
            left join fd_articles_img on fd_articles.article_id = fd_articles_img.article_id
            where fd_articles.category_id in (35000132430) AND fd_articles.status=2 and fd_articles.language='$codeLang'
            and ((fd_folders.visibility in (1,2)) or (fd_folders.visibility = 4 and fd_folders_companies.id_company=$idCompany))
            order by fd_articles.updated_at desc";
        $db->query($sql);
        return $db->results();
    }
    function getExample($idCompany,$codeLang = 'ja-JP'){
        Global $db;
        
        /*
         * fd_articles.status => 1: draft, 2: published
         * fd_articles.category_id => 35000137386 : Live Examples
         * fd_folders.visibility => 1: All Users, 2: Logged In Users, 4: Selected Companies
        */
        
        $sql="SELECT fd_articles.*,fd_articles_img.img_url FROM fd_articles
            join fd_folders on fd_articles.folder_id = fd_folders.id
            left join fd_folders_companies on fd_folders.id=fd_folders_companies.id_folder
            left join fd_articles_img on fd_articles.article_id = fd_articles_img.article_id
            where fd_articles.category_id in (35000137386) AND fd_articles.status=2 and fd_articles.language='$codeLang'
            and ((fd_folders.visibility in (1,2)) or (fd_folders.visibility = 4 and fd_folders_companies.id_company=$idCompany))
            order by fd_articles.updated_at desc";
        $db->query($sql);
        return $db->results();
    }
    function getEventAttendees($codeLang = 'ja-JP'){
        Global $db;
        
        /*
         * fd_articles.status => 1: draft, 2: published
         * fd_articles.category_id => 35000132685 : Live Events
         * fd_folders.visibility => 1: All Users, 2: Logged In Users, 4: Selected Companies
        */
        
        $sql="SELECT fd_folders.name,IF(date_start is NULL,0,1) as  start_order,date_start,qty_total,'0' as qty_attd, fd_articles.* FROM fd_articles
            join fd_folders on fd_articles.folder_id = fd_folders.id
            left join (select id_event,min(date_start) as date_start,SUM(quantity) as qty_total from event_schedule group by id_event) schedule on fd_articles.article_id = schedule.id_event
            where fd_articles.category_id in (35000132685) AND fd_articles.status=2 and language='$codeLang'           
            order by start_order,date_start desc";
        
        //$data = $db->query($sql)->result();
        $db->query($sql);
        return $db->results();
    }
    function getBlog($idCompany,$codeLang = 'ja-JP'){
        Global $db;
        
        /*
         * fd_articles.status => 1: draft, 2: published
         * fd_articles.category_id => 35000132430 : Live News
         * fd_folders.visibility => 1: All Users, 2: Logged In Users, 4: Selected Companies
        */
        
        $sql="SELECT fd_articles.*,fd_articles_img.img_url FROM fd_articles
            join fd_folders on fd_articles.folder_id = fd_folders.id
            left join fd_folders_companies on fd_folders.id=fd_folders_companies.id_folder
            left join fd_articles_img on fd_articles.article_id = fd_articles_img.article_id
            where fd_articles.category_id in (35000132430) AND fd_articles.status=2 and fd_articles.language='$codeLang'
            and ((fd_folders.visibility in (1,2)) or (fd_folders.visibility = 4 and fd_folders_companies.id_company=$idCompany))
            order by fd_articles.updated_at desc";
        
        //$data = $db->query($sql)->result();
        $db->query($sql);
        return $db->results();
    }
    function getBNews($idCompany,$codeLang = 'ja-JP',$limit = NULL){
        Global $db;
        
        /*
         * fd_articles.status => 1: draft, 2: published
         * fd_articles.category_id => 35000134428 : Live News single line
         * fd_folders.visibility => 1: All Users, 2: Logged In Users, 4: Selected Companies
        */
        $crtLimit="";
        if (!empty($limit)){
            $crtLimit= "Limit ".$limit;
        }
        
        $sql="SELECT fd_articles.* FROM fd_articles
            join fd_folders on fd_articles.folder_id = fd_folders.id
            left join fd_folders_companies on fd_folders.id=fd_folders_companies.id_folder
            where fd_articles.category_id in (35000134428) AND fd_articles.status=2 and fd_articles.language='$codeLang'
            and ((fd_folders.visibility in (1,2)) or (fd_folders.visibility = 4 and fd_folders_companies.id_company=$idCompany))
            order by fd_articles.updated_at desc ".$crtLimit;
        
        //$data = $db->query($sql)->result();
        $db->query($sql);
        return $db->results();
    }
    function getFAQs($idCompany,$codeLang = 'ja-JP'){
        Global $db;
        
        /*
         * fd_articles.status => 1: draft, 2: published
         * fd_articles.category_id => 35000133840 : Live FAQ
         * fd_folders.visibility => 1: All Users, 2: Logged In Users, 4: Selected Companies
        */
        
        $sql="SELECT fd_articles.* FROM fd_articles
            join fd_folders on fd_articles.folder_id = fd_folders.id
            left join fd_folders_companies on fd_folders.id=fd_folders_companies.id_folder
            where fd_articles.category_id in (35000133840) AND fd_articles.status=2 and fd_articles.language='$codeLang'
            and ((fd_folders.visibility in (1,2)) or (fd_folders.visibility = 4 and fd_folders_companies.id_company=$idCompany))
            order by fd_articles.created_at desc";
        
        $db->query($sql);
        return $db->results();
    }
    function getTags($idCategory,$idCompany,$codeLang = 'ja-JP'){
        Global $db;
        
        /*
         * fd_articles.status =>        1: draft, 2: published
         * fd_articles.category_id =>   35000132685 : Live EVENTS
         *                              35000132430 : Live News
         * fd_folders.visibility =>     1: All Users, 2: Logged In Users, 4: Selected Companies
         * 35000131126
        */
        
        $sql="SELECT fd_articles_tags.tag, count(distinct fd_articles_tags.article_id) as nb  FROM fd_articles_tags
            join fd_articles on fd_articles_tags.article_id = fd_articles.article_id
            join fd_folders on fd_articles.folder_id = fd_folders.id
            left join fd_folders_companies on fd_folders.id = fd_folders_companies.id_folder
            where fd_articles.category_id in ($idCategory) and fd_articles.status=2
            and ((fd_folders.visibility in (1,2)) or (fd_folders.visibility = 4 and fd_folders_companies.id_company=$idCompany))
            group by fd_articles_tags.tag
            order by fd_articles_tags.tag";
        // and fd_articles.language='$codeLang'
        //$data = $db->query($sql)->result();
        $db->query($sql);
        return $db->results();
    }
    function getEventsSorted(){
        Global $db;
        $sql="SELECT id_event,min(concat(date_start,' ',time_start)) as dt
                FROM event_schedule
                where concat(date_start,' ',time_start) > CONVERT_TZ(now(),'+00:00','+09:00')
                group by id_event
                order by dt";
        $db->query($sql);
        return $db->results();
    }
    function getEvents($idCompany,$codeLang = 'ja-JP'){
        Global $db;
        
        /*
         * fd_articles.status => 1: draft, 2: published
         * fd_articles.category_id => 35000132685 : Live EVENTS
         * fd_folders.visibility => 1: All Users, 2: Logged In Users, 4: Selected Companies
         * 35000131126
        */
        
        $sql="SELECT distinct fd_articles.*,fd_articles_img.img_url, date_start, qty, qty_booked FROM fd_articles
            join fd_folders on fd_articles.folder_id = fd_folders.id
            left join fd_folders_companies on fd_folders.id = fd_folders_companies.id_folder
            left join fd_articles_img on fd_articles.article_id = fd_articles_img.article_id      
            join 
            (select id_event,date_start,SUM(s.quantity) as qty, SUM(IFNULL(qty_booked,0)) as qty_booked
				from event_schedule s
				left join (select id_event_schedule, SUM(IFNULL(quantity,0)) as qty_booked from event_attendees where deleted_at is null group by id_event_schedule) a 
				on s.id_event_schedule = a.id_event_schedule
				group by id_event,date_start) booked 
            on fd_articles.article_id = booked.id_event
            where fd_articles.category_id in (35000132685) and fd_articles.status=2 and fd_articles.language='$codeLang'
            and ((fd_folders.visibility in (1,2)) or (fd_folders.visibility = 4 and fd_folders_companies.id_company=$idCompany))
            order by article_id desc, date_start asc";
        
        //$data = $db->query($sql)->result();
        $db->query($sql);
        return $db->results();
    }
    function getEvent($idEvent,$idCompany,$codeLang = 'ja-JP'){
        Global $db;
        
        /*
         * fd_articles.status => 1: draft, 2: published
         * fd_articles.category_id => 35000132685 : Live EVENTS
         * fd_folders.visibility => 1: All Users, 2: Logged In Users, 4: Selected Companies
         * 35000131126
        */
        $sql="";
        
        $sql="SELECT fd_articles.*,fd_articles_img.img_url,date_start,TIME_FORMAT(time_start,'%H:%i') as time_start,TIME_FORMAT(time_end,'%H:%i') as time_end,id_event_schedule,quantity,quantity_max,qty_booked FROM fd_articles
            join fd_folders on fd_articles.folder_id = fd_folders.id
            left join fd_folders_companies on fd_folders.id = fd_folders_companies.id_folder
            left join fd_articles_img on fd_articles.article_id = fd_articles_img.article_id            
            join (select s.id_event_schedule, id_event,date_start,date_end,time_start,time_end,s.quantity,s.quantity_max ,SUM(IF(a.quantity is null,0,a.quantity)) as qty_booked from event_schedule s left join event_attendees a on s.id_event_schedule = a.id_event_schedule and deleted_at is null group by s.id_event_schedule) schedule on fd_articles.article_id = schedule.id_event            
            where fd_articles.article_id = $idEvent and fd_articles.category_id in (35000132685) and fd_articles.status=2 and fd_articles.language='$codeLang'
            and ((fd_folders.visibility in (1,2)) or (fd_folders.visibility = 4 and fd_folders_companies.id_company=$idCompany))
            order by date_start,time_start";
        
        //$data = $db->query($sql)->result();
        $db->query($sql);
        return $db->results();
    }
    function getPost($idPost,$idCompany,$codeLang = 'ja-JP'){
        Global $db;
        
        /*
         * fd_articles.status => 1: draft, 2: published
         * fd_articles.category_id => 35000132430 : Live Blogs
         * fd_articles.category_id => 35000135533 : Live Cards
         * fd_articles.category_id => 35000137386 : Live Examples
         * fd_folders.visibility => 1: All Users, 2: Logged In Users, 4: Selected Companies
        */
        
        $sql="SELECT fd_articles.*,fd_articles_img.img_url FROM fd_articles
            join fd_folders on fd_articles.folder_id = fd_folders.id
            left join fd_folders_companies on fd_folders.id = fd_folders_companies.id_folder
            left join fd_articles_img on fd_articles.article_id = fd_articles_img.article_id            
            where fd_articles.article_id = $idPost and fd_articles.category_id in (35000132430,35000135533,35000137386) and fd_articles.status=2 and fd_articles.language='$codeLang'
            and ((fd_folders.visibility in (1,2)) or (fd_folders.visibility = 4 and fd_folders_companies.id_company=$idCompany))
            order by fd_articles.created_at desc";
        
        //$data = $db->query($sql)->result();
        $db->query($sql);
        return $db->results();
    }
    
    
}
