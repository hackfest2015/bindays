<?php
date_default_timezone_set('Australia/Brisbane');

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
// Create some arrays to use for random data

print_r("Generating some random loads \n\r");

for($i=0;$i<100;$i++) {
    $load[] = rand(0,55)+30; // base weight of 30kg plus variable load of up to 55kg
}
shuffle($load);

print_r("Generating some time shifters \n\r");

$shift = array(-8,-5,-3,-2,-1,0,1,2,3,5,8);
shuffle($shift);


/********************************************************************************/
// Get bin rounds

$q = "SELECT DISTINCT Round FROM SCC_source";
$r = mysql_query($q, $db);
while($row = mysql_fetch_row($r)){
     $rounds[] = $row[0];
}

foreach($rounds as $round) {
    print_r("Simulating 16 weeks of bin collections for round $round \n\r");
    /********************************************************************************/
    // Get properties in round 
    
    $q = "SELECT `Property_Number`,`Latitude`,`Longitude`,`CollectionDay` FROM SCC_source WHERE Round=$round";
    $r = mysql_query($q, $db);
    while($row = mysql_fetch_row($r)){
        $properties[] = $row;
    }

    /********************************************************************************/
    // Calculate dates and sequence, alternate yellow bin with green bin

    $cday = $properties[0][3];
    switch ($cday) {
        case "MONDAY":
        case "Monday":
        case "monday":
            $nday = 1;
        break;
        case "TUESDAY":
        case "Tuesday":
        case "Tuesday":
            $nday = 2;
        break;
        case "WEDNESDAY":
        case "Wednesday":
        case "wednesday":
            $nday = 3;
        break;
        case "THURSDAY":
        case "Thursday":
        case "thursday":
            $nday = 4;
        break;
        case "FRIDAY":
        case "Friday":
        case "friday":
            $nday = 5;
        break;
    };

    
    for($i=32;$i<48;$i++) {

        $rundate = new DateTime();
        $rundate->setISODate(2015,$i,$nday);            // year , week num , day
        $rundate->setTime(06,rand(-5,10),rand(0,30));   // reset and randomise time to for run start
        $j = 0;                                         // reset time shifter
        $date = $rundate->format('Y-m-d');
    
        /********************************************************************************/
        // Simulate a general waste run starting at ~06:00 and shift 30s +/- (0-8) per pickup
        
        $truckid = rand(1,27);                          // no idea which truck; just randomise;
        $bintype = 1;                                   // static for now; would come off QR code
        $binsize = 240;                                 // determined by bin type
        
        foreach($properties as $property) {
            //echo ".";
            $random = '+'.$shift[$j].' seconds';
            $rundate->modify($random);
            $rundate->modify('+30 seconds');
            $time = $rundate->format('H:i:s');
            $thisload = $load[$j]*1000;
            $thisprop = $property['0'];
            $thisQR = $thisprop."-".$bintype;
            $thislat = $property['1'];
            $thislong = $property['2'];
            
            $q = "INSERT INTO BinLogs VALUES (uuid_short(),'$date','$time',$truckid,$thislat,$thislong,'$thisQR',$thisprop,$bintype,$binsize,$thisload,null,null)";
            mysql_query($q, $db);

            $j++; if (($j+1)>count($shift)) {               // reset shifter once it exceeds the available array length;
                $j = 0;
                shuffle($shift);
                shuffle($load);
            };
            
        };

        /********************************************************************************/
        // Simulate a recycling waste run starting at ~07:00 and shift 30s +/- (0-8) per pickup
        
        $rundate->setTime(07,rand(-5,10),rand(0,30));   // reset and randomise time to for run start
        $truckid = rand(1,27);                          // no idea which truck; just randomise;
        $bintype = 4;                                   // static for now; would come off QR code
        $binsize = 240;                                 // determined by bin type
        
        if($i%2==0) {                                   // Recycling every fortnight only
            foreach($properties as $property) {
                //echo ".";
                $random = '+'.$shift[$j].' seconds';
                $rundate->modify($random);
                $rundate->modify('+30 seconds');
                $time = $rundate->format('H:i:s');
                $thisload = $load[$j]*500;              // recycling is not as heavy
                $thisprop = $property['0'];
                $thisQR = $thisprop."-".$bintype;
                $thislat = $property['1'];
                $thislong = $property['2'];

                $q = "INSERT INTO BinLogs VALUES (uuid_short(),'$date','$time',$truckid,$thislat,$thislong,'$thisQR',$thisprop,$bintype,$binsize,$thisload,null,null)";
                mysql_query($q, $db);

                $j++; if (($j+1)>count($shift)) {               // reset shifter once it exceeds the available array length;
                    $j = 0;
                    shuffle($shift);
                    shuffle($load);
                };

            };
        };
    };
    unset($properties);
};

?>
