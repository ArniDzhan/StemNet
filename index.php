<?php

// ensure file download dialog
header("Content-type: text/csv");  
header("Cache-Control: no-store, no-cache");  
header('Content-Disposition: attachment; filename="filename.csv"'); 

// set variables for later reuse
$year = date("Y", time());
$bonusDay = 15;

$currMonth = date("n", time());
$currDay = date("j", time());

// go through remaining month of the year and get
for($month = $currMonth; $month<=12; $month++){
    
    // create timestamp for later reuse
    $time = mktime(0, 0, 0, $month, $bonusDay, $year);
    
    // add month name
    $array[$month][1] = date("F", $time);
    
    // add last working day of the month
    if ($checkWeekEnd = date("N", strtotime("last day of this month", $time)) - 5 > 0) $array[$month][2] = date("j", strtotime("last Friday", strtotime("last day of this month", $time)));
    else $array[$month][2] = date("j", strtotime("last day of this month", $time));
    
    // add bonus day
    if ($checkWeekEnd = date("N", $time) - 5 > 0) $array[$month][3] = date("j", strtotime("Wednesday", $time));
    else $array[$month][3] = date("j", $time);
    
    // remove salary dates if they are before current day
    if($month == $currMonth) {
        if($array[$month][2]<$currDay && $array[$month][3]<$currDay) unset ($array[$month]);
        else{
            if($array[$month][2]<$currDay) $array[$month][2] = NULL;
            if($array[$month][3]<$currDay) $array[$month][3] = NULL;
        }            
    }
}


// export array to CSV file
$out = fopen('php://output', 'w');
foreach ($array as $fields) {
    fputcsv($out, $fields);
}
fclose($out);
 
?>
