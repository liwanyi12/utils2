$redisHelper = new RedisHelper();


$redisHelper->addScore('user1', 100);
$redisHelper->addScore('user2', 200);

// 自定义过期时间
$redisHelper->addScore('user3', 300, 86400, 604800, 2592000); // 日榜 1 天，周榜 7 天，月榜 30 天



// 获取总榜前 10 名
$totalRank = $redisHelper->getRank('total', 10);
print_r($totalRank);

// 获取日榜前 5 名
$dayRank = $redisHelper->getRank('day', 5);
print_r($dayRank);


// 获取 user1 在日榜中的排名
$rank = $redisHelper->getMemberRank('day', 'user1');
if ($rank !== false) {
echo "user1 在日榜中的排名是：" . ($rank + 1) . "\n";
} else {
echo "获取排名失败。\n";
}










