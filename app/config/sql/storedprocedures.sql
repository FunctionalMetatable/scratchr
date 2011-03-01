-- MySQL dump 10.11
--
-- Host: localhost    Database: beta
-- ------------------------------------------------------
-- Server version	5.0.77-log
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping routines for database 'beta'
--
DELIMITER ;;
/*!50003 SET SESSION SQL_MODE=""*/;;
/*!50003 CREATE*/ /*!50020 DEFINER=`anant`@`scratchweb1.media.mit.edu`*/ /*!50003 PROCEDURE `AggregateUserReport`()
BEGIN
DECLARE total_users int(10) default 0;
DECLARE
	start_user_id,	end_user_id INT(10);

set start_user_id = 1;
set end_user_id   = start_user_id + 1000;

drop table if exists aggregate_user_report_tmp ;
create table aggregate_user_report_tmp
(user_id                            int(10),
username                            varchar(45),
gender                              varchar(35),
byear                               varchar(45),
bmonth                              varchar(45),
prj_count      int(10),
tot_scripts    int(10),
tot_sprites int(10),
projects_visited int(10),
gallery_count int(10),
user_comment_with_no_reply int(10),
user_comments_with_replies int(10),
user_response_to_others_comments int(10),
comments_replies_on_users_comments int(10),
tag_count int(10),
tags_on_user_project int(10),
downloads_by_user_count int(10),
downloads_of_users_projects_count int(10),
censored_project_count int(10),
deleted_by_admin_project_count int(10),
deleted_by_user_project_count int(10),
safe_projects_of_user_flagged_inapp_by_others int(10),
not_safe_projects_of_user_flagged_inapp_by_others int(10),
feat_proj_count int(10),
feat_gal_count int(10),
total_proj_loveits int(10),
loveit_on_others_projects_by_user int(10),
favorited_others_projects_count int(10),
favorited_own_projects_count int(10),
projects_flagged_inapp_by_user int(10),
user_projects_flagged_inapp_by_others int(10),
comments_flagged_inapp_by_others int(10),
remixed_others_projects_count int(10),
remixed_own_project_count int(10),
users_projects_remixed_by_others_count int(10),
remix_projects_of_user_project_flagged_by_others int(10)
);

select max(id) into total_users from users;

WHILE start_user_id <= total_users DO
INSERT INTO aggregate_user_report_tmp
select
usrs.id,
usrs.username,
usrs.gender,
usrs.byear,
usrs.bmonth,
        prjs.prj_count,
        prjs.tot_scripts,
        prjs.tot_sprites,
        view_stats.projects_visited,
        galleries.gallery_count,
        gcomments_1.user_gcomment_with_no_reply + pcomments_1.user_pcomment_with_no_reply,
        gcomments_2.gcomments_with_replies + pcomments_2.pcomments_with_replies,
        gcomments_3.gcomment_user_response_to_others_comments + pcomments_3.pcomment_user_response_to_others_comments,
        gcomments_4.gcomments_replies_on_users_gallery_comments + pcomments_4.pcomments_replies_on_users_project_comments,
        taggers_1.tag_count,
        taggers_2.tags_on_user_project,
        downloaders_1.downloads_by_user_count,
        downloaders_2.downloads_of_users_projects_count,
        projects_2.censored_project_count,
        projects_2.deleted_by_admin_project_count,
        projects_2.deleted_by_user_project_count,
        flaggers_1.safe_projects_of_user_flagged_inapp_by_others,
        flaggers_1.not_safe_projects_of_user_flagged_inapp_by_others,
        feat_projects.feat_proj_count,
        feat_galleries.feat_gal_count,
        prjs.total_proj_loveits,
        lovers.loveit_on_others_projects_by_user,
        favs_1.favorited_others_projects_count,
        favs_1.favorited_own_projects_count,
        flaggers_3.projects_flagged_inapp_by_user,
        flaggers_4.user_projects_flagged_inapp_by_others,
        pcomments_5.pcomments_flagged_inapp_by_others + gcomments_5.gcomments_flagged_inapp_by_others,
        remix_1.remixed_others_projects_count,
        remix_2.remixed_own_project_count,
        remix_3.users_projects_remixed_by_others_count,
        remix_4.remix_projects_of_user_project_flagged_by_others
from
    (select id,username, gender,byear,bmonth
     from users a
     where a.id between start_user_id and end_user_id) usrs


left outer join
     (select user_id,count(1) as prj_count, sum(totalScripts) as tot_scripts, sum(numberOfSprites) as tot_sprites, sum(loveit) as total_proj_loveits
      from projects
      where user_id between start_user_id and end_user_id
      group by user_id) prjs on ( prjs.user_id = usrs.id)

left outer join
      (select user_id, count(1) as projects_visited
        from view_stats a
        where project_id not in ( select id
                        from projects
                        where user_id = a.user_id)
        and a.user_id between start_user_id and end_user_id
        group by user_id ) view_stats on (view_stats.user_id = usrs.id)

left outer join
     (select user_id,count(1) as gallery_count
      from galleries
      where user_id between start_user_id and end_user_id
      group by user_id) galleries on (galleries.user_id = usrs.id)
left outer join
( select user_id,count(1) as user_gcomment_with_no_reply
  from gcomments a
  where not exists ( select 1
                 from gcomments b
                 where b.reply_to = a.id)
  and a.user_id between start_user_id and end_user_id
  group by user_id)  gcomments_1 on (gcomments_1.user_id = usrs.id)

left outer join
( select user_id,count(1) as user_pcomment_with_no_reply
  from pcomments a
  where not exists ( select 1
                 from pcomments b
                 where b.reply_to = a.id)
  and a.user_id between start_user_id and end_user_id
  group by user_id)  pcomments_1 on (pcomments_1.user_id = usrs.id)

left outer join
(select a.user_id, count(1) as gcomments_with_replies
  from gcomments a, gcomments b
where b.reply_to = a.id
and a.user_id between start_user_id and end_user_id
group by a.user_id ) gcomments_2 on ( gcomments_2.user_id = usrs.id )

left outer join
(select a.user_id, count(1) as pcomments_with_replies
 from pcomments a, pcomments b
where b.reply_to = a.id
and a.user_id between start_user_id and end_user_id
group by a.user_id ) pcomments_2 on ( pcomments_2.user_id = usrs.id)

left outer join
(select a.user_id, count(1) as gcomment_user_response_to_others_comments
 from gcomments a
 where reply_to > 0
and a.user_id between start_user_id and end_user_id
group by user_id ) gcomments_3 on ( gcomments_3.user_id = usrs.id )

left outer join
(select a.user_id, count(1) as pcomment_user_response_to_others_comments
 from pcomments a
where reply_to > 0
and a.user_id between start_user_id and end_user_id
group by user_id ) pcomments_3 on (pcomments_3.user_id = usrs.id)

left outer join
  ( select a.user_id, count(1) as gcomments_replies_on_users_gallery_comments
    from gcomments a, gcomments b
    where a.reply_to = b.id
    and a.user_id between start_user_id and end_user_id
    group by a.user_id) gcomments_4 on (gcomments_4.user_id = usrs.id)

left outer join
(select a.user_id, count(1) as pcomments_replies_on_users_project_comments
from pcomments a , pcomments b
where a.reply_to = b.id
and a.user_id between start_user_id and end_user_id
group by a.user_id ) pcomments_4 on (pcomments_4.user_id = usrs.id)

left outer join
  (select user_id, count(1) as tag_count
   from taggers
   where user_id between start_user_id and end_user_id
   group by user_id) taggers_1 on (taggers_1.user_id = usrs.id)

left outer join
    (select c.id, count(1) as tags_on_user_project
     from taggers a, projects b, users c
     where b.user_id = c.id
       and a.user_id <> c.id
       and a.project_id = b.id
       and c.id between start_user_id and end_user_id
     group by c.id) taggers_2 on ( taggers_2.id = usrs.id)

left outer join
(select c.id, count(1) as downloads_by_user_count
from downloaders a, users b, projects c
where a.user_id = b.id
and a.project_id = c.id
and c.user_id <> b.id
and a.user_id between start_user_id and end_user_id
group by c.id) downloaders_1 on (downloaders_1.id = usrs.id)

left outer join
(select c.id, count(1) as downloads_of_users_projects_count
from downloaders a,projects b, users c
where  a.user_id <> b.user_id
and a.project_id = b.id
and a.user_id = c.id
and c.id between start_user_id and end_user_id
group by c.id) downloaders_2 on ( downloaders_2.id = usrs.id)



left outer join
(select user_id, sum(if(proj_visibility in ('censbyadmin','censbycomm'),1,0)) as censored_project_count,
                 sum(if(proj_visibility ='delbyadmin',1,0)) as deleted_by_admin_project_count,
                 sum(if(proj_visibility ='delbyusr',1,0)) as deleted_by_user_project_count
from projects a
where proj_visibility in ('censbyadmin','censbycomm','delbyadmin','delbyusr')
and a.user_id between start_user_id and end_user_id
group by a.user_id) projects_2 on (projects_2.user_id = usrs.id)


left outer join
(select c.id, sum(if(b.status = 'safe',1,0)) as safe_projects_of_user_flagged_inapp_by_others,
              sum(if(b.status = 'notsafe',1,0)) as not_safe_projects_of_user_flagged_inapp_by_others
from flaggers a, projects b, users c
where a.user_id <> c.id
and b.id = a.project_id
and   b.user_id = c.id
and b.status in ('safe','notsafe')
and c.id between start_user_id and end_user_id
group by c.id) flaggers_1 on ( flaggers_1.id = usrs.id )

left outer join
(select c.id,count(1) as feat_proj_count
from featured_projects a,projects b, users c
where b.user_id = c.id
and b.id = a.project_id
and c.id between start_user_id and end_user_id
group by c.id) feat_projects on (feat_projects.id = usrs.id)

left outer join
(select c.id, count(1) as feat_gal_count
from featured_galleries a, galleries b, users c
where b.user_id = c.id
and b.id = a.gallery_id
and c.id between start_user_id and end_user_id
group by c.id) feat_galleries on (feat_galleries.id = usrs.id)

left outer join
(select c.id, count(1) as loveit_on_others_projects_by_user
from lovers a, users c
where a.user_id = c.id
and not exists (select 1
                from projects b
                where b.id = a.project_id
                and   b.user_id = c.id)
and c.id between start_user_id and end_user_id
group by c.id) lovers on (lovers.id = usrs.id)

 left outer join
(select c.id, sum(if(b.user_id <> c.id,1,0)) as favorited_others_projects_count,
              sum(if(b.user_id = c.id,1,0)) as favorited_own_projects_count
from favorites a, projects b, users c
where a.user_id = c.id
and b.id = a.project_id
and c.id between start_user_id and end_user_id
group by c.id) favs_1 on (favs_1.id = usrs.id)

left outer join
(select c.id,count(1) as projects_flagged_inapp_by_user
from flaggers a, users c
where a.user_id = c.id
and not exists (select 1
                from projects b
                where b.id = a.project_id
                and   b.user_id = c.id)
and c.id between start_user_id and end_user_id
group by c.id) flaggers_3 on (flaggers_3.id = usrs.id)

left outer join
(select c.id, count(1) as user_projects_flagged_inapp_by_others
from flaggers a, users c
where a.user_id <> c.id
and exists (select 1
                from projects b
                where b.id = a.project_id
                and   b.user_id = c.id)
and c.id between start_user_id and end_user_id
group by c.id) flaggers_4 on ( flaggers_4.id = usrs.id)

left outer join
(select user_id, count(1) as pcomments_flagged_inapp_by_others
from pcomments
where comment_visibility in ('delbyadmin','censbyadmin','censbycomm')
and user_id between start_user_id and end_user_id
group by user_id ) pcomments_5 on ( pcomments_5.user_id = usrs.id)

left outer join
(select user_id, count(1) as gcomments_flagged_inapp_by_others
from gcomments
where comment_visibility in ('delbyadmin','censbyadmin','censbycomm')
and user_id between start_user_id and end_user_id
group by user_id ) gcomments_5 on ( gcomments_5.user_id = usrs.id)

left outer join
(select b.id, count(distinct project_id,user_id) as remixed_others_projects_count
from project_shares a,users b
where a.user_id = b.id
and related_user_id <> b.id
and project_id <> related_project_id
and related_project_id is not null
and b.id between start_user_id and end_user_id
group by b.id ) remix_1 on (remix_1.id = usrs.id)

left outer join
(select b.id, count(distinct project_id,user_id) as remixed_own_project_count
from project_shares a,users b
where a.user_id = b.id
and related_user_id = b.id
and project_id <> related_project_id
and related_project_id is not null
and b.id between start_user_id and end_user_id
group by b.id ) remix_2 on (remix_2.id = usrs.id)

left outer join
(select b.id, count(distinct project_id,user_id) as users_projects_remixed_by_others_count
from project_shares a, users b
where user_id <> b.id
and related_user_id = b.id
and project_id <> related_project_id
and related_project_id is not null
and b.id between start_user_id and end_user_id
group by b.id ) remix_3 on (remix_3.id = usrs.id)

left outer join
(select a.user_id,count(distinct a.project_id,a.user_id) as remix_projects_of_user_project_flagged_by_others
from flaggers a, (select b.project_id
                 from project_shares b, users d
                 where b.user_id <> d.id
                   and b.related_user_id = d.id
                   and project_id <> related_project_id
                   and related_project_id is not null) c
where a.project_id = c.project_id
and a.user_id between start_user_id and end_user_id
group by a.user_id ) remix_4 on (remix_4.user_id = usrs.id)
;
set start_user_id = end_user_id ;
set end_user_id   = start_user_id + 1000;
commit;
END WHILE;

select user_id  as 'User ID',
username as 'User Name',
byear as 'Birth Year',
gender as 'Gender',
bmonth as 'Birth Month',
prj_count as 'Project Count',
tot_scripts  as 'Total Scripts',
tot_sprites as 'Total Sprites',
projects_visited as 'Projects Visited',
gallery_count as 'Gallery Count',
user_comment_with_no_reply as "Users' comments with no replies",
user_comments_with_replies as "User's comments with replies",
user_response_to_others_comments as "User's response to other's comments",
comments_replies_on_users_comments as "Replies on user's comments",
tag_count as 'Tag count',
tags_on_user_project as 'Tags on users projects',
downloads_by_user_count as 'Projects downloaded by user',
downloads_of_users_projects_count as "Download of User's projects",
censored_project_count as "# Censored Project",
deleted_by_admin_project_count as "Projects deleted by Admin",
deleted_by_user_project_count as "Projects deleted by User",
safe_projects_of_user_flagged_inapp_by_others  as "Safe projects of user flagged inapp by others",
not_safe_projects_of_user_flagged_inapp_by_others as "Not safe projects of user flagged inapp by others",
feat_proj_count as "# Featured Project",
feat_gal_count as "# Featured Gallery",
total_proj_loveits as "# Project Loveits",
loveit_on_others_projects_by_user as "# Loveits on others projects by User",
favorited_others_projects_count as "#User Favorited Other's projects",
favorited_own_projects_count as "# User Favorited Own Project",
projects_flagged_inapp_by_user as "# Projects flagged inapp by User",
user_projects_flagged_inapp_by_others as "User's projects flagged inapp by others",
comments_flagged_inapp_by_others as "#Comments flagged inapp by others",
remixed_others_projects_count as "# User remixed others projects",
remixed_own_project_count as "# User remixed own project",
users_projects_remixed_by_others_count as "# user's projects remixed by others",
remix_projects_of_user_project_flagged_by_others as "# Remix of user's projects flagged inapp by others"
from aggregate_user_report_tmp;
END */;;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE*/;;
/*!50003 SET SESSION SQL_MODE=""*/;;
/*!50003 CREATE*/ /*!50020 DEFINER=`beta`@`localhost`*/ /*!50003 PROCEDURE `analysis_sp_remix_activity_in_period`(cutoff_date datetime, to_num_of_days int,
                                                      padding_days int)
begin


declare from_num_of_days int;

set from_num_of_days = 0 - to_num_of_days;


drop table if exists analysis_remix_activity_in_period;

create table
       analysis_remix_activity_in_period(
          user_id int not null,
          projects_before  int null,
          projects_after int null,
          remix_projects_before  int null,
          remix_projects_after int null,
          self_remix_projects_before  int null,
          self_remix_projects_after int null
          );

insert into analysis_remix_activity_in_period(user_id)
select distinct user_id
from projects a
where datediff(a.created, cutoff_date) between from_num_of_days and 0
   or  datediff(a.created, INTERVAL padding_days DAY + date(cutoff_date)) between 0 and to_num_of_days
;


select '' as 'Updating Total Projects Before and After.......';
update analysis_remix_activity_in_period c,
(select a.user_id, count(a.id) as projects_before
from projects a,(select distinct user_id from analysis_remix_activity_in_period) b
where a.user_id = b.user_id
and datediff(a.created, cutoff_date) between from_num_of_days and 0
group by a.user_id
) r
set c.projects_before = r.projects_before
where c.user_id = r.user_id
;



delete from analysis_remix_activity_in_period
where projects_before is NULL or projects_before = 0
;

update analysis_remix_activity_in_period c,
(select a.user_id, count(a.id) as projects_after
from projects a,(select distinct user_id from analysis_remix_activity_in_period) b
where a.user_id = b.user_id
and datediff(a.created, INTERVAL padding_days DAY + date(cutoff_date)) between 0 and to_num_of_days
group by a.user_id
) r
set c.projects_after = r.projects_after
where c.user_id = r.user_id
;

select '' as 'Updating Total Remix Projects Before and After.......';
update analysis_remix_activity_in_period c,
(select a.user_id, count(a.id) as remix_projects_before
from projects a,(select distinct user_id from analysis_remix_activity_in_period) b
where a.user_id = b.user_id
and datediff(a.created, cutoff_date) between from_num_of_days and 0
and based_on_pid is not null
group by a.user_id
) r
set c.remix_projects_before = r.remix_projects_before
where c.user_id = r.user_id
;



update analysis_remix_activity_in_period c,
(select a.user_id, count(a.id) as remix_projects_after
from projects a,(select distinct user_id from analysis_remix_activity_in_period) b
where a.user_id = b.user_id
and datediff(a.created, INTERVAL padding_days DAY + date(cutoff_date)) between 0 and to_num_of_days
and based_on_pid is not null
group by a.user_id
) r
set c.remix_projects_after = r.remix_projects_after
where c.user_id = r.user_id
;

select '' as 'Updating Total Self-remix Projects Before and After.......' ;

update analysis_remix_activity_in_period c,
(select a.user_id, count(a.id) as self_remix_projects_before
from projects a,(select distinct user_id from analysis_remix_activity_in_period) b
where a.user_id = b.user_id
and datediff(a.created, cutoff_date) between from_num_of_days and 0
and based_on_pid is not null
and exists
    (select 1 from projects p where p.id = a.based_on_pid and p.user_id = a.user_id)
group by a.user_id
) r
set c.self_remix_projects_before = r.self_remix_projects_before
where c.user_id = r.user_id
;



update analysis_remix_activity_in_period c,
(select a.user_id, count(a.id) as self_remix_projects_after
from projects a,(select distinct user_id from analysis_remix_activity_in_period) b
where a.user_id = b.user_id
and datediff(a.created, INTERVAL padding_days DAY + date(cutoff_date)) between 0 and to_num_of_days
and based_on_pid is not null
and exists
    (select 1 from projects p where p.id = a.based_on_pid and p.user_id = a.user_id)
group by a.user_id
) r
set c.self_remix_projects_after = r.self_remix_projects_after
where c.user_id = r.user_id
;



select * 
from analysis_remix_activity_in_period ;

end */;;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE*/;;
/*!50003 SET SESSION SQL_MODE=""*/;;
/*!50003 CREATE*/ /*!50020 DEFINER=`beta`@`scratchweb2.media.mit.edu`*/ /*!50003 PROCEDURE `latest1friendproject`(in user_id_param int(10))
begin
 declare  no_more int default 0;
 declare  project_id_list varchar(15000) default '0';
 declare sql_statement    varchar(15000);
  declare stmt varchar(15000);
 declare  cur_friend_id varchar(255);
 declare  cur_friend_project_id varchar(255);
  declare  cur_friend cursor for
       select  distinct friend_id
       from relationships
       where user_id = user_id_param;
declare  cur_friend_project cursor for
       select  id
       from projects
       where user_id = cur_friend_id
       and proj_visibility = 'visible'
	 and status != 'notsafe'
       order by created desc
       limit 1;
declare  continue handler for not found
       set  no_more = 1;
open cur_friend;
Loop1: loop
  fetch cur_friend into cur_friend_id;
       if no_more then
        close cur_friend;
        leave LOOP1;
       end if;
       open cur_friend_project;
      LOOP2: loop
          fetch cur_friend_project into cur_friend_project_id;
          if no_more then
             set no_more = 0;
             close  cur_friend_project;
             leave LOOP2;
          end if;
          set project_id_list = concat(project_id_list,',',cur_friend_project_id);
      end loop LOOP2;
end loop LOOP1;
set @sql_statement =
 CONCAT('select ',user_id_param,' as user_id, d.username ,a.user_id as friend_id, b.username, a.id, a.name, a.created
  from projects a, users b,(select username from users where id = ',user_id_param,') d
where a.user_id = b.id
and   a.id in (', project_id_list,') order by a.created desc, user_id, friend_id');
prepare stmt from  @sql_statement;
execute stmt;
deallocate prepare stmt;
END */;;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE*/;;
/*!50003 SET SESSION SQL_MODE=""*/;;
/*!50003 CREATE*/ /*!50020 DEFINER=`anant`@`scratchweb1.media.mit.edu`*/ /*!50003 PROCEDURE `remixchainflags`()
BEGIN
DECLARE no_more_projects               int default 0;
DECLARE curProjectID, curUserId,curBasedOnPID,curBasedonPIDParent,AlreadyPresent  int(1) default NULL;
DECLARE curMinViewDateTime datetime default NULL;
DECLARE cur_projects CURSOR FOR
select distinct project_id
from analysis_view_stats
order by project_id;
DECLARE  CONTINUE HANDLER FOR NOT FOUND
SET  no_more_projects = 1;
DROP TABLE IF EXISTS analysis_project_user_view;
create  table analysis_project_user_view
        ( project_id int, user_id int, viewedYN char(1) null,
          minViewTime datetime null);

DROP TABLE IF EXISTS analysis_remixes_viewed_by_originators;
create table analysis_remixes_viewed_by_originators(
project_id int null,
user_id int null,
based_on_pid int null,
flag_id int null,
reasons   varchar(500) null,
flag_timestamp datetime null,
loveit_id int null,
loveit_timestamp datetime null,
favorite_id int null,
favorite_timestamp datetime null,
num_of_tags int null,
tag_timestamp datetime null,
concat_tags varchar(1000) null,
num_of_comments int null,
comment_timestamp datetime null,
concat_comment varchar(1000) null,
view_timestamp datetime null
)
;
select 'Creating remix views chain.........' as '';
OPEN cur_projects;
FETCH cur_projects into curProjectID;
REPEAT
        set  no_more_projects = 0;
        set  AlreadyPresent = 0;
        set curBasedOnPID = NULL;
        set curBasedonPIDParent = NULL;
        set curMinViewDateTime = NULL;
        select user_id, based_on_pid
        INTO curUserID, curBasedOnPID
        from projects
        where id = curProjectID
        ;
        IF curBasedOnPID is not null THEN
              INSERT INTO analysis_project_user_view
              values(curProjectID, curUserId, 'N',NULL);
    parentLoop: while (curBasedOnPID is not null)
    DO
        set curMinViewDateTime = NULL;
        set curBasedOnPIDParent = NULL;
        set curUserID = NULL;
        SELECT user_id, based_on_pid
          INTO curUserID, curBasedOnPIDParent
          FROM projects a
         WHERE id = curBasedonPID
           and id <> ifnull(based_on_pid,0)
         ;
        select distinct 1
        INTO  AlreadyPresent
        from  analysis_project_user_view
        where project_id = curProjectID
          and user_id = curUserID;
         if AlreadyPresent = 1 THEN
           leave parentLoop;
         end if;
            IF curUserID is not null THEN
                INSERT INTO analysis_project_user_view
                values(curProjectID, curUserId, 'N', NULL);
            END IF;
      set curBasedOnPID = curBasedonPIDParent;
        set curMinViewDateTime = NULL;
        select min(timestamp)
        INTO curMinViewDateTime
        from analysis_view_stats  a
        where a.project_id = curProjectID
        and a.user_id = curUserID;
        IF curMinViewDateTime is not NULL THEN
                update analysis_project_user_view a
                set viewedYN = 'Y', minviewtime = curMinViewDateTime
                where a.project_id = curProjectID and a.user_id = curUserID
                ;
        END IF;
    end while ;
          DELETE from analysis_project_user_view
          where viewedYN = 'N'
          ;
 END IF;
set  no_more_projects = 0;
set curBasedOnPID = 0;
set curBasedonPIDParent = 0;
 FETCH  cur_projects INTO curProjectID;
 UNTIL  no_more_projects = 1
END REPEAT ;

INSERT INTO analysis_remixes_viewed_by_originators(
            user_id, project_id, based_on_pid,
            view_timestamp)
select distinct
       a.user_id,a.project_id,
       p.based_on_pid,
       ifnull(a.minviewtime,' ')
from analysis_project_user_view a,
     projects  p
where (a.project_id = p.id)
;
select 'Update Flags data.........' as '';

UPDATE analysis_remixes_viewed_by_originators o,
       analysis_project_user_view a,
       flaggers f
SET   o.flag_id = f.id,
      o.reasons = f.reasons,
      o.flag_timestamp = f.timestamp
where o.project_id = a.project_id
and o.user_id = a.user_id
and o.project_id = f.project_id
and o.user_id = f.user_id
and f.project_id = a.project_id
and f.user_id = a.user_id
;
select 'Update Loveits data.........' as '';

UPDATE analysis_remixes_viewed_by_originators o,
       analysis_project_user_view a,
       lovers f
SET   o.loveit_id = f.id,
      o.loveit_timestamp = f.timestamp
where o.project_id = a.project_id
and o.user_id = a.user_id
and o.project_id = f.project_id
and o.user_id = f.user_id
and f.project_id = a.project_id
and f.user_id = a.user_id
;
select 'Update Favorites data.........' as '';

UPDATE analysis_remixes_viewed_by_originators o,
       analysis_project_user_view a,
       favorites f
SET   o.favorite_id = f.id,
      o.favorite_timestamp = f.timestamp
where o.project_id = a.project_id
and o.user_id = a.user_id
and o.project_id = f.project_id
and o.user_id = f.user_id
and f.project_id = a.project_id
and f.user_id = a.user_id
;
select 'Update Tags data.........' as '';

UPDATE analysis_remixes_viewed_by_originators o,
       (select distinct
               a.user_id,a.project_id,
               count(distinct f.id) as tag_count,
               group_concat(ts.name separator '; ') as concat_tags,
               min(f.timestamp) as tagger_timestamp
          from analysis_project_user_view a,
               taggers f, tags ts
          where a.project_id = f.project_id
            and a.user_id = f.user_id
            and ts.id = f.tag_id
       group by a.project_id , a.user_id) t
SET   o.num_of_tags = t.tag_count,
      o.tag_timestamp = t.tagger_timestamp,
      o.concat_tags   = t.concat_tags
where o.project_id = t.project_id
and o.user_id = t.user_id
;
select 'Update Comments data.........' as '';

UPDATE analysis_remixes_viewed_by_originators o,
       (select distinct
               a.user_id,a.project_id,
               count(pc.id) as comment_count,
               min(pc.timestamp) as comment_timestamp,
               group_concat(content separator ' ; ') as concat_comment
          from analysis_project_user_view a,
               pcomments pc
          where a.project_id = pc.project_id
            and a.user_id = pc.user_id
       group by a.project_id , a.user_id) c
SET   o.num_of_comments  = c.comment_count,
      o.comment_timestamp = c.comment_timestamp,
      o.concat_comment = c.concat_comment
where o.project_id = c.project_id
and o.user_id = c.user_id
;
END */;;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE*/;;
/*!50003 SET SESSION SQL_MODE=""*/;;
/*!50003 CREATE*/ /*!50020 DEFINER=`beta`@`scratchweb2.media.mit.edu`*/ /*!50003 PROCEDURE `top3friendproject`(in user_id_param int(10))
begin
 declare  no_more_friends int default 0;
 declare  cur_friend_id varchar(255);
 declare  cur_friend cursor for
       select  distinct friend_id
       from relationships
       where user_id = user_id_param;
 declare  continue handler for not found
 set  no_more_friends = 1;
drop temporary table if exists tmp_friendprojects;
create temporary table tmp_friendprojects
( user_id       int(10),
  friend_id int(10),
  project_id int(10));
open cur_friend;
repeat
  fetch cur_friend into cur_friend_id;
  insert into tmp_friendprojects
  select distinct user_id_param,cur_friend_id, id
   from projects
   where user_id = cur_friend_id
   order by created desc
   limit 3;
until no_more_friends end REPEAT ;
close  cur_friend;
select distinct a.user_id,b.username,a.friend_id,c.username,a.project_id, d.name
from tmp_friendprojects a, users b, users c, projects d
where b.id = a.user_id
and   c.id = a.friend_id
and   d.id = a.project_id;

drop table  tmp_friendprojects;
end */;;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE*/;;
/*!50003 SET SESSION SQL_MODE=""*/;;
/*!50003 CREATE*/ /*!50020 DEFINER=`anant`@`scratchweb1.media.mit.edu`*/ /*!50003 PROCEDURE `UserLevelReport`()
BEGIN
DECLARE total_users int(10) default 0;
DECLARE
	start_user_id,	end_user_id INT(10);

set start_user_id = 1;
set end_user_id   = start_user_id + 1000;

drop table if exists user_level_report_tmp ;
create table user_level_report_tmp
(user_id                            int(10),
username                            varchar(45),
gender                              varchar(35),
byear                               varchar(45),
bmonth                              varchar(45),
prj_count                           int(10),
inactive_prj_count                  int(10),
orig_prj_count                      int(10),
remixed_others_projects_count       int(10),
projects_visited                    int(10),
downloads_by_user_count             int(10),
favorited_others_projects_count     int(10),
friends_added_by_user               int(10),
gallery_count                       int(10),
comments_posted_by_user             int(10),
loveit_on_others_projects_by_user   int(10),
tags_posted_by_user                   int(10),
avg_diff_cdates                     int(10),
user_added_as_friend_by_others      int(10),
avg_views_on_users_prjs_by_others   int(10),
avg_prj_users_favorited_by_others   int(10),
avg_comments_on_users_prj_gals      int(10),
avg_proj_loveits                  int(10),
avg_downloads_of_users_projects   int(10),
avg_users_projects_remixed_by_others int(10),
avg_scripts                          int(10),
avg_sprites                          int(10),
avg_tags_on_users_project             int(10));


select max(id) into total_users from users;

WHILE start_user_id <= total_users DO
INSERT INTO user_level_report_tmp
select
usrs.id,
usrs.username,
usrs.gender,
usrs.byear,
usrs.bmonth,
        prjs.prj_count,prjs.inactive_prj_count, 
	(prjs.prj_count - remix_1.remixed_others_projects_count),
        remix_1.remixed_others_projects_count,
        view_stats_1.projects_visited ,
        downloaders_1.downloads_by_user,
        favs_1.favorited_others_projects_count,
        friends_1.friends_added_by_user,
        galleries.gallery_count,
        (user_pcomments + user_gcomments),
        lovers.loveit_on_others_projects_by_user,
        taggers_1.users_project_tags,
        (datediff(prjs.max_created,prjs.min_created)/prjs.prj_count),
        friends_2.user_added_as_friend,
        (view_stats_2.users_projects_viewed_by_others /prjs.prj_count),
        (user_projects_favorited/prjs.prj_count),
        ((pcomments_on_users_projects+gcomments_on_users_galleries)/prjs.prj_count),
        (prjs.total_proj_loveits/prjs.prj_count),
        (downloaders_2.downloads_of_users_projects/prjs.prj_count),
        (remix_3.users_projects_remixed_by_others_count/prjs.prj_count),
        (prjs.tot_scripts/prjs.prj_count),
        (prjs.tot_sprites/prjs.prj_count),
        (taggers_3.tags_on_users_projects/prjs.prj_count)


from
    (select id,username, gender,byear,bmonth
     from users a
     where a.id between start_user_id and end_user_id) usrs


left outer join
     (select user_id,
             sum(if(proj_visibility in('delbyusr','delbyadmin'),0,1)) as prj_count,
             sum(if(proj_visibility in('delbyusr','delbyadmin'),1,0)) as inactive_prj_count,
	     sum(totalScripts) as tot_scripts,
             sum(numberOfSprites) as tot_sprites,
             sum(loveit) as total_proj_loveits,
             max(created) as max_created,min(created) as min_created
      from projects
      where user_id between start_user_id and end_user_id
      group by user_id) prjs on ( prjs.user_id = usrs.id)

left outer join
      (select user_id, count(1) as projects_visited
        from view_stats a
        where project_id not in ( select id
                        from projects
                        where user_id = a.user_id)
        and a.user_id between start_user_id and end_user_id
        group by user_id ) view_stats_1 on (view_stats_1.user_id = usrs.id)

left outer join
      (select b.user_id, count(1) as users_projects_viewed_by_others
        from view_stats a, projects b
        where b.user_id between start_user_id and end_user_id
          and a.project_id = b.id
        group by b.user_id ) view_stats_2 on (view_stats_2.user_id = usrs.id)



left outer join
     (select user_id,count(1) as friends_added_by_user
      from relationships
      where user_id between start_user_id and end_user_id
      group by user_id) friends_1 on ( friends_1.user_id = usrs.id)

left outer join
     (select friend_id,count(1) as user_added_as_friend
      from relationships
      where friend_id between start_user_id and end_user_id
      group by friend_id) friends_2 on ( friends_2.friend_id = usrs.id)

left outer join
(select c.id, count(1) as downloads_by_user
from downloaders a, users b, projects c
where a.user_id = b.id
and a.project_id = c.id
and c.user_id <> b.id
and a.user_id between start_user_id and end_user_id
group by c.id) downloaders_1 on (downloaders_1.id = usrs.id)

left outer join
(select c.id, count(1) as downloads_of_users_projects
from downloaders a,projects b, users c
where  a.user_id <> b.user_id
and a.project_id = b.id
and a.user_id = c.id
and c.id between start_user_id and end_user_id
group by c.id) downloaders_2 on ( downloaders_2.id = usrs.id)

left outer join
  (SELECT p.user_id,count(1) as users_project_tags
   FROM project_tags p, users b
   where p.user_id = b.id
     and b.id between start_user_id and end_user_id
   group by b.id) taggers_1 on (taggers_1.user_id = usrs.id)


left outer join
    (SELECT b.user_id,count(1) as tags_on_users_projects
      FROM  project_tags p, projects b
      where p.project_id = b.id
      and   b.user_id between start_user_id and end_user_id
      group by b.user_id) taggers_3 on ( taggers_3.user_id = usrs.id)




left outer join
(select c.id, sum(if(b.user_id <> c.id,1,0)) as favorited_others_projects_count
from favorites a, projects b, users c
where a.user_id = c.id
and b.id = a.project_id
and c.id between start_user_id and end_user_id
group by c.id) favs_1 on (favs_1.id = usrs.id)

left outer join
(select c.id, sum(if(a.user_id <> c.id,1,0)) as user_projects_favorited
from favorites a, projects b, users c
where b.user_id = c.id
and b.id = a.project_id
and c.id between start_user_id and end_user_id
group by c.id) favs_2 on (favs_2.id = usrs.id)




left outer join
     (select user_id,count(1) as gallery_count
      from galleries
      where user_id between start_user_id and end_user_id
      group by user_id) galleries on (galleries.user_id = usrs.id)

left outer join
(select c.id, count(1) as loveit_on_others_projects_by_user
from lovers a, users c
where a.user_id = c.id
and not exists (select 1
                from projects b
                where b.id = a.project_id
                and   b.user_id = c.id)
and c.id between start_user_id and end_user_id
group by c.id) lovers on (lovers.id = usrs.id)

left outer join
(select b.id, count(distinct project_id,user_id) as remixed_others_projects_count
from project_shares a,users b
where a.user_id = b.id
and related_user_id <> b.id
and project_id <> related_project_id
and related_project_id is not null
and b.id between start_user_id and end_user_id
group by b.id ) remix_1 on (remix_1.id = usrs.id)


left outer join
(select b.id, count(distinct project_id,user_id) as users_projects_remixed_by_others_count
from project_shares a, users b
where user_id <> b.id
and related_user_id = b.id
and project_id <> related_project_id
and related_project_id is not null
and b.id between start_user_id and end_user_id
group by b.id ) remix_3 on (remix_3.id = usrs.id)




left outer join
( select user_id,count(1) as user_pcomments
  from pcomments a
  where a.user_id between start_user_id and end_user_id
  group by user_id)  pcomments_1 on (pcomments_1.user_id = usrs.id)


left outer join
( select user_id,count(1) as user_gcomments
  from gcomments a
  where a.user_id between start_user_id and end_user_id
  group by user_id)  gcomments_1 on (gcomments_1.user_id = usrs.id)


left outer join
( select b.user_id,count(1) as pcomments_on_users_projects
  from pcomments a, projects b
  where a.project_id = b.id
  and   b.user_id between start_user_id and end_user_id
  group by b.user_id)  pcomments_2 on (pcomments_2.user_id = usrs.id)


left outer join
( select b.user_id,count(1) as gcomments_on_users_galleries
  from gcomments a, galleries b
  where a.gallery_id = b.id
  and   b.user_id between start_user_id and end_user_id
  group by b.user_id)  gcomments_2 on (gcomments_2.user_id = usrs.id)

;

set start_user_id = end_user_id ;
set end_user_id   = start_user_id + 1000;
commit;
END WHILE;

select user_id  as 'User_ID',
username as 'User_Name',
byear as 'Birth_Year',
gender as 'Gender',
bmonth as 'Birth_Month',
prj_count as 'Project_Count',
inactive_prj_count as 'Inactive_Project_Count',
orig_prj_count as 'Original_Project_Count',
remixed_others_projects_count as 'Remixed_Project_Count',
projects_visited as 'Projects_Visited_by_User',
downloads_by_user_count as 'Projects_downloaded_by_user',
favorited_others_projects_count as "#User_Favorited_Other's_projects",
friends_added_by_user as 'Friends_added_by_User',
gallery_count as 'Gallery_Count',
comments_posted_by_user as 'Comments_posted_by_user',
loveit_on_others_projects_by_user as "# Loveits_on_others_projects_by_User",
tags_posted_by_user as 'Project_Tags_posted_by_user',
avg_diff_cdates as 'Avg_diff_between_Project_creation',
user_added_as_friend_by_others as 'User_added_as_friend_by_others',
avg_views_on_users_prjs_by_others   as "Avg_views_of_users_projects",
avg_prj_users_favorited_by_others   as "Avg_users_projects_favorited_by_others",
avg_comments_on_users_prj_gals      as "Avg_comments_on_users_projects/galleries",
avg_proj_loveits                  as "Avg_loveit_on_users_projects",
avg_downloads_of_users_projects   as "Avg_Downloads_of_users_projects",
avg_users_projects_remixed_by_others as "Avg_user_projects_remixed_by_others",
avg_scripts                          as "Avg_Scripts",
avg_sprites                      as "Avg_Sprites",
avg_tags_on_users_project  as "Avg_tags_on_user's_projects"
from user_level_report_tmp;
END */;;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE*/;;
/*!50003 SET SESSION SQL_MODE=""*/;;
/*!50003 CREATE*/ /*!50020 DEFINER=`anant`@`scratchweb1.media.mit.edu`*/ /*!50003 PROCEDURE `UserLevelReportArchive`()
BEGIN
DECLARE total_users int(10) default 0;
DECLARE
	start_user_id,	end_user_id INT(10);

set start_user_id = 0;
set end_user_id   = start_user_id + 1000;

drop table if exists user_level_report_archive_tmp ;
create table user_level_report_archive_tmp
(user_id                            int(10),
username                            varchar(45),
gender                              varchar(35),
byear                               varchar(45),
bmonth                              varchar(45),
prj_count                           int(10),
inactive_prj_count                  int(10),
orig_prj_count                      int(10),
remixed_others_projects_count       int(10),
projects_visited                    int(10),
downloads_by_user_count             int(10),
favorited_others_projects_count     int(10),
friends_added_by_user               int(10),
gallery_count                       int(10),
comments_posted_by_user             int(10),
loveit_on_others_projects_by_user   int(10),
tags_posted_by_user                   int(10),
avg_diff_cdates                     int(10),
user_added_as_friend_by_others      int(10),
avg_views_on_users_prjs_by_others   int(10),
avg_prj_users_favorited_by_others   int(10),
avg_comments_on_users_prj_gals      int(10),
avg_proj_loveits                  int(10),
avg_downloads_of_users_projects   int(10),
avg_users_projects_remixed_by_others int(10),
avg_scripts                          int(10),
avg_sprites                          int(10),
avg_tags_on_users_project             int(10));


select max(id) into total_users from users;

WHILE start_user_id <= total_users DO
INSERT INTO user_level_report_archive_tmp
select
usrs.id,
usrs.username,
usrs.gender,
usrs.byear,
usrs.bmonth,
        prjs.prj_count,prjs.inactive_prj_count, 
	(prjs.prj_count - remix_1.remixed_others_projects_count),
        remix_1.remixed_others_projects_count,
        (view_stats_1.projects_visited +  view_stats_3.projects_visited_20081211) ,
        downloaders_1.downloads_by_user,
        favs_1.favorited_others_projects_count,
        friends_1.friends_added_by_user,
        galleries.gallery_count,
        (user_pcomments + user_gcomments),
        lovers.loveit_on_others_projects_by_user,
        taggers_1.users_project_tags,
        (datediff(prjs.max_created, prjs.min_created)/prjs.prj_count),
        friends_2.user_added_as_friend,
        ((view_stats_2.users_projects_viewed_by_others + view_stats_4.users_projects_viewed_by_others_20081211)/prjs.prj_count),
        (user_projects_favorited/prjs.prj_count),
        ((pcomments_on_users_projects+gcomments_on_users_galleries)/prjs.prj_count),
        (prjs.total_proj_loveits/prjs.prj_count),
        (downloaders_2.downloads_of_users_projects/prjs.prj_count),
        (remix_3.users_projects_remixed_by_others_count/prjs.prj_count),
        (prjs.tot_scripts/prjs.prj_count),
        (prjs.tot_sprites/prjs.prj_count),
        (taggers_3.tags_on_users_projects/prjs.prj_count)


from
    (select id,username, gender,byear,bmonth
     from users a
     where a.id between start_user_id and end_user_id) usrs


left outer join
     (select user_id,
             sum(if(proj_visibility in('delbyusr','delbyadmin'),0,1)) as prj_count,
             sum(if(proj_visibility in('delbyusr','delbyadmin'),1,0)) as inactive_prj_count,
	     sum(totalScripts) as tot_scripts,
             sum(numberOfSprites) as tot_sprites,
             sum(loveit) as total_proj_loveits,
             max(created) as max_created,min(created) as min_created
      from projects
      where user_id between start_user_id and end_user_id
      group by user_id) prjs on ( prjs.user_id = usrs.id)

left outer join
      (select user_id, count(1) as projects_visited
        from view_stats a
        where project_id not in ( select id
                        from projects
                        where user_id = a.user_id)
        and a.user_id between start_user_id and end_user_id
        group by user_id ) view_stats_1 on (view_stats_1.user_id = usrs.id)

left outer join
      (select b.user_id, count(1) as users_projects_viewed_by_others
        from view_stats a, projects b
        where b.user_id between start_user_id and end_user_id
          and a.project_id = b.id
        group by b.user_id ) view_stats_2 on (view_stats_2.user_id = usrs.id)

left outer join
      (select user_id, count(1) as projects_visited_20081211
        from view_stats_20081211 a
        where project_id not in ( select id
                        from projects
                        where user_id = a.user_id)
        and a.user_id between start_user_id and end_user_id
        group by user_id ) view_stats_3 on (view_stats_3.user_id = usrs.id)

left outer join
      (
	select  prj_4.user_id, count(*) as users_projects_viewed_by_others_20081211
	from view_stats_20081211 a
	join ( select id, user_id
       		from projects b
       		where b.user_id between start_user_id and end_user_id) 
					prj_4 on (a.project_id = prj_4.id)
	group by prj_4.user_id) view_stats_4 on (view_stats_4.user_id = usrs.id)


left outer join
     (select user_id,count(1) as friends_added_by_user
      from relationships
      where user_id between start_user_id and end_user_id
      group by user_id) friends_1 on ( friends_1.user_id = usrs.id)

left outer join
     (select friend_id,count(1) as user_added_as_friend
      from relationships
      where friend_id between start_user_id and end_user_id
      group by friend_id) friends_2 on ( friends_2.friend_id = usrs.id)

left outer join
(select c.id, count(1) as downloads_by_user
from downloaders a, users b, projects c
where a.user_id = b.id
and a.project_id = c.id
and c.user_id <> b.id
and a.user_id between start_user_id and end_user_id
group by c.id) downloaders_1 on (downloaders_1.id = usrs.id)

left outer join
(select c.id, count(1) as downloads_of_users_projects
from downloaders a,projects b, users c
where  a.user_id <> b.user_id
and a.project_id = b.id
and a.user_id = c.id
and c.id between start_user_id and end_user_id
group by c.id) downloaders_2 on ( downloaders_2.id = usrs.id)

left outer join
  (SELECT p.user_id,count(1) as users_project_tags
   FROM project_tags p, users b
   where p.user_id = b.id
     and b.id between start_user_id and end_user_id
   group by b.id) taggers_1 on (taggers_1.user_id = usrs.id)


left outer join
    (SELECT b.user_id,count(1) as tags_on_users_projects
      FROM  project_tags p, projects b
      where p.project_id = b.id
      and   b.user_id between start_user_id and end_user_id
      group by b.user_id) taggers_3 on ( taggers_3.user_id = usrs.id)




left outer join
(select c.id, sum(if(b.user_id <> c.id,1,0)) as favorited_others_projects_count
from favorites a, projects b, users c
where a.user_id = c.id
and b.id = a.project_id
and c.id between start_user_id and end_user_id
group by c.id) favs_1 on (favs_1.id = usrs.id)

left outer join
(select c.id, sum(if(a.user_id <> c.id,1,0)) as user_projects_favorited
from favorites a, projects b, users c
where b.user_id = c.id
and b.id = a.project_id
and c.id between start_user_id and end_user_id
group by c.id) favs_2 on (favs_2.id = usrs.id)




left outer join
     (select user_id,count(1) as gallery_count
      from galleries
      where user_id between start_user_id and end_user_id
      group by user_id) galleries on (galleries.user_id = usrs.id)

left outer join
(select c.id, count(1) as loveit_on_others_projects_by_user
from lovers a, users c
where a.user_id = c.id
and not exists (select 1
                from projects b
                where b.id = a.project_id
                and   b.user_id = c.id)
and c.id between start_user_id and end_user_id
group by c.id) lovers on (lovers.id = usrs.id)

left outer join
(select b.id, count(distinct project_id,user_id) as remixed_others_projects_count
from project_shares a,users b
where a.user_id = b.id
and related_user_id <> b.id
and project_id <> related_project_id
and related_project_id is not null
and b.id between start_user_id and end_user_id
group by b.id ) remix_1 on (remix_1.id = usrs.id)


left outer join
(select b.id, count(distinct project_id,user_id) as users_projects_remixed_by_others_count
from project_shares a, users b
where user_id <> b.id
and related_user_id = b.id
and project_id <> related_project_id
and related_project_id is not null
and b.id between start_user_id and end_user_id
group by b.id ) remix_3 on (remix_3.id = usrs.id)




left outer join
( select user_id,count(1) as user_pcomments
  from pcomments a
  where a.user_id between start_user_id and end_user_id
  group by user_id)  pcomments_1 on (pcomments_1.user_id = usrs.id)


left outer join
( select user_id,count(1) as user_gcomments
  from gcomments a
  where a.user_id between start_user_id and end_user_id
  group by user_id)  gcomments_1 on (gcomments_1.user_id = usrs.id)


left outer join
( select b.user_id,count(1) as pcomments_on_users_projects
  from pcomments a, projects b
  where a.project_id = b.id
  and   b.user_id between start_user_id and end_user_id
  group by b.user_id)  pcomments_2 on (pcomments_2.user_id = usrs.id)


left outer join
( select b.user_id,count(1) as gcomments_on_users_galleries
  from gcomments a, galleries b
  where a.gallery_id = b.id
  and   b.user_id between start_user_id and end_user_id
  group by b.user_id)  gcomments_2 on (gcomments_2.user_id = usrs.id)

;

set start_user_id = end_user_id + 1;
set end_user_id   = start_user_id + 1000;
commit;
END WHILE;

select user_id  as 'User_ID',
username as 'User_Name',
byear as 'Birth_Year',
gender as 'Gender',
bmonth as 'Birth_Month',
prj_count as 'Project_Count',
inactive_prj_count as 'Inactive_Project_Count',
orig_prj_count as 'Original_Project_Count',
remixed_others_projects_count as 'Remixed_Project_Count',
projects_visited as 'Projects_Visited_by_User',
downloads_by_user_count as 'Projects_downloaded_by_user',
favorited_others_projects_count as "#User_Favorited_Other's_projects",
friends_added_by_user as 'Friends_added_by_User',
gallery_count as 'Gallery_Count',
comments_posted_by_user as 'Comments_posted_by_user',
loveit_on_others_projects_by_user as "# Loveits_on_others_projects_by_User",
tags_posted_by_user as 'Project_Tags_posted_by_user',
avg_diff_cdates as 'Avg_diff_between_Project_creation',
user_added_as_friend_by_others as 'User_added_as_friend_by_others',
avg_views_on_users_prjs_by_others   as "Avg_views_of_users_projects",
avg_prj_users_favorited_by_others   as "Avg_users_projects_favorited_by_others",
avg_comments_on_users_prj_gals      as "Avg_comments_on_users_projects/galleries",
avg_proj_loveits                  as "Avg_loveit_on_users_projects",
avg_downloads_of_users_projects   as "Avg_Downloads_of_users_projects",
avg_users_projects_remixed_by_others as "Avg_user_projects_remixed_by_others",
avg_scripts                          as "Avg_Scripts",
avg_sprites                      as "Avg_Sprites",
avg_tags_on_users_project  as "Avg_tags_on_user's_projects"
from user_level_report_archive_tmp;
END */;;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE*/;;
/*!50003 SET SESSION SQL_MODE=""*/;;
/*!50003 CREATE*/ /*!50020 DEFINER=`anant`@`scratchweb1.media.mit.edu`*/ /*!50003 PROCEDURE `UserProjectTotals`()
BEGIN
DECLARE total_users int(10) default 0;
DECLARE
	start_user_id,	end_user_id INT(10);

set start_user_id = 1;
set end_user_id   = start_user_id + 1000;

drop table if exists user_project_totals_tmp ;
create table user_project_totals_tmp
(user_id                             int(10),
username                            varchar(45),
byear                                varchar(45),
bmonth        varchar(45),
num_friends    int(10),
num_favorites    int(10),
num_galleries    int(10),
num_comments_created    int(10),
prj_id      int(10),
prj_name    varchar(100),
prj_created    timestamp,
prj_num_favoriters    int(10),
prj_total_scripts    int(10),
prj_num_sprites    int(10),
prj_remix_count    int(10),
prj_comment_count    int(10)
);

select max(id) into total_users from users;

WHILE start_user_id <= total_users DO
INSERT INTO user_project_totals_tmp
select usrs.id, usrs.username, usrs.byear, usrs.bmonth,
       fr.num_friends,fav.num_favorites,
       gal.num_galleries,
       pcomments.pcomments_count+gcomments.gcomments_count,
       prj_details.id,prj_details.name,prj_details.created,prj_details.num_favoriters,prj_details.totalScripts,
       prj_details.numberOfSprites,prj_details.remix_count, prj_details.comment_count
from
	(select a.id,username,gender, byear,bmonth
		 from users a
    where a.id between start_user_id and end_user_id
		) usrs  
left outer join	(select a.id, count(b.status) as num_friends
		  from users a
		  left outer join relationships b on ((a.id = b.user_id or a.id = b.friend_id) )
    where a.id between start_user_id and end_user_id
      group by a.id  ) fr on (usrs.id = fr.id) 
left outer join	(select a.id, count(c.project_id) as num_favorites
		  from users a
		  left outer join favorites c on (a.id = c.user_id)
    where a.id between start_user_id and end_user_id
		  group by a.id) fav on (usrs.id = fav.id) 
left outer join	(select a.id,count(g.id) as num_galleries
		  from users a
		  left outer join galleries g on (a.id = g.user_id)
    where a.id between start_user_id and end_user_id
    group by a.id) gal on (usrs.id = gal.id ) 
left outer join	(select a.id,count(p.id) as pcomments_count
		  from users a
		  left outer join pcomments p on (a.id = p.user_id)
    where a.id between start_user_id and end_user_id
    group by a.id) pcomments on (usrs.id = pcomments.id) 
left outer join	(select a.id,count(gc.id) as gcomments_count
		  from users a
		  left outer join gcomments gc on (a.id = gc.user_id)
    where a.id between start_user_id and end_user_id
		  group by a.id) gcomments on (usrs.id = gcomments.id) 
left outer join
     (select prj.id,name,prj.user_id,created,num_favoriters,totalScripts,
	       numberOfSprites,remix.remix_count, comment_count 
		  from projects prj left outer join
					                          ( select a.id,count(b.related_project_id) as remix_count
                                        from projects a left outer join project_shares b on (a.id = b.related_project_id)
						                            where b.project_id  <> b.related_project_id
						                            and b.related_project_id is not null
						                            and a.user_id between start_user_id and end_user_id
                                        and b.user_id between start_user_id and end_user_id
                                        group by a.id) remix
						                            on ( prj.id = remix.id)
			                  left outer join
                                    ( select a.id,count(b.project_id) as comment_count
							                          from projects a left outer join pcomments b on (a.id = b.project_id)
                                        where a.user_id between start_user_id and end_user_id
                                        and b.user_id between start_user_id and end_user_id
							                          group by a.id) comments
						                            on ( prj.id = comments.id)
      where prj.user_id between start_user_id and end_user_id
   ) prj_details on (usrs.id = prj_details.user_id );
set start_user_id = end_user_id ;
set end_user_id   = start_user_id + 1000;
commit;
END WHILE;

select user_id  as 'User ID',
username as 'User Name',
byear as 'Birth Year',
bmonth as 'Birth Month',
num_friends  as '# Friends',
num_favorites  as '# Favorites',
num_galleries  as '# Galleries',
num_comments_created  as '# Comments created',
prj_id  as 'Project ID',
prj_name  as 'Project Name',
prj_created as 'Project Created Date',
prj_num_favoriters  as '# Project Favoriters',
prj_total_scripts  as '# Total Scripts',
prj_num_sprites  as '# Sprites',
prj_remix_count  as 'Remix Count',
prj_comment_count as '# Comments on Project'
from user_project_totals_tmp;
END */;;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE*/;;
DELIMITER ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-03-01 17:44:01
