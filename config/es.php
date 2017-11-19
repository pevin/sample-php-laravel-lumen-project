<?php

$connections = [
    'default' => [
        'servers' => [
            [
                "host" => env("ELASTIC_HOST", "127.0.0.1"),
                "port" => env("ELASTIC_PORT", 9200),
                'scheme' => env('ELASTIC_SCHEME', 'http'),
            ]
        ]
    ]
];

if (env('ELASTIC_AWS')) {
    $connections['handler'] = new \Aws\ElasticsearchService\ElasticsearchPhpHandler(env('AWS_REGION'));
}

return [
    # Here you can define the default connection name.

    'default' => env('ELASTIC_CONNECTION', 'default'),

    # Here you can define your connections.

    'connections' => $connections,

    # Here you can define your indices.

    'indices' => [
        App\ES\Index\EmployeeIndex::NAME => App\ES\Index\EmployeeIndex::getIndexConfig(),
        App\ES\Index\DepartmentIndex::NAME => App\ES\Index\DepartmentIndex::getIndexConfig(),
    ]
];
