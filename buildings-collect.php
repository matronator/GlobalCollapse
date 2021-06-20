<?php

$user = 'root';
$password = 'root';
$db = 'globalcollapse';
$host = 'localhost';
$port = 3306;

$link = mysqli_init();
$success = mysqli_real_connect(
  $link,
  $host,
  $user,
  $password,
  $db,
  $port
);

$playerBuildings = mysqli_query($link, "SELECT * FROM `player_buildings` WHERE `level` > '0' AND `is_upgrading` <= '0';");
$rows = array();
$i = 0;
if ($playerBuildings) {
  while ($row = mysqli_fetch_assoc($playerBuildings)) {
    $rows[] = $row;
    $i++;
  }
}
$players = [];
foreach ($rows as $row) {
  $income = 0;
  $capacity = 0;
  $storage = 0;
  $level = 0;
  $userId = intval($row['user_id']);
  if (!$row['is_upgrading']) {
    if (!in_array($userId, $players)) {
      array_push($players, $userId);
    }
    $playerBuildingId = $row['id'];
    $capacity = intval($row['capacity']);
    $storage = $row['storage'] != NULL ? intval($row['storage']) : 0;
    $income = intval($row['income']);
    $newStorage = $storage + $income;
    if ($capacity >= $newStorage) {
      $addResource = mysqli_query($link, "UPDATE `player_buildings` SET `storage` = '$newStorage' WHERE `id` = '$playerBuildingId';");
    } else {
      if ($capacity <= $newStorage && $capacity != $storage) {
        $addResource = mysqli_query($link, "UPDATE `player_buildings` SET `storage` = '$capacity' WHERE `id` = '$playerBuildingId';");
      }
    }
  }
}
foreach($players as $player) {
  $pIncomeUpdate = mysqli_query($link, "UPDATE `player_income` SET `last_collection` = now() WHERE `user_id` = '$player';");
}

header("Location: https://www.google.com");
exit;

?>
