<?php

return [
    'default' => 'beanstalkd',

    'connections' => [

        'beanstalkd' => [
            'driver'      => 'beanstalkd',
            'host'        => '127.0.0.1',
            'port'        => '11300',
            'queue'       => 'default',
            'timeout'     => 90,
            'retry_after' => 90,
            'persistent'  => true,
        ],

    ],
];
