#первый запрос:
SELECT hours.date, boosterpack.id, SUM(analytics.amount) AS "user_spend", SUM(items.price) AS "user_get" FROM `analytics`
  LEFT JOIN boosterpack ON `boosterpack`.`id` = analytics.object_id AND analytics.action = "buy"
  LEFT JOIN boosterpack_info ON `boosterpack`.`id` = `boosterpack_info`.`boosterpack_id`
  LEFT JOIN items ON `boosterpack_info`.`item_id` = `items`.`id`
  JOIN (SELECT id, date_format(`time_created`, '%Y-%m-%d %H:00:00' ) as date FROM `analytics`) as hours ON hours.id=analytics.id
GROUP BY hours.date, `boosterpack`.id;

# Второй запрос
SELECT `user`.`email`, `user`.`wallet_total_refilled`, `user`.`wallet_balance`, SUM(`items`.`price`) AS "total_likes"
FROM `user`
         LEFT JOIN analytics ON `analytics`.`user_id` = `user`.`id`
         LEFT JOIN boosterpack ON `boosterpack`.`id` = analytics.object_id AND analytics.action = "buy"
         LEFT JOIN boosterpack_info ON `boosterpack`.`id` = `boosterpack_info`.`boosterpack_id`
         LEFT JOIN items ON `boosterpack_info`.`item_id` = `items`.`id`;