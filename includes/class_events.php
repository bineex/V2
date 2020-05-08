<?php

class events {
    
    function getFullListEvents(){
        $db = DB::getInstance();
        
        /*
         * fd_articles.status => 1: draft, 2: published
         * fd_articles.category_id => 35000132685 : Live Events
         * fd_folders.visibility => 1: All Users, 2: Logged In Users, 4: Selected Companies
        */
        
        $sql="SELECT fd_folders.name,IF(date_start is NULL,0,1) as start_order, date_start,qty_total,qty_booked, fd_articles.* FROM fd_articles
            join fd_folders on fd_articles.folder_id = fd_folders.id
            left join (select id_event,min(date_start) as date_start,SUM(s.quantity) as qty_total,SUM(a.quantity) as qty_booked from event_schedule s left join event_attendees a on s.id_event_schedule = a.id_event_schedule group by id_event) schedule on fd_articles.article_id = schedule.id_event
            where fd_articles.category_id in (35000132685) AND fd_articles.status=2           
            order by start_order,date_start desc";
        
        $sql="SELECT fd_folders.name,IF(date_start is NULL,0,1) as start_order, date_start,qty_total,qty_booked, fd_articles.* FROM fd_articles
                join fd_folders on fd_articles.folder_id = fd_folders.id
                left join 
                        (select id_event,min(date_start) as date_start,SUM(s.quantity) as qty_total, SUM(IF(qty_booked is NULL,0,qty_booked)) as qty_booked from event_schedule s left join (select id_event_schedule,SUM(IF(quantity is null,0,quantity)) as qty_booked from event_attendees where deleted_at is null group by id_event_schedule) a on s.id_event_schedule = a.id_event_schedule group by id_event) schedule 
                on fd_articles.article_id = schedule.id_event
                where fd_articles.category_id in (35000132685) AND fd_articles.status=2           
                order by start_order asc,date_start desc";
        
        //$data = $db->query($sql)->result();

        $db->query($sql);
        return $db->results();
    }
    
}
