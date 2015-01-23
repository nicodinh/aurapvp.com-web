<?php
	// Rcon
	define( 'MQ_SERVER_ADDR', 'localhost' );
	// rcon port
	define( 'MQ_SERVER_PORT', 25575 );
	// rcon password
	define( 'MQ_SERVER_PASS', '' );
	define( 'MQ_TIMEOUT', 2 );
	require '/path/to/lib/MinecraftRcon.class.php';
	try {                                                
		$Rcon = new MinecraftRcon;                                                
		$Rcon->Connect( MQ_SERVER_ADDR, MQ_SERVER_PORT, MQ_SERVER_PASS, MQ_TIMEOUT );                                                
		$Data = $Rcon->Command("save-all");
		if( $Data === false ) {
			throw new MinecraftRconException( "Failed to get command result." );
		}
		else if( StrLen($Data) == 0 ) {
			throw new MinecraftRconException( "Got command result, but it's empty." );
		}
	}
	catch(MinecraftRconException $e) {
	}
	$Rcon->Disconnect();
?>