<?php

if(true) {
  if(true) {
    $user = 'root';
    $password = 'rootroot';
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

    $playerBuildings = mysqli_query($link, "SELECT `player_buildings`.`id`, `player_buildings`.`user_id`, `player_buildings`.`income`, `player_buildings`.`capacity`, `player_buildings`.`storage`, `player_lands`.`is_upgrading` FROM `player_buildings` INNER JOIN `player_lands` ON `player_buildings`.`player_land_id` = `player_lands`.`id` WHERE `player_buildings`.`level` > 0");
    $rows = array();
    if ($playerBuildings) {
      while ($row = mysqli_fetch_assoc($playerBuildings)) {
        $rows[] = $row;
      }
    }
    foreach ($rows as $row) {
      // Default value if NULL
      $income = 0;
      $capacity = 0;
      $storage = 0;
      $level = 0;
      $userId = (int) $row['user_id'];
      if (!$row['is_upgrading']) {
        $playerBuildingId = $row['id'];
        $capacity = (int) $row['capacity'];
        $storage = (int) $row['storage'];
        $income = (int) $row['income'];
        $newStorage = $storage + $income;
        if ($capacity > $newStorage) {
          mysqli_query($link, "UPDATE `player_buildings` SET `storage` = '$newStorage' WHERE `id` = '$playerBuildingId'");
          // echo "Player building " . $playerBuildingId . " updated<br>";
        } else if ($capacity <= $newStorage) {
          if ($capacity != $storage) {
            mysqli_query($link, "UPDATE `player_buildings` SET `storage` = '$capacity' WHERE `id` = '$playerBuildingId'");
            // echo "Player building " . $playerBuildingId . " updated<br>";
          }
        }
      }
      mysqli_query($link, "UPDATE `player_income` SET `last_collection` = now() WHERE `user_id` = '$userId'");
    }
    echo "Finished";
    // header("Location: https://www.google.com");
    // exit;
  } else {
    // header("Location: https://www.google.com");
    // exit;
  }
} else {
  // header("Location: https://www.google.com");
  // exit;
}

?>
