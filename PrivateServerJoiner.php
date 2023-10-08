<!-- Quickly join private servers for Roblox UWP -->
<?php
  # Just something that was made in order to learn a bit of PHP, might learn it fully eventually
  header('Cache-Control: no-cache');
  header('Pragma: no-cache')
?>

<?php if (isset($_REQUEST['exec'])) {
  checkLink();
  }
?>

<!-- The Form -->
<html><body>
<center>
<label for="game">Input Private Server or Game Link</label>
<form action=PrivateServerJoiner.php">
  <input type="text" name="txt" required/>
  <input type="submit" name="exec"/>
</form>
<pre>
  Instructions:
  1. Click on the text box
  2. Paste the link in following format -> https://www.roblox.com/games/[id]?privateServerCode=[id]

  Note: The ?privateServerCode query can be omitted!
</pre>

<?php
function checkLink() {
  $checkURL = $_REQUEST['txt'];
  # Clear queries for /game/[id]
  $urlSplit = parse_url($checkURL);
  $urlRemQuery = strtok($checkUrl, "?");
  # Grab Private Server Link Code from ?privateServerCode=
  parse_str($urlSplit['query'], $urlQuery);

  /* For debugging purposes
  print_r($urlQuery);
  echo $urlGame[4]; */

  if(array_key_exists('privateServerLinkCode', $urlQuery) == true && array_key_exists(4, $urlGame) == true) {
    header("Location: roblox://placeID=".$urlGame[4]."&linkCode=".$urlQuery['privateServerLinkCode']);
    die();
  } else if (array_key_exists('privateServerLinkCode', $urlQuery) == false && array_key_exists(4, $urlGame) == true {
    header("Location: roblox://placeID=".$urlGame[4]);
  } else {
    echo "<center><h1>This url is not supported</h1></center>";
  }
}
?>
