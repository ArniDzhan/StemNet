<?php

$report = new Report(date("j", time()), date("n", time()), date("Y", time()), 15);
$report->display();

/**
 * @author     Andrei Arnautov <arni@dream-clouds.com>
 */
class Report {

    private $array;

    /**
     * Generates salary dates for remaining year
     * 
     * @param int $currDay number from 1 to 31
     * @param int $currMonth number from 1 to 12
     * @param int $currYear number like 2013
     */
    function __construct($currDay = 30, $currMonth = 6, $currYear = 2013, $bonusDay = 15) {

        // go through remaining month of the year and get
        for ($month = $currMonth; $month <= 12; $month++) {

            // create timestamp for later reuse
            $time = mktime(0, 0, 0, $month, $bonusDay, $currYear);

            // add month name
            $this->array[$month][1] = date("F", $time);

            // add last working day of the month
            if ($checkWeekEnd = date("N", strtotime("last day of this month", $time)) - 5 > 0)
                $this->array[$month][2] = date("j", strtotime("last Friday", strtotime("last day of this month", $time)));
            else
                $this->array[$month][2] = date("j", strtotime("last day of this month", $time));

            // add bonus day
            if ($checkWeekEnd = date("N", $time) - 5 > 0)
                $this->array[$month][3] = date("j", strtotime("Wednesday", $time));
            else
                $this->array[$month][3] = date("j", $time);

            // remove salary dates if they are before current day
            if ($month == $currMonth) {
                if ($this->array[$month][2] < $currDay && $this->array[$month][3] < $currDay)
                    unset($this->array[$month]);
                else {
                    if ($this->array[$month][2] < $currDay)
                        $this->array[$month][2] = NULL;
                    if ($this->array[$month][3] < $currDay)
                        $this->array[$month][3] = NULL;
                }
            }
        }
    }

    /**
     * Exports salary dates to CSV file and triggers download
     */
    public function display() {

        // ensure file download dialog
        header("Content-type: text/csv");
        header("Cache-Control: no-store, no-cache");
        header('Content-Disposition: attachment; filename="filename.csv"');

        // export array to CSV file
        $out = fopen('php://output', 'w');
        foreach ($this->array as $fields) {
            fputcsv($out, $fields);
        }
        fclose($out);
    }

}

?>