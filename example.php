<?php

# When you cannot find any file here, install Composer first! http://getcomposer.org/
require 'vendor/autoload.php';

use \Xbox\Gamertag;

$gamertags = array(
  'Major Nelson',
  'Spartan 117'
);

echo "<table border=\"1\">";
echo "<tr><th>Gamertag</th><th>Gamerscore</th><th>Name</th></th>";
foreach($gamertags as $gamertag) {

  $gamertag = Gamertag::create($gamertag);
  echo "<tr><td>{$gamertag->gamertag}</td><td>{$gamertag->score}</td><td>{$gamertag->name}</td></tr>";

}
echo "</table>";
