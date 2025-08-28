<?php
defined('MOODLE_INTERNAL') || die();

$tasks = [
    [
        'classname' => '\block_myxp\task\backfill_xp',
        'blocking'  => 0,
        'minute'    => 'R',
        'hour'      => '3',
        'day'       => '*',
        'dayofweek' => '*',
        'month'     => '*'
    ],
];
