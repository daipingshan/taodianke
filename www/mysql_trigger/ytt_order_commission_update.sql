create trigger updateOrderRate
AFTER UPDATE ON `ytt_order`
FOR EACH ROW
BEGIN
declare old_pid char(40);
declare new_pay_status char(10);
declare old_pay_status char(10);
declare old_order_id char(30);
declare old_item_id char(20);
declare old_order_num int(11);
declare old_total_money float(10);
declare new_earning_time char(20);
declare new_fee float(10);
declare new_total_money float(10);
declare count int(11);
set old_pid = old.pid;
set new_pay_status = new.pay_status;
set old_pay_status = old.pay_status;
set old_order_id = old.order_id;
set old_item_id = old.item_id;
set old_order_num = old.order_num;
set old_total_money = old.total_money;
set new_earning_time = new.earning_time;
set new_fee = new.fee;
set new_total_money = new.total_money;
set count = (select count(`id`) from `ytt_order_commission` where order_id=old_order_id and item_id=old_item_id and order_num=old_order_num);
IF (new_pay_status != old_pay_status) and (count > 0) THEN
    IF new_pay_status = 'settle' THEN
        update `ytt_order_commission` set pay_status=new_pay_status,earning_time=new_earning_time where order_id=old_order_id and item_id=old_item_id and order_num=old_order_num;
    END IF;
    IF new_pay_status = 'fail' THEN
        update `ytt_order_commission` set pay_status=new_pay_status,fee=new_fee,total_money=new_total_money,earning_time=new_earning_time where order_id=old_order_id and item_id=old_item_id and order_num=old_order_num;
    END IF;
END IF;
IF old_total_money != new_total_money THEN
    update `ytt_order_commission` set pay_status=new_pay_status,total_money=new_total_money,fee=new_fee where order_id=old_order_id and item_id=old_item_id and order_num=old_order_num;
    IF count > 1 THEN
        delete from `ytt_order_commission` where order_id=old_order_id and item_id=old_item_id and order_num=old_order_num and pid != old_pid;
    END IF; 
END IF;
END