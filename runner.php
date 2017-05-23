<?php

/***
 * google play fetch runner
 * @author Lev Savranskiy <lev.savranskiy@gmail.com>
 */


set_time_limit(0);

$start  = 0;
//enter  row count
$total  = 4200759;
$step   = 16000;

$path = '/home/username/google-packages';
for (;$start < $total; $start  = $start + $step  ){
    $end  = $start + $step;
    $cmd   = "nohup /usr/bin/php $path/parseparams.php $start $end >$path/logs/data_{$start}_{$end}.csv &";
    echo $cmd;
    echo PHP_EOL;
    shell_exec($cmd);

}