<?php

//Default values for the Pastable traits
return [
    //The default cut&paste chunk size (limit)
    'chunkSize' => 1000,
    //Auto-create tables, if not existing
    'autoCreate' => false,
    //Enable detailed logging to any accepted and configured level
    'logging' => [
        'enabled' => false,
        'level' => null,
    ],
];
