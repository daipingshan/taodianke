create trigger insertOrderRate
AFTER INSERT ON `ytt_order`
FOR EACH ROW
BEGIN
declare pay_status char(10);
declare db_pid char(40);
declare db_parent_pid char(40);
declare db_parents_pid char(40);
declare user_id int(11);
declare parent_id int(11);
declare parents_id int(11);
declare rate float(10);
declare parent_rate float(10);
declare amount float(10);
declare parent_amount float(10);
declare parents_amount float(10);
declare db_rate float(10);
declare db_parent_rate float(10);
declare db_parents_rate float(10);
declare shop_type char(10);
set shop_type = new.shop_type;
set pay_status = new.pay_status;
set db_pid = new.pid;
IF shop_type = 'J' THEN
set user_id = (select `uid` from `ytt_zone` where dwxk_adsense_id=db_pid limit 1);
ELSE
set user_id = (select `uid` from `ytt_zone` where pid=db_pid limit 1);
END IF;
set parent_id = 0;
set parents_id = 0;
set db_parent_pid = 0;
set db_parents_pid = 0;
set rate = 0;
set parent_rate = 0;
set amount = new.fee;
set parent_amount = 0;
set parents_amount = 0;
set db_rate = new.commission_rate;
set db_parent_rate = 0;
set db_parents_rate = 0;
IF (pay_status = 'paid') or (pay_status = 'settle') THEN
	set parent_id  = (select `ParentID` from `ytt_user` where id = user_id limit 1);
	set db_parent_pid = (select `pid` from `ytt_user` where id = parent_id limit 1);
	IF parent_id > 0 THEN
		set parents_id = (select `ParentID` from `ytt_user` where  id = parent_id limit 1);
		set db_parents_pid = (select `pid` from `ytt_user` where id = parents_id limit 1);
		set rate        = (select `dip` from `ytt_user_proxy_ratio` where (uid = parent_id) AND (cid = user_id));
		IF (rate > 0 AND rate < 100) THEN 
			set parent_amount = amount*((100-rate)/100);
			set amount = amount-parent_amount;
			set db_parent_rate = db_rate*((100-rate)/100);
			set db_rate = db_rate-db_parent_rate;
			IF parents_id > 0 THEN
				set parent_rate = (select `dip` from ytt_user_proxy_ratio where (uid = parents_id) AND (cid = parent_id) limit 1);
				IF (parent_rate > 0 AND parent_rate < 100) THEN
					set parents_amount = parent_amount*((100-parent_rate)/100);
					set parent_amount  = parent_amount-parents_amount;
					set db_parents_rate = db_parent_rate*((100-rate)/100);
					set db_parent_rate = db_parent_rate-db_parents_rate;
					INSERT INTO `ytt_order_commission` (`order_id`,`item_id`,`user_id`,`order_num`,`commission_rate`,`fee`,`total_money`,`create_time`,`earning_time`,`pay_status`,`pid`,`from_pid`,`order_pid`) VALUE (new.order_id,new.item_id,parents_id,new.order_num,db_parents_rate,parents_amount,new.total_money,new.create_time,new.earning_time,new.pay_status,db_parents_pid,db_parent_pid,db_pid);
				END IF;
			END IF;
			INSERT INTO `ytt_order_commission` (`order_id`,`item_id`,`user_id`,`order_num`,`commission_rate`,`fee`,`total_money`,`create_time`,`earning_time`,`pay_status`,`pid`,`from_pid`,`order_pid`) VALUE (new.order_id,new.item_id,parent_id,new.order_num,db_parent_rate,parent_amount,new.total_money,new.create_time,new.earning_time,new.pay_status,db_parent_pid,db_pid,db_pid);
		END IF;
	END IF;
	INSERT INTO `ytt_order_commission` (`order_id`,`item_id`,`user_id`,`order_num`,`commission_rate`,`fee`,`total_money`,`create_time`,`earning_time`,`pay_status`,`pid`,`from_pid`,`order_pid`) VALUE (new.order_id,new.item_id,user_id,new.order_num,db_rate,amount,new.total_money,new.create_time,new.earning_time,new.pay_status,db_pid,db_pid,db_pid);
ELSE
	INSERT INTO `ytt_order_commission` (`order_id`,`item_id`,`user_id`,`order_num`,`commission_rate`,`fee`,`total_money`,`create_time`,`earning_time`,`pay_status`,`pid`,`from_pid`,`order_pid`) VALUE (new.order_id,new.item_id,user_id,new.order_num,db_rate,amount,new.total_money,new.create_time,new.earning_time,new.pay_status,db_pid,db_pid,db_pid);
END IF;
END;