--
-- Dumping routines for database 'scratchdb'
--
drop PROCEDURE if exists latest3friendproject;
delimiter //

CREATE PROCEDURE `latest3friendproject`(in user_id_param int(10))
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
       order by created desc
       limit 3;
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
and   a.id in (', project_id_list,') order by user_id, friend_id, a.created desc');

prepare stmt from  @sql_statement;
execute stmt;
deallocate prepare stmt;
END //