<?php

//Default values for the Pastable traits
return [
    //The default chunk size (limit)
    'chunkSize' => 1000,
    //Auto-create tables, if not existing
    'autoCreate' => false,
    //Do you need logging?
    'logging' => [
        'enabled' => false,
        'level' => null,
    ],
];
