#!/usr/bin/php
<?php
if ($argc == 5){
	// rcon
	define('MQ_SERVER_ADDR', 'localhost');
	// rcon port
	define('MQ_SERVER_PORT', 25575);
	// rcon password
	define('MQ_SERVER_PASS', '');
	define('MQ_TIMEOUT', 2);
	require '/path/to/lib/MinecraftRcon.class.php';
	// mysql
	$link = mysqli_connect("localhost", "mysqlUser", "mysqlPassword", "mysqlDatabase");
	if (mysqli_connect_errno())
	{
		exit();
	}
	$link2 = mysqli_connect("localhost", "mysqlUser", "mysqlPassword", "mysqlDatabase");
	if (mysqli_connect_errno())
	{
		exit();
	}
	// ./eco give pseudo valeur "raison"
	if (strcmp($argv[1], 'give') == 0 && $argc == 5 && is_numeric($argv[3]))
	{
		$pseudo = $argv[2];
		$points = $argv[3];
		$raison = $argv[4];
		$query = "SELECT `playername` FROM `accounts` where `active` = 1 and `playername` ='" . mysqli_real_escape_string($link, $pseudo)."'";
		if ($result = mysqli_query($link, $query)){
			$row = mysqli_fetch_assoc($result);
			mysqli_free_result($result);
			if (!empty($row)){
				$query = "SELECT `pointsTotal` FROM `points` WHERE `playername` = '" . $pseudo ."'";
				if ($result = mysqli_query($link2, $query)){
					$row = mysqli_fetch_assoc($result);
					mysqli_free_result($result);
					$pointsTotal = $row["pointsTotal"] + $points;
					$query = "UPDATE `auradata`.`points` SET `pointsTotal` = '" . $pointsTotal . "' WHERE `points`.`playername` = '" . $pseudo ."'";
					mysqli_query($link2, $query);
					try	{
						$Rcon = new MinecraftRcon;					
						$Rcon->Connect(MQ_SERVER_ADDR, MQ_SERVER_PORT, MQ_SERVER_PASS, MQ_TIMEOUT);					
						$Data = $Rcon->Command("eco give " . $pseudo . " " . $points);					
						if ($Data === false){
							throw new MinecraftRconException("Failed to get command result.");
						}
						else if(StrLen($Data) == 0){
							throw new MinecraftRconException("Got command result, but it's empty.");
						}
						$Data = $Rcon->Command("broadcast " . $pseudo . " obtient une recompense de " . $points . " points. Raison: ". $raison);					
						if ($Data === false){
							throw new MinecraftRconException("Failed to get command result.");
						}
						else if(StrLen($Data) == 0){
							throw new MinecraftRconException("Got command result, but it's empty.");
						}	
					}
					catch( MinecraftRconException $e){
					}
					$Rcon->Disconnect();
				}
			}
		}
	}
	// ./eco take pseudo valeur "raison"
	if (strcmp($argv[1], 'take') == 0 && $argc == 5 && is_numeric($argv[3])){
		$pseudo = $argv[2];
		$points = $argv[3];
		$raison = $argv[4];
		$query = "SELECT `playername` FROM `accounts` where `active` = 1 and `playername` ='" . mysqli_real_escape_string($link, $pseudo)."'";
		if ($result = mysqli_query($link, $query)){
			$row = mysqli_fetch_assoc($result);
			mysqli_free_result($result);
			if (!empty($row)){
				try	{
					$Rcon = new MinecraftRcon;					
					$Rcon->Connect(MQ_SERVER_ADDR, MQ_SERVER_PORT, MQ_SERVER_PASS, MQ_TIMEOUT);					
					$Data = $Rcon->Command("eco take " . $pseudo . " " . $points);					
					if ($Data === false){
						throw new MinecraftRconException("Failed to get command result.");
					}
					else if(StrLen($Data) == 0){
						throw new MinecraftRconException("Got command result, but it's empty.");
					}
					$Data = $Rcon->Command("broadcast " . $pseudo . " obtient une amende de " . $points . " points. Raison: ". $raison);					
					if ($Data === false){
						throw new MinecraftRconException("Failed to get command result.");
					}
					else if(StrLen($Data) == 0){
						throw new MinecraftRconException("Got command result, but it's empty.");
					}					
				}
				catch( MinecraftRconException $e){
				}
				$Rcon->Disconnect();				
			}
		}
	}
	mysqli_close($link);
	mysqli_close($link2);
}
?>