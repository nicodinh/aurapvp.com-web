<?php
	// Rcon
	define( 'MQ_SERVER_ADDR', 'localhost' );
	define( 'MQ_SERVER_PORT', 25575 );
	define( 'MQ_SERVER_PASS', '1SMdU6zU4sY3WE9' );
	define( 'MQ_TIMEOUT', 2 );		
	require '/path/to/MinecraftRcon.class.php';
	// Taux à enlever pour attribution des points en fonction des kills et des deaths
	$taux = 80;
	// Attribution points haut faits
	// PvP
	$hfpPaysan = 10000;
	$hfpAssassin = 25000;
	$hfpBourreau = 50000;
	$hfpBarbare = 75000;
	$hfpEcuyer = 150000;
	$hfpChevalier = 300000;
	$hfpSeigneur = 600000;
	$hfpSauron = 1000000;
	$hfpArcher = 25000;
	$hfpLegolas = 50000;
	$hfpChampion = 50000;
	$hfpDominateur = 50000;
	$hfpExplorateur = 200000;
	// PvE
	$hfpSpiderKiller = 50000;
	$hfpCreeperKiller = 50000;
	$hfpZombieKiller = 50000;
	$hfpBlazeKiller = 50000;
	$hfpCaveSpiderKiller = 50000;
	$hfpEndermanKiller = 50000;
	$hfpMagmaCubeKiller = 50000;
	$hfpPigZombieKiller = 50000;
	$hfpSkeletonKiller = 50000;
	$hfpSlimeKiller = 50000;
	$hfpWolfKiller = 50000;
	// Binouze
	$hfpbinouze1 = 20000;
	$hfpbinouze2 = 20000;
	$hfpbinouze3 = 20000;

	// xauth
	$link = mysqli_connect("localhost", "mysqlUser", "mysqlPassword", "mysqlDatabase");
	if (mysqli_connect_errno())
	{
		exit();
	}
	// auradata
	$link2 = mysqli_connect("localhost", "mysqlUser", "mysqlPassword", "mysqlDatabase");
	if (mysqli_connect_errno())
	{
		exit();
	}
	// stats
	$link3 = mysqli_connect("localhost", "mysqlUser", "mysqlPassword", "mysqlDatabase");
	if (mysqli_connect_errno())
	{
		exit();
	}
	// mcmmo
	$link4 = mysqli_connect("localhost", "mysqlUser", "mysqlPassword", "mysqlDatabase");
	if (mysqli_connect_errno())
	{
		exit();
	}
	// tf2
	$link5 = mysqli_connect("localhost", "mysqlUser", "mysqlPassword", "mysqlDatabase");
	if (mysqli_connect_errno())
	{
		exit();
	}
	
	$query = "SELECT `playername` FROM `accounts` where `active` = 1 order by `playername` asc";
	if ($resultGlobal = mysqli_query($link, $query))
	{
		while ($rowGlobal = mysqli_fetch_assoc($resultGlobal))
		{
			$playername = $rowGlobal["playername"];
			$kills = 0;
			$deaths = 0;
			$refKills = 0;
			$refDeaths = 0;
			$points = 0;
			// kills general
			$query = "SELECT `amount` FROM `Stats_kill` WHERE `type` = 'Player' AND `player` = '" . $playername . "'";			
			if ($result = mysqli_query($link3, $query))
			{
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$kills = $row["amount"];
			}
			// kills tf2
			$query = "SELECT CONVERT(`kills` , UNSIGNED INTEGER) AS kills FROM `players` WHERE `username` = '" . $playername . "'";			
			if ($result = mysqli_query($link5, $query))
			{
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$kills = $row["kills"] + $kills;
			}
			// deaths general
			$query = "SELECT `amount` FROM `Stats_death` WHERE `cause` = 'Player' AND `player` = '" . $playername . "'";			
			if ($result = mysqli_query($link3, $query))
			{
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$deaths = $row["amount"];
			}
			// deaths tf2
			$query = "SELECT CONVERT(`deaths` , UNSIGNED INTEGER) AS deaths FROM `players` WHERE `username` = '" . $playername . "'";			
			if ($result = mysqli_query($link5, $query))
			{
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$deaths = $row["deaths"] + $deaths;
			}
			$query = "SELECT `refkills`, `refdeaths` FROM `points` WHERE `playername` = '" . $playername . "'";			
			if ($result = mysqli_query($link2, $query))
			{
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				if (!empty($row))
				{
					$refKills = $row["refkills"];
					$refDeaths = $row["refdeaths"];
				}
				else 
				{
					$refKills = 0;
					$refDeaths = 0;
					$query = "INSERT INTO `auradata`.`points` (
							`id` ,
							`playername` ,
							`refkills` ,
							`refdeaths` ,
							`pointsTotal` ,
							`pointsDispo` ,
							`hfPaysan` ,
							`hfAssassin` ,
							`hfBourreau` ,
							`hfBarbare` ,
							`hfEcuyer` ,
							`hfChevalier` ,
							`hfSeigneur` ,
							`hfSauron` ,
							`hfArcher` ,
							`hfLegolas` ,
							`hfSpiderKiller` ,
							`hfCreeperKiller` ,
							`hfZombieKiller` ,
							`hfBlazeKiller` ,
							`hfCaveSpiderKiller` ,
							`hfEndermanKiller` ,
							`hfMagmaCubeKiller` ,
							`hfPigZombieKiller` ,
							`hfSkeletonKiller` ,
							`hfSlimeKiller` ,
							`hfWolfKiller` ,
							`hfFerrailleur` ,
							`hfOrfevre` ,
							`hfJoailler`,
							`hfPochtron`,
							`hfIvrogne`,
							`hfBoitsanssoif`
							)
							VALUES (
							NULL , '". $playername . "', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0'
							);";
					mysqli_query($link2, $query);
				}
			}
			// Calcul des points en fonction des kills et des deaths
			$points = (($kills - $refKills) - floor(($taux * ($deaths - $refDeaths ) / 100))) * 100;
			if ($points <= 0)
			{
				$points = 0;
			}
			else
			{
				$query = "UPDATE `auradata`.`points` SET `refkills` = '" . $kills . "', `refdeaths` = '" . $deaths . "' WHERE `points`.`playername` = '" . $playername ."';";
				mysqli_query($link2, $query);
			}
			// Calcul des points et deblocage des titres Paysan, Assassin, Bourreau, Barbare, Ecuyer, Chevalier, Seigneur, Sauron
			$query = "SELECT `amount` FROM `Stats_kill` WHERE `type` = 'Player' AND `player` = '" . $playername ."'";
			if ($result = mysqli_query($link3, $query))
			{
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$amount = $row["amount"];			
				$query = "SELECT CONVERT(`kills` , UNSIGNED INTEGER) AS kills FROM `players` WHERE `username` = '" . mysqli_real_escape_string($link, $playername) ."'";			
				if ($result = mysqli_query($link5, $query))
				{
					$row2 = mysqli_fetch_assoc($result);
					mysqli_free_result($result);
					if (!empty($row2))
					{
						$amount = $row2["kills"] + $amount; 
					}
				}		
				$query = "SELECT `hfPaysan` , `hfAssassin` , `hfBourreau` , `hfBarbare` FROM `points` WHERE `playername` = '" . $playername ."'";
				if ($result = mysqli_query($link2, $query))
				{
					$row = mysqli_fetch_assoc($result);
					mysqli_free_result($result);
					if ($amount >= 400 and $row["hfPaysan"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfPaysan` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpPaysan;
					}
					if ($amount >= 1000 and $row["hfAssassin"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfAssassin` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpAssassin;
					}
					if ($amount >= 2500 and $row["hfBourreau"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfBourreau` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpBourreau;
					}					
					if ($amount >= 10000 and $row["hfBarbare"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfBarbare` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpBarbare;
					}	
					if ($amount >= 17500 and $row["hfEcuyer"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfEcuyer` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpEcuyer;
					}	
					if ($amount >= 25000 and $row["hfChevalier"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfChevalier` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpChevalier;
					}	
					if ($amount >= 35000 and $row["hfSeigneur"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfSeigneur` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpSeigneur;
					}	
					if ($amount >= 50000 and $row["hfSauron"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfSauron` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpSauron;
					}						
				}			
			}
			// Calcul des points et deblocage des titres Chevalier, Sauron
/*			$query = "SELECT count(`victime`) as amount FROM `pvpvs` WHERE `type` = 'slain' AND `tueur` = '" . $playername ."'";
			if ($result = mysqli_query($link2, $query)){
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$amount = $row["amount"];
				$query = "SELECT `hfChevalier` , `hfSauron` FROM `points` WHERE `playername` = '" . $playername ."'";
				if ($result = mysqli_query($link2, $query)){
					$row = mysqli_fetch_assoc($result);
					mysqli_free_result($result);
					if ($amount >= 5000 and $row["hfChevalier"] == 0){
						$query = "UPDATE `auradata`.`points` SET `hfChevalier` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpChevalier;
					}
					if ($amount >= 10000 and $row["hfSauron"] == 0){
						$query = "UPDATE `auradata`.`points` SET `hfSauron` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpSauron;
					}				
				}			
			}	
*/			
			// Calcul des points et deblocage des titres Champion, Dominateur
			$query = "SELECT CONVERT(`points_captured`, UNSIGNEDINTEGER) AS points_captured, CONVERT(`games_won`, UNSIGNEDINTEGER) AS games_won
						FROM `players`
						WHERE `username` = '" . $playername ."'";
			if ($result = mysqli_query($link5, $query))
			{
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$points_captured = $row["points_captured"];
				$games_won = $row["games_won"];
				$query = "SELECT `hfChampion` , `hfDominateur` FROM `points` WHERE `playername` = '" . $playername ."'";
				if ($result = mysqli_query($link2, $query))
				{
					$row = mysqli_fetch_assoc($result);
					mysqli_free_result($result);
					if ($games_won >= 2500 and $row["hfChampion"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfChampion` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpChampion;
					}
					if ($points_captured >= 2000 and $row["hfDominateur"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfDominateur` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpDominateur;
					}				
				}			
			}
			// Calcul des points et deblocage des titres Archer, Legolas
			/*
			$query = "SELECT count(`victime`) as amount FROM `pvpvs` WHERE `type` = 'shot' AND `tueur` = '" . $playername ."'";
			if ($result = mysqli_query($link2, $query)){
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$amount = $row["amount"];
				$query = "SELECT `hfArcher` , `hfLegolas` FROM `points` WHERE `playername` = '" . $playername ."'";
				if ($result = mysqli_query($link2, $query)){
					$row = mysqli_fetch_assoc($result);
					mysqli_free_result($result);
					if ($amount >= 2000 and $row["hfArcher"] == 0){
						$query = "UPDATE `auradata`.`points` SET `hfArcher` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpArcher;
					}
					if ($amount >= 5000 and $row["hfLegolas"] == 0){
						$query = "UPDATE `auradata`.`points` SET `hfLegolas` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpLegolas;
					}				
				}			
			}
			*/
			// Calcul des points et deblocage des titres alcoolo
			$query = "SELECT `alcoolo` FROM auradata.`alcoolo` WHERE `playername` = '" . $playername ."'";
			if ($result = mysqli_query($link2, $query))
			{
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$amount = $row["alcoolo"];
				$query = "SELECT `hfPochtron`, `hfIvrogne`, `hfBoitsanssoif` FROM `points` WHERE `playername` = '" . $playername ."'";
				if ($result = mysqli_query($link2, $query))
				{
					$row = mysqli_fetch_assoc($result);
					mysqli_free_result($result);
					if ($amount >= 10000 and $row["hfPochtron"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfPochtron` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpbinouze1;
					}
					if ($amount >= 20000 and $row["hfIvrogne"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfIvrogne` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpbinouze2;
					}				
					if ($amount >= 30000 and $row["hfBoitsanssoif"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfBoitsanssoif` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpbinouze3;
					}
				}			
			}
			// Calcul des points et deblocage du titre Explorateur
			$query = "SELECT `id` FROM `mcmmo_users` WHERE `user` ='" . $playername ."'";
			if ($result = mysqli_query($link4, $query))
			{
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$query = "SELECT `excavation`, `fishing`, `herbalism`, `mining`, `woodcutting`, `axes`, `archery`, `swords`, `taming`, `unarmed` FROM `mcmmo_skills` WHERE `user_id` = " . $row["id"];
				if ($result = mysqli_query($link4, $query))
				{
					$rowmcMMo = mysqli_fetch_assoc($result);
					mysqli_free_result($result);
					$query = "SELECT `hfExplorateur` FROM `points` WHERE `playername` = '" . $playername ."'";
					if ($result = mysqli_query($link2, $query))
					{
						$row = mysqli_fetch_assoc($result);
						mysqli_free_result($result);
						if ($rowmcMMo["excavation"] >= 400 
							and $rowmcMMo["fishing"] >= 400 
							and $rowmcMMo["herbalism"] >= 400 
							and $rowmcMMo["mining"] >= 400 
							and $rowmcMMo["woodcutting"] >= 400 
							and $rowmcMMo["axes"] >= 400 
							and $rowmcMMo["archery"] >= 400 
							and $rowmcMMo["swords"] >= 400 
							and $rowmcMMo["taming"] >= 400 
							and $rowmcMMo["unarmed"] >= 400 
							and $row["hfExplorateur"] == 0)
						{
							$query = "UPDATE `auradata`.`points` SET `hfExplorateur` = '1' WHERE `points`.`playername` = '" . $playername ."'";
							mysqli_query($link2, $query);
							$points += $hfpExplorateur;
						}
					}
				}
			}				
			// Calcul des points et deblocage du titre Spider
			$query = "SELECT `amount` FROM `Stats_kill` WHERE `type` = 'Spider' and `player` = '" . $playername ."'";
			if ($result = mysqli_query($link3, $query))
			{
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$amount = $row["amount"];
				$query = "SELECT `hfSpiderKiller` FROM `points` WHERE `playername` = '" . $playername ."'";
				if ($result = mysqli_query($link2, $query))
				{
					$row = mysqli_fetch_assoc($result);
					mysqli_free_result($result);
					if ($amount >= 2000 and $row["hfSpiderKiller"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfSpiderKiller` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpSpiderKiller;
					}
				}			
			}	
			// Calcul des points et deblocage du titre Creeper
			$query = "SELECT `amount` FROM `Stats_kill` WHERE `type` = 'Creeper' and `player` = '" . $playername ."'";
			if ($result = mysqli_query($link3, $query))
			{
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$amount = $row["amount"];
				$query = "SELECT `hfCreeperKiller` FROM `points` WHERE `playername` = '" . $playername ."'";
				if ($result = mysqli_query($link2, $query))
				{
					$row = mysqli_fetch_assoc($result);
					mysqli_free_result($result);
					if ($amount >= 2000 and $row["hfCreeperKiller"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfCreeperKiller` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpCreeperKiller;
					}
				}			
			}				
			// Calcul des points et deblocage du titre Zombie
			$query = "SELECT `amount` FROM `Stats_kill` WHERE `type` = 'Zombie' and `player` = '" . $playername ."'";
			if ($result = mysqli_query($link3, $query))
			{
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$amount = $row["amount"];
				$query = "SELECT `hfZombieKiller` FROM `points` WHERE `playername` = '" . $playername ."'";
				if ($result = mysqli_query($link2, $query))
				{
					$row = mysqli_fetch_assoc($result);
					mysqli_free_result($result);
					if ($amount >= 2000 and $row["hfZombieKiller"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfZombieKiller` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpZombieKiller;
					}
				}			
			}				
			// Calcul des points et deblocage du titre Blaze
			$query = "SELECT `amount` FROM `Stats_kill` WHERE `type` = 'Blaze' and `player` = '" . $playername ."'";
			if ($result = mysqli_query($link3, $query))
			{
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$amount = $row["amount"];
				$query = "SELECT `hfBlazeKiller` FROM `points` WHERE `playername` = '" . $playername ."'";
				if ($result = mysqli_query($link2, $query))
				{
					$row = mysqli_fetch_assoc($result);
					mysqli_free_result($result);
					if ($amount >= 2000 and $row["hfBlazeKiller"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfBlazeKiller` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpBlazeKiller;
					}
				}			
			}		
			// Calcul des points et deblocage du titre CaveSpider
			$query = "SELECT `amount` FROM `Stats_kill` WHERE `type` = 'Cave_spider' and `player` = '" . $playername ."'";
			if ($result = mysqli_query($link3, $query))
			{
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$amount = $row["amount"];
				$query = "SELECT `hfCaveSpiderKiller` FROM `points` WHERE `playername` = '" . $playername ."'";
				if ($result = mysqli_query($link2, $query))
				{
					$row = mysqli_fetch_assoc($result);
					mysqli_free_result($result);
					if ($amount >= 2000 and $row["hfCaveSpiderKiller"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfCaveSpiderKiller` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpCaveSpiderKiller;
					}
				}			
			}	
			// Calcul des points et deblocage du titre Enderman
			$query = "SELECT `amount` FROM `Stats_kill` WHERE `type` = 'Enderman' and `player` = '" . $playername ."'";
			if ($result = mysqli_query($link3, $query))
			{
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$amount = $row["amount"];
				$query = "SELECT `hfEndermanKiller` FROM `points` WHERE `playername` = '" . $playername ."'";
				if ($result = mysqli_query($link2, $query))
				{
					$row = mysqli_fetch_assoc($result);
					mysqli_free_result($result);
					if ($amount >= 2000 and $row["hfEndermanKiller"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfEndermanKiller` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpEndermanKiller;
					}
				}			
			}				
			// Calcul des points et deblocage du titre MagmaCube 
			$query = "SELECT `amount` FROM `Stats_kill` WHERE `type` = 'Magma_cube' and `player` = '" . $playername ."'";
			if ($result = mysqli_query($link3, $query))
			{
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$amount = $row["amount"];
				$query = "SELECT `hfMagmaCubeKiller` FROM `points` WHERE `playername` = '" . $playername ."'";
				if ($result = mysqli_query($link2, $query))
				{
					$row = mysqli_fetch_assoc($result);
					mysqli_free_result($result);
					if ($amount >= 2000 and $row["hfMagmaCubeKiller"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfMagmaCubeKiller` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpMagmaCubeKiller;
					}
				}			
			}		
			// Calcul des points et deblocage du titre PigZombie 
			$query = "SELECT `amount` FROM `Stats_kill` WHERE `type` = 'Pig_zombie' and `player` = '" . $playername ."'";
			if ($result = mysqli_query($link3, $query))
			{
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$amount = $row["amount"];
				$query = "SELECT `hfPigZombieKiller` FROM `points` WHERE `playername` = '" . $playername ."'";
				if ($result = mysqli_query($link2, $query))
				{
					$row = mysqli_fetch_assoc($result);
					mysqli_free_result($result);
					if ($amount >= 2000 and $row["hfPigZombieKiller"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfPigZombieKiller` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpPigZombieKiller;
					}
				}			
			}				
			// Calcul des points et deblocage du titre Skeleton 
			$query = "SELECT `amount` FROM `Stats_kill` WHERE `type` = 'Skeleton' and `player` = '" . $playername ."'";
			if ($result = mysqli_query($link3, $query))
			{
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$amount = $row["amount"];
				$query = "SELECT `hfSkeletonKiller` FROM `points` WHERE `playername` = '" . $playername ."'";
				if ($result = mysqli_query($link2, $query))
				{
					$row = mysqli_fetch_assoc($result);
					mysqli_free_result($result);
					if ($amount >= 2000 and $row["hfSkeletonKiller"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfSkeletonKiller` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpSkeletonKiller;
					}
				}			
			}							
			// Calcul des points et deblocage du titre Slime 
			$query = "SELECT `amount` FROM `Stats_kill` WHERE `type` = 'Slime' and `player` = '" . $playername ."'";
			if ($result = mysqli_query($link3, $query))
			{
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$amount = $row["amount"];
				$query = "SELECT `hfSlimeKiller` FROM `points` WHERE `playername` = '" . $playername ."'";
				if ($result = mysqli_query($link2, $query))
				{
					$row = mysqli_fetch_assoc($result);
					mysqli_free_result($result);
					if ($amount >= 2000 and $row["hfSlimeKiller"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfSlimeKiller` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpSlimeKiller;
					}
				}			
			}			
			// Calcul des points et deblocage du titre Wolf 
			$query = "SELECT `amount` FROM `Stats_kill` WHERE `type` = 'Wolf' and `player` = '" . $playername ."'";
			if ($result = mysqli_query($link3, $query))
			{
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$amount = $row["amount"];
				$query = "SELECT `hfWolfKiller` FROM `points` WHERE `playername` = '" . $playername ."'";
				if ($result = mysqli_query($link2, $query))
				{
					$row = mysqli_fetch_assoc($result);
					mysqli_free_result($result);
					if ($amount >= 2000 and $row["hfWolfKiller"] == 0)
					{
						$query = "UPDATE `auradata`.`points` SET `hfWolfKiller` = '1' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						$points += $hfpWolfKiller;
					}
				}			
			}
			// Deblocage des titres Ferrailleur, Orfevre, Joailler
			$query = "SELECT `id` FROM `mcmmo_users` WHERE `user` ='" . $playername ."'";
			if ($result = mysqli_query($link4, $query))
			{
				$row = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$query = "SELECT `fishing` FROM `mcmmo_skills` WHERE `user_id` = " . $row["id"];
				if ($result = mysqli_query($link4, $query))
				{
					$row = mysqli_fetch_assoc($result);
					mysqli_free_result($result);
					$amount = $row["fishing"];
					$query = "SELECT `hfFerrailleur`, `hfOrfevre`, `hfJoailler` FROM `points` WHERE `playername` = '" . $playername ."'";
					if ($result = mysqli_query($link2, $query))
					{
						$row = mysqli_fetch_assoc($result);
						mysqli_free_result($result);
						if ($amount >= 400 and $row["hfFerrailleur"] == 0)
						{
							$query = "UPDATE `auradata`.`points` SET `hfFerrailleur` = '1' WHERE `points`.`playername` = '" . $playername ."'";
							mysqli_query($link2, $query);						
						}
						if ($amount >= 800 and $row["hfOrfevre"] == 0)
						{
							$query = "UPDATE `auradata`.`points` SET `hfOrfevre` = '1' WHERE `points`.`playername` = '" . $playername ."'";
							mysqli_query($link2, $query);							
						}
						if ($amount >= 1200 and $row["hfJoailler"] == 0)
						{
							$query = "UPDATE `auradata`.`points` SET `hfJoailler` = '1' WHERE `points`.`playername` = '" . $playername ."'";
							mysqli_query($link2, $query);							
						}
					}						
				}

			}			
			// maj du total des points
			if ($points > 0)
			{
				$query = "SELECT `pointsTotal` FROM `points` WHERE `playername` = '" . $playername ."'";
				if ($result = mysqli_query($link2, $query))
				{
					$row = mysqli_fetch_assoc($result);
					mysqli_free_result($result);
					$pointsDispo = $points;
					$points += $row["pointsTotal"];
					$query = "UPDATE `auradata`.`points` SET `pointsTotal` = '" . $points . "', `pointsDispo` = '" . $pointsDispo . "' WHERE `points`.`playername` = '" . $playername ."'";
					mysqli_query($link2, $query);	
				}
			}
			// envoyer les $points a la console
			if ($points > 0)
			{
				$query = "SELECT `pointsDispo` FROM `points` WHERE `playername` = '" . $playername ."'";
				if ($result = mysqli_query($link2, $query))
				{
					$row = mysqli_fetch_assoc($result);
					mysqli_free_result($result);			
					try	
					{
						$Rcon = new MinecraftRcon;					
						$Rcon->Connect( MQ_SERVER_ADDR, MQ_SERVER_PORT, MQ_SERVER_PASS, MQ_TIMEOUT );					
						$Data = $Rcon->Command("eco give " . $playername . " " . $row["pointsDispo"]);					
						if( $Data === false )
						{
							throw new MinecraftRconException( "Failed to get command result." );
						}
						else if( StrLen( $Data ) == 0 )
						{
							throw new MinecraftRconException( "Got command result, but it's empty." );
						}
						$query = "UPDATE `auradata`.`points` SET `pointsDispo` = '0' WHERE `points`.`playername` = '" . $playername ."'";
						mysqli_query($link2, $query);
						//echo HTMLSpecialChars( $Data );
					}
					catch( MinecraftRconException $e )
					{
						//echo $e->getMessage( );
					}
					$Rcon->Disconnect( );
				}
			}
		}
		mysqli_free_result($resultGlobal);
	}
	mysqli_close($link);
	mysqli_close($link2);
	mysqli_close($link3);	
	mysqli_close($link4);	
	mysqli_close($link5);	
?>