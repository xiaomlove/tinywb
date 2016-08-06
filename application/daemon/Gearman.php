<?php
$worker = new GearmanWorker();
$worker->addServer();

$worker->addFunction('test', function(GearmanJob $job) {
   $workload = $job->workload();
   echo "workload: $workload \n";
});

while ($worker->work());