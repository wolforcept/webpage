<html>
	<head>
		<?php
			include 'head.php';
			session_start();
		?>
		<style type="text/css" media="all"> 
			@import "minecraft_style.css";
		</style>
	</head>
	
	<body>
		<?php include 'navbar.php';?>

		<div id="page">
			<div id="filters" class="generic_container">
				<form id="filterForm">
					Online Now? 
					<input type="checkbox" name="status" value="online" <? if($_GET['status'] == 'online') { echo 'checked'; } ?>>				
					&nbsp;&nbsp;&nbsp;&nbsp;
					Game Type:
					<select name="GameType">
						<option value="any" <? if($_GET['GameType'] == 'any') { echo 'selected'; } ?>>Any</option>
						<option value="SMP" <? if($_GET['GameType'] == 'SMP') { echo 'selected'; } ?>>SMP</option>
					</select>
					&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="submit" value="Filter" />
					<a onclick="submitFilter()">Filter </a>
				</form>
			</div>
			<div id="info" class="generic_container">
				<a href="?reload=true">reload</a>
				<?
					$Ragecraft = isset($_SESSION ['Ragecraft'] ) ? 
						$_SESSION ['Ragecraft'] : 
						new MinecraftServer('wolforce.no-ip.biz', '25565', 'Ragecraft', 'Lisboa, Portugal', 'mc1.png');
						
					$Raffa = isset( $_SESSION ['Red Rafa'] ) ?
						$_SESSION ['Red Rafa'] :
						new MinecraftServer('adnecrias.servegame.com', '8085', 'Red Rafa', 'Lisboa, Portugal', 'mc2.png', 'Yogscast Complete Pack');
					
					// $Emptyclouds = new MinecraftServer('emptyclouds.no-ip.org', '25565', 'EmptyClouds', 'Lisboa, Portugal', 'mc3.png');
					
					$Servers = Array ( $Ragecraft, $Raffa ); //, $Emptyclouds );
					
					foreach( $Servers as $s ){
						if ( $_GET["reload"] ){
							$s -> connect();
						}
						$s->show();
						echo '<br />';
					}
					
				?>
			</div>
			
			<div id="helps" class="generic_container" align="center">
				<a href="http://i.imgur.com/AcOny.png" >This has helped me </a>
			</div>
			
			<?php include 'footer.php'; ?>
		</div>
	</body>
</html>

<?php

	class MinecraftServer {
	
		private $Address;
		private $Port;
		
		private $Name;
		private $Location;
		private $Image;
		private $Modpack;
		private $Mods;
		private $Players;
		
		function __construct( $address, $port, $name, $location, $image, $modpack = 'no' ) {
			$this->Address = $address;
			$this->Port = $port;
			
			$this->Name = $name;
			$this->Location = $location;
			$this->Image = $image;
			$this->Modpack = $modpack;
		}
		
		public function connect () {
			try	{
				$Query = new MinecraftQuery( );
				$Query->Connect( $this->Address, $this->Port );
				$Players = $Query->GetPlayers( );
				$Mods = $Query->GetPlayers( );
				$Info = $Query->GetInfo( );
				
				$Online = true;
			}
			catch( MinecraftQueryException $e ) {
				$Online = false;
			}
			return;
		}
		
		public function show () {
			
			$AtLeastOne = false;
			if ($Online) {
				//$Query->Connect( $this->Address, $this->Port );
				//$Players = $Query->GetPlayers( );
				//$Mods = $Query->GetPlayers( );
				//$Info = $Query->GetInfo( );
				
				if(!isset($_GET['GameType']) || $Info['GameType'] == $_GET['GameType'] || $_GET['GameType']=='any'){
					echo '<table class="info"><tr><td>'; //image
					echo '<a onclick="change()"><img src="'.$this->Image.'" width="64" height="64"></img></a>';
					echo '</td> <td>'; //info
					echo 'The '.$this->Name.' Server is ';
					echo '<font class="online">online</font>.<br />';				
					echo '<font class="server_address">';
					echo $this->Address.' : '.$this->Port.'<br />';
					echo '</td> </tr> </table>';

					echo '<br /><font class="server_description">';

					echo '<table class="lists"> <tr> <td>';
					if(count($Players) > 0){
						echo 'Online Players Now:';
						foreach( $Players as $p ){
							echo '<br />'.$p ;
						}
					} else {
						echo 'No Players Now.';
					}
					echo '</td>	<td>';
					echo 'Location: '.$this->Location.'<br />';
					echo 'Version: '.$Info['Version'].'<br />';
					echo 'GameType: '.$Info['GameType'].'<br />';
					if($this->Modpack == 'no'){ echo 'No Modpack'; }
					else { echo 'Modpack: '.$this->Modpack.'<br />'; }
					echo '</td> </tr> </font> </table>';
				}
			} else {
				echo '<table class="info"><tr><td>';
				echo '<img src="'.$this->Image.'" width="64" height="64"></img>';
				echo '</td> <td>';
				echo 'The '.$this->Name.' Server is <font class="offline">offline</font>.<br />';
				echo '<font class="server_address">';
				echo $this->Address.' : '.$this->Port.'<br />';
				echo '</td> </tr> </table>';
			}
		}
	}
	class MinecraftQueryException extends Exception
	{
		// Exception thrown by MinecraftQuery class
	}

	class MinecraftQuery
	{		
		/*
		 * Class written by xPaw
		 *
		 * Website: http://xpaw.ru
		 * GitHub: https://github.com/xPaw/PHP-Minecraft-Query
		 */

		const STATISTIC = 0x00;
		const HANDSHAKE = 0x09;

		private $Socket;
		private $Players;
		private $Info;

		public function Connect( $Ip, $Port = 25565, $Timeout = 3 )
		{
			if( !is_int( $Timeout ) || $Timeout < 0 )
			{
				throw new InvalidArgumentException( 'Timeout must be an integer.' );
			}

			$this->Socket = @FSockOpen( 'udp://' . $Ip, (int)$Port, $ErrNo, $ErrStr, $Timeout );

			if( $ErrNo || $this->Socket === false )
			{
				throw new MinecraftQueryException( 'Could not create socket: ' . $ErrStr );
			}

			Stream_Set_Timeout( $this->Socket, $Timeout );
			Stream_Set_Blocking( $this->Socket, true );

			try
			{
				$Challenge = $this->GetChallenge( );

				$this->GetStatus( $Challenge );
			}
			// We catch this because we want to close the socket, not very elegant
			catch( MinecraftQueryException $e )
			{
				FClose( $this->Socket );

				throw new MinecraftQueryException( $e->getMessage( ) );
			}

			FClose( $this->Socket );
		}

		public function GetInfo( )
		{
			return isset( $this->Info ) ? $this->Info : false;
		}

		public function GetPlayers( )
		{
			return isset( $this->Players ) ? $this->Players : false;
		}

		private function GetChallenge( )
		{
			$Data = $this->WriteData( self :: HANDSHAKE );

			if( $Data === false )
			{
				throw new MinecraftQueryException( 'Failed to receive challenge.' );
			}

			return Pack( 'N', $Data );
		}

		private function GetStatus( $Challenge )
		{
			$Data = $this->WriteData( self :: STATISTIC, $Challenge . Pack( 'c*', 0x00, 0x00, 0x00, 0x00 ) );

			if( !$Data )
			{
				throw new MinecraftQueryException( 'Failed to receive status.' );
			}

			$Last = '';
			$Info = Array( );

			$Data    = SubStr( $Data, 11 ); // splitnum + 2 int
			$Data    = Explode( "\x00\x00\x01player_\x00\x00", $Data );

			if( Count( $Data ) !== 2 )
			{
				throw new MinecraftQueryException( 'Failed to parse server\'s response.' );
			}

			$Players = SubStr( $Data[ 1 ], 0, -2 );
			$Data    = Explode( "\x00", $Data[ 0 ] );

			// Array with known keys in order to validate the result
			// It can happen that server sends custom strings containing bad things (who can know!)
			$Keys = Array(
				'hostname'   => 'HostName',
				'gametype'   => 'GameType',
				'version'    => 'Version',
				'plugins'    => 'Plugins',
				'map'        => 'Map',
				'numplayers' => 'Players',
				'maxplayers' => 'MaxPlayers',
				'hostport'   => 'HostPort',
				'hostip'     => 'HostIp'
			);

			foreach( $Data as $Key => $Value )
			{
				if( ~$Key & 1 )
				{
					if( !Array_Key_Exists( $Value, $Keys ) )
					{
						$Last = false;
						continue;
					}

					$Last = $Keys[ $Value ];
					$Info[ $Last ] = '';
				}
				else if( $Last != false )
				{
					$Info[ $Last ] = $Value;
				}
			}

			// Ints
			$Info[ 'Players' ]    = IntVal( $Info[ 'Players' ] );
			$Info[ 'MaxPlayers' ] = IntVal( $Info[ 'MaxPlayers' ] );
			$Info[ 'HostPort' ]   = IntVal( $Info[ 'HostPort' ] );

			// Parse "plugins", if any
			if( $Info[ 'Plugins' ] )
			{
				$Data = Explode( ": ", $Info[ 'Plugins' ], 2 );

				$Info[ 'RawPlugins' ] = $Info[ 'Plugins' ];
				$Info[ 'Software' ]   = $Data[ 0 ];

				if( Count( $Data ) == 2 )
				{
					$Info[ 'Plugins' ] = Explode( "; ", $Data[ 1 ] );
				}
			}
			else
			{
				$Info[ 'Software' ] = 'Vanilla';
			}

			$this->Info = $Info;

			if( $Players )
			{
				$this->Players = Explode( "\x00", $Players );
			}
		}

		private function WriteData( $Command, $Append = "" )
		{
			$Command = Pack( 'c*', 0xFE, 0xFD, $Command, 0x01, 0x02, 0x03, 0x04 ) . $Append;
			$Length  = StrLen( $Command );

			if( $Length !== FWrite( $this->Socket, $Command, $Length ) )
			{
				throw new MinecraftQueryException( "Failed to write on socket." );
			}

			$Data = FRead( $this->Socket, 2048 );

			if( $Data === false )
			{
				throw new MinecraftQueryException( "Failed to read from socket." );
			}

			if( StrLen( $Data ) < 5 || $Data[ 0 ] != $Command[ 2 ] )
			{
				return false;
			}

			return SubStr( $Data, 5 );
		}
	}
?>