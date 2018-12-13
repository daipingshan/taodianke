create trigger deleteItemsLike
AFTER DELETE ON `ytt_items`
FOR EACH ROW
BEGIN
delete from `ytt_items_like` where num_iid = old.num_iid;
END;