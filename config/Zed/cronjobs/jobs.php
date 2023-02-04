<?php

/**
 * Notes:
 *
 * - jobs[]['name'] must not contains spaces or any other characters, that have to be urlencode()'d
 * - jobs[]['role'] default value is 'admin'
 *
 * @TODO Use values from config/stores.php
 */


$allStores = ['DE'];

/* -- MAIL QUEUE -- */

$jobs[] = [
    'name' => 'send-mails',
    'command' => 'mailqueue:registration:send',
    'schedule' => '*/10 * * * *',
    'enable' => false,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

$jobs[] = [
    'name' => 'export-kv',
    'command' => 'collector:storage:export',
    'schedule' => '* * * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

$jobs[] = [
    'name' => 'export-search',
    'command' => 'collector:search:export',
    'schedule' => '* * * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

$jobs[] = [
    'name' => 'export-time-slot-search',
    'command' => 'collector:time-slot-search:export',
    'schedule' => '* * * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

/* Oms */
$jobs[] = [
    'name' => 'check-oms-conditions',
    'command' => 'oms:check-condition',
    'schedule' => '* * * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

$jobs[] = [
    'name' => 'check-oms-timeouts',
    'command' => 'oms:check-timeout',
    'schedule' => '* * * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

$jobs[] = [
    'name' => 'clear-oms-locks',
    'command' => 'oms:clear-locks',
    'schedule' => '0 6 * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

// Detect stuck orders
$jobs[] = [
    'name' => 'oms-detect-stuck-orders',
    'command' => 'oms:detect-stuck-orders',
    'schedule' => '0 * * * *',
    'enable' => true,
    'run_on_non_production' => false,
    'stores' => $allStores
];

/* StateMachine */
$jobs[] = [
    'name' => 'check-tour-state-machine-conditions',
    'command' => 'state-machine:check-condition Tour',
    'schedule' => '* * * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

$jobs[] = [
    'name' => 'check-tour-state-machine-timeouts',
    'command' => 'state-machine:check-timeout Tour',
    'schedule' => '* * * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

$jobs[] = [
    'name' => 'clear-state-machine-locks',
    'command' => 'state-machine:clear-locks',
    'schedule' => '0 6 * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

// Concrete Time Slots
$jobs[] = [
    'name' => 'create-concrete-time-slots',
    'command' => 'delivery-area:create:concrete-time-slots',
    'schedule' => '* * * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

// Concrete Tours
$jobs[] = [
    'name' => 'create-concrete-tours',
    'command' => 'tour:concrete:generate',
    'schedule' => '* * * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

$jobs[] = [
    'name' => 'touch-delete-passed-concrete-time-slots',
    'command' => 'delivery-area:delete-touch:passed-concrete-time-slots',
    'schedule' => '50 23 * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

// count all sold items
$jobs[] = [
    'name' => 'count-all-sold-items',
    'command' => 'product-relevance:update:count-sold-items',
    'schedule' => '0 5 * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];


// set-is-bookable-campaign
$jobs[] = [
    'name' => 'set-is-bookable-campaign',
    'command' => 'campaign:add:is-bookable',
    'schedule' => '0 5 * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

// Edifact D96a Exports
$jobs[] = [
    'name' => 'tour-edifact-export-check',
    'command' => 'tour:export:check',
    'schedule' => '*/1 * * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

$jobs[] = [
    'name' => 'tour-edifact-export',
    'command' => 'tour:export',
    'schedule' => '*/5 * * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

// Billing Period and Billing Items Generators
$jobs[] = [
    'name' => 'billing-periods-generate',
    'command' => 'billing:create:billing-periods',
    'schedule' => '0 1 * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

$jobs[] = [
    'name' => 'billing-items-generate',
    'command' => 'billing:create:billing-items',
    'schedule' => '0 2 * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

// License Invoice
$jobs[] = [
    'name' => 'realax-invoice-export-variable',
    'command' => 'realax:invoice:export:variable',
    'schedule' => '0 0 3 * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

$jobs[] = [
    'name' => 'realax-invoice-export-fix',
    'command' => 'realax:invoice:export:fix',
    'schedule' => '0 0 1 * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

// Product export check
$jobs[] = [
    'name' => 'product-export-batch',
    'command' => 'product:export:batch',
    'schedule' => '* * * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores
];

// Price import check
$jobs[] = [
    'name' => 'price-import-batch',
    'command' => 'price:import:batch',
    'schedule' => '* * * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores
];

// Integra / GBZ
$jobs[] = [
    'name' => 'integra-export-open-orders',
    'command' => 'integra:export:open-orders',
    'schedule' => '*/15 * * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores
];

$jobs[] = [
    'name' => 'integra-export-closed-orders',
    'command' => 'integra:export:closed-orders',
    'schedule' => '*/15 * * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores
];

$jobs[] = [
    'name' => 'integra-import-orders',
    'command' => 'integra:import:orders',
    'schedule' => '*/15 * * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores
];

// Graphmasters Time-slot creator
$jobs[] = [
    'name' => 'graphmasters-gm-time-slot-generate',
    'command' => 'graphmasters:time-slots:generate',
    'schedule' => '0 2 * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

// Graphmasters Time-slot collector
$jobs[] = [
    'name' => 'export-gm-time-slot-search',
    'command' => 'collector:gm-time-slot-search:export',
    'schedule' => '*/5 * * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

// Graphmasters
$jobs[] = [
    'name' => 'graphmasters-tour-import',
    'command' => 'graphmasters:tour:import',
    'schedule' => '*/5 * * * *',
    'enable' => false,
    'run_on_non_production' => true,
    'stores' => $allStores,
];

// Graphmasters fix tours where cutoff reached
$jobs[] = [
    'name' => 'graphmasters-tours-fix-cutoff-reached',
    'command' => 'graphmasters:tours:fix:cutoff-reached',
    'schedule' => '*/5 * * * *',
    'enable' => true,
    'run_on_non_production' => true,
    'stores' => $allStores,
];
