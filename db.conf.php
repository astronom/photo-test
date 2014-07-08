<?php

$db = mysqli_connect('localhost', 'root', 'vagrant', 'photo-test') or die("Error " . mysqli_error($db));
return $db;