--
-- Dumping routines for database 'scratchdb'
--
delimiter %%
drop procedure if exists top3friendproject %%
create procedure top3friendproject(in user_id_param int(10))
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
/*select distinct a.user_id,b.username,a.friend_id,c.username,a.project_id, d.name
from tmp_friendprojects a
     join ( select id, username from users) b on (b.id = a.user_id)
     join ( select id, username from users) c on (c.id = a.friend_id)
     join ( select id, name from projects) d on (d.id = a.project_id)
;*/
drop table  tmp_friendprojects;
end %%
delimiter ;