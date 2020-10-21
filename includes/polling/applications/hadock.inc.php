<?php

use LibreNMS\RRD\RrdDefinition;

$name = 'hadock';
$app_id = $app['app_id'];
if (! empty($agent_data['app'][$name])) {
	$hadock = $agent_data['app'][$name];
} else {
	$hadock = snmp_get($device, '.1.3.6.1.4.1.2021.7890.104.97.100.111.99.107.3.1.2.6.104.97.100.111.99.107', '-Ovq');
	d_echo("hadock data: $hadock!!!\n");
}
$hadock = trim($hadock, '"');

echo ' hadock: ';

[$image, $science, $total, $size, $errors, $skipped, $uptime, $wait] = array_map('rtrim', explode("\n", $hadock));
d_echo("image: $image, science: $science, total: $total, size: $size, errors: $errors, skipped: $skipped\n");
d_echo("wait: $wait, uptime: $uptime\n");

$rrd_name = ['app', $name, $app_id];
$rrd_def = RrdDefinition::make()
    ->addDataset('Images', 'GAUGE', 0, 125000000000)
    ->addDataset('Sciences', 'GAUGE', 0, 125000000000)
    ->addDataset('Total', 'GAUGE', 0, 125000000000)
    ->addDataset('Size', 'GAUGE', 0, 125000000000)
    ->addDataset('Skipped', 'GAUGE', 0, 125000000000)
    ->addDataset('Errors', 'GAUGE', 0, 125000000000)
    ->addDataset('Wait', 'GAUGE', 0, 125000000000)
    ->addDataset('Uptime', 'GAUGE', 0, 125000000000);

$fields = [
    'Images'   => $image,
    'Sciences' => $science,
    'Total'    => $total,
    'Size'     => $size,
    'Skipped'  => $skipped,
    'Errors'   => $errors,
    'Uptime'   => $uptime,
    'Wait'     => $wait,
];

$tags = compact('name', 'app_id', 'rrd_name', 'rrd_def');
data_update($device, 'app', $tags, $fields);
update_application($app, $hadock, $fields);

// Unset the variables we set here
 unset($hadock, $image, $science, $total, $size, $skipped, $errors, $wait, $total, $rrd_name, $rrd_def, $tags);

