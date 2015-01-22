<?php
//PATH=/usr/sbin:/usr/bin:/sbin:/bin

// auradata
$link = mysqli_connect("localhost", "mysqlUser", "mysqlPassword", "mysqlDatabase");
if (mysqli_connect_errno()) {
	printf("Connect failed: %s\n", mysqli_connect_error());
	exit();
}

$ligne = '';
$nbrLigne = 0;
$monfichier = fopen('./server.log', 'r');
// $monfichier = fopen('server.log', 'r');



$query = "SELECT `valeur` FROM `config` WHERE `config` = 'lastRow'";
if ($result = mysqli_query($link, $query)){
	$row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	if (empty($row)){
		while(!feof($monfichier)) { 
			$nbrLigne++;
			$ligneprec=$ligne; 
			$ligne = fgets($monfichier,1024);
			if(preg_match("/was slain by|was shot by/",$ligne))	{
				if(!preg_match("/Zombie|Spider|Enderman|Wolf|Creeper|Skeleton|Silverfish|Ghast|Blaze|Slime|Witch|Magma Cube|Cave Spider|arrow/", $ligne )) {
					$result = explode(" ", $ligne);	
					$query = "INSERT INTO `auradata`.`pvpvs` (`id` , `victime` , `type` , `tueur`) VALUES (NULL , '" .mysqli_real_escape_string($link, $result[3]). "', '".mysqli_real_escape_string($link, $result[5])."', '". mysqli_real_escape_string($link, trim($result[7]))."')";
					mysqli_query($link, $query);
				}		
			}
		} 
	}
	else{
		while(!feof($monfichier)) { 
			$ligneprec=$ligne; 
			$ligne = fgets($monfichier,1024);
			$nbrLigne++;
			if ($nbrLigne >= intval($row["valeur"])){
				if(preg_match("/was slain by|was shot by/",$ligne))	{
					if(!preg_match("/Zombie|Spider|Enderman|Wolf|Creeper|Skeleton|Silverfish|Ghast|Blaze|Slime|Witch|Magma Cube|Cave Spider|arrow/", $ligne )) {
						$result = explode(" ", $ligne);	
						$query = "INSERT INTO `auradata`.`pvpvs` (`id` , `victime` , `type` , `tueur`) VALUES (NULL , '" .mysqli_real_escape_string($link, $result[3]). "', '".mysqli_real_escape_string($link, $result[5])."', '". mysqli_real_escape_string($link, trim($result[7]))."')";
						mysqli_query($link, $query);
					}		
				}
			}				
		} 
	}
	
 }

$query = "SELECT `config` FROM `config` WHERE `config` = 'lastRow'";
if ($result = mysqli_query($link, $query)){
	$row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	if (!empty($row)){
		$query = "UPDATE `auradata`.`config` SET `valeur` = '" . mysqli_real_escape_string($link, $nbrLigne) . "' WHERE `config`.`config` = 'lastRow'";
	}
	else{
		$query = "INSERT INTO `auradata`.`config` (`id` , `config` , `valeur`) VALUES (NULL , 'lastRow', '". mysqli_real_escape_string($link, $nbrLigne) ."')";
	}
	mysqli_query($link, $query);
}

mysqli_close($link);
fclose($monfichier);
?>
