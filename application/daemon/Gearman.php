<?php
$worker = new GearmanWorker();
$worker->addServer('127.0.0.1', 4730);

$worker->addFunction('test', function(GearmanJob $job) {
   $workload = $job->workload();
   echo "workload: $workload \n";
});

while ($worker->work());