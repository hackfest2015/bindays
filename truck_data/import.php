<?php

$user = 'root';
$password = 'root';
$database = 'bindays';
$socket = 'localhost:/Applications/MAMP/tmp/mysql/mysql.sock';

$db = mysql_connect(
   $socket, 
   $user, 
   $password
);
$db_select = mysql_select_db(
   $database, 
   $db
);

/********************************************************************************/
// Parameters: filename.csv table_name

$argv = $_SERVER[argv];

if($argv[1]) { $file = $argv[1]; }
else {
    echo "Please provide a file name\n"; exit; 
}
if($argv[2]) { $table = $argv[2]; }
else {
    $table = pathinfo($file);
    $table = $table['filename'];
}

/********************************************************************************/
// Get the first row to create the column headings

$fp = fopen($file, 'r');
$frow = fgetcsv($fp);

foreach($frow as $column) {
    if($columns) $columns .= ', ';
    $columns .= "`$column` varchar(250)";
}

$create = "create table if not exists $table ($columns);";
mysql_query($create, $db);

/********************************************************************************/
// Import the data into the newly created table.

$file = $_SERVER['PWD'].'/'.$file;
$q = "load data infile '$file' into table $table fields terminated by ',' ignore 1 lines";
mysql_query($q, $db);

?>
