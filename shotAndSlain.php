<?php
	// xauth
	$link = mysqli_connect("localhost", "mysqlUser", "mysqlPassword", "mysqlDatabase");
	if (mysqli_connect_errno()) {
		echo 'En maintenance';
		exit();
	}
	// auradata
	$link2 = mysqli_connect("localhost", "mysqlUser", "mysqlPassword", "mysqlDatabase");
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	$query = "SELECT `playername` FROM `accounts` where `active` = 1 order by `playername` asc";
	if ($result = mysqli_query($link, $query)) {
		while ($row = mysqli_fetch_assoc($result)) {	
			$slain = 0;
			$shot = 0;
			$querySlain = "SELECT count(`victime`) as slain FROM `pvpvs` WHERE `type` = 'slain' AND `tueur` = '" . $row["playername"] ."'";
			if ($resultSlain = mysqli_query($link2, $querySlain)){
				$rowSlain = mysqli_fetch_assoc($resultSlain);
				mysqli_free_result($resultSlain);
				$slain = $rowSlain["slain"];
			}
			$queryShot = "SELECT count(`victime`) as shot FROM `pvpvs` WHERE `type` = 'shot' AND `tueur` = '" . $row["playername"] ."'";
			if ($resultShot = mysqli_query($link2, $queryShot)){
				$rowShot = mysqli_fetch_assoc($resultShot);
				mysqli_free_result($resultShot);
				$shot = $rowShot["shot"];
			}			
			$query = "SELECT `playername` FROM `pvpstats` WHERE `playername` = '".$row["playername"]."'";
			if ($result2 = mysqli_query($link2, $query)){
				$row2 = mysqli_fetch_assoc($result2);
				mysqli_free_result($result2);
				if (!empty($row2)){
					$query = "UPDATE `auradata`.`pvpstats` SET `shot` = '". mysqli_real_escape_string($link2, $shot) ."', `slain` = '". mysqli_real_escape_string($link2, $slain) ."' WHERE `pvpstats`.`playername` = '". mysqli_real_escape_string($link2, $row["playername"])."'";
				}
				else{
					$query = "INSERT INTO `auradata`.`pvpstats` (`id` , `playername` , `shot` , `slain` ) VALUES ( NULL , '". mysqli_real_escape_string($link2, $row["playername"]) ."', '". mysqli_real_escape_string($link2, $shot) ."', '". mysqli_real_escape_string($link2, $slain) ."')";
				}
				mysqli_query($link2, $query);
			}	
		}
		mysqli_free_result($result);
	}
	mysqli_close($link);
	mysqli_close($link2);
?>