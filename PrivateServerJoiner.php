<!--
This is for use with a MITM Proxy capable of redirecting/rewriting a request to either a locally running server or to another place
You can use this just to be able to join Private Servers easier on UWP (mostly MS Store UWP) but it can also work on devices that can't do it yet.
The way share links work is that they will check for protocol & user-agent, if they see it works then you will be prompted to open it in the app.
If not, it will redirect you to either:
- Web Client
- Google Play Store
- Apple Store (apparently iOS check altogether doesn't work, it redirects either way to Apple Store?)
...for the rest, not sure. But there's that. We are essentially using that to our advantage and just redirecting in-game browser to it afterwards, thus site itself will prompt us with valid protocol,
which means it's just a matter of clicking "Yes" and we are in.

The only reason why a MITM proxy is required for this is for it to be displayed in app. Otherwise you can execute this from a web page and it will work.

Uses MUI Framework for Material Design-like styling. Code was created more as a way to learn some aspects of PHP and experiment with my googling skills so major thanks to countless stackoverflow threads and
reddit posts for being super helpful in this journey!
-->
<?php
	/* 
	Debugging Headers
	Please do not enable these if you don't need them,
	as they won't provide you with any info unless you're actively
	modifying this code
	
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	*/
?>

<?php
	if (isset($_REQUEST['exec'])) {
		checkLink();
	}
?>

<!-- HTML document start -->
<!DOCTYPE html>	
<html>
<!-- Header -->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="//cdn.muicss.com/mui-0.10.3/css/mui.min.css" rel="stylesheet" type="text/css" />
		<script src="//cdn.muicss.com/mui-0.10.3/js/mui.min.js"></script>
	</head>
	
	<body style="background-color:#FFFFFFF">
	
		<div class="mui-appbar">
			<table width="100%">
				<tr style="vertical-align:middle;">
					<td class="mui--appbar-height" align="center"><font color="#FFFFFF">UWP In-Game Joiner</font></td>
				</tr>
			</table>
		</div>
		<!-- Notice for people running this in browser -->
		<title>Load this in UWP Roblox</title>
		
		<div class="mui--z1">
			<div class="mui-panel">
			<center>
			Paste any valid Private Server or Game Link and then press Join!
			<form class="mui-form--inline" align="center" action="PrivateServerJoiner.php">
				<div class="mui-textfield">
					<input type="text" name="txt" placeholder="roblox.com/games/1" align="center" required/>
				</div>
					<button class="mui-btn mui-btn--primary" type="submit" align="center" name="exec">Join</button>
			</form>
			</center>
			</div>
		</div>
	</body>
</html>
<!-- HTML document end -->

<?php
function checkLink() {
	class NotACodeLink extends Exception {};
	$checkURL = $_REQUEST['txt'];

	# Try-Catch statement just in case it fails it will recover from it.
	try {
		# Parse URL so we can extract data from it
		$urlNewCheck = parse_url($checkURL);
		debug_to_console("[Info] Checking if URL is a new PS link");
		parse_str($urlNewCheck['query'], $urlNewQuery);
		
		if(array_key_exists('code', $urlNewQuery) == true) {
			
			# This is ugly but I don't care tbf
			if (array_key_exists('host', $urlNewCheck) == false) {
				$urlNewCheck['scheme'] = 'https';
				$urlNewCheck['host'] = "www.roblox.com";
				$urlNewCheck['path'] = "/share-links";
			}
			$urlRetargeting = $urlNewCheck['scheme']."://". $urlNewCheck['host'].$urlNewCheck['path']."?code=".$urlNewQuery['code']."&type=".$urlNewQuery['type'];
			
			redirect($urlRetargeting);
		}
	}
	catch (NotACodeLink $ex) {
		debug_to_console("[Warning] URL provided is not a new PS link");
	}
	
	/*
	This continues in case the private server link is the old variant, it's a game link or it's invalid
	By old, this implies the link is roblox.com/game/id?query or roblox.com/game/id
	*/
	debug_to_console("[Info] Checking if URL is either a Game or PS link");
	$urlCheckOld = parse_url($checkURL);

	# Extract Place ID from URL
	
	$urlPlaceID = explode('/', $urlCheckOld['path']);
	
	if(array_key_exists('privateServerLinkCode', $urlCheckOld) == true && array_key_exists(2, $urlPlaceID) == true) {
			header("Location: roblox://placeID=".$urlPlaceID[2]."&linkCode=".$urlCheckOld['privateServerLinkCode']);
			die();
	} else if (array_key_exists('privateServerLinkCode', $urlCheckOld) == false && array_key_exists(2, $urlPlaceID) == true) {
			header("Location: roblox://placeID=".$urlPlaceID[2]);
			die();
	} else {
		debug_to_console("[Critical] Unsupported URL or Invalid Parameter(s)!");
		echo "<div class='mui--bg-danger' style='height:20px;' align='center' text-align='center'><font color='#FFFFF0'>An error has occured, check developer console for more details.</font></div>";
	}
}
?>

<?php
function redirect($urlRetargeting) {
	# I'm too lazy to learn how to build a full url with query so ye lol
	
	header('Location: '.$urlRetargeting);
	die();
}
?>

<?php
function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Join Worker: " . $output . "' );</script>";
}
?>
