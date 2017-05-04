<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDO Fetch Style
    |--------------------------------------------------------------------------
    |
    | By default, database results will be returned as instances of the PHP
    | stdClass object; however, you may desire to retrieve records in an
    | array format for simplicity. Here you can tweak the fetch style.
    |
    */

    'fetch' => PDO::FETCH_OBJ,

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'hospitals-mysql' => [
            'driver' => 'mysql',
            'host' => 'us-cdbr-iron-east-04.cleardb.net',
            'port' => '3306',
            'database' => 'heroku_07b2ee1bdcc6603',
            'username' => 'b6945bac0598ec',
            'password' => '73ea1cb0',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',

            'select' => [

            ],
            'from' => 'hospitals',
            'join' => [
            ],
            'where' => [
            ],
            'order' => [
            ],
            'limit' => '',
            'groupBy' => ''
        ],

        'hospitals-mongo' => [
            'driver'   => 'mongodb',
            'host'     => 'ds119768.mlab.com',
            'port'     => '19768',
            'database' => 'heroku_mnk5ppkt',
            'username' => 'teste',
            'password' => '12345678',
            'options'  => [
                'database' => 'heroku_mnk5ppkt'
            ],

            'select' => [

            ],
            'collection' => 'hospitals',
            'join' => [

            ],
            'where' => [
            
            ],
            'order' => [

            ],
            'limit' => ''
        ],

        'teste-post' => [
            'driver' => 'pgsql',
            'host' => 'ec2-23-23-228-115.compute-1.amazonaws.com',
            'port' => '5432',
            'database' => 'd736umlve9c426',
            'username' => 'mydnosilrzztoa',
            'password' => 'TbdHSFy3HpfsDkEsK4ivqp14Z2',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',

            'select' => [

            ],
            'from' => 'hospitals',
            'join' => [

            ],
            'where' => [
            
            ],
            'order' => [

            ],
            'limit' => ''
        ],

        'neo4j' => [
            'driver' => 'neo4j',
            'host'   => 'hobby-fcefkdlhoeaggbkekcbjceol.dbs.graphenedb.com',
            'port'   => '24789',
            'username' => 'app60260246-wZyi04',
            'password' => 'b.ijypNIBXjpdX.UwsXleHmdzIpEz2L',

            'select' => [

            ],
            'from' => 'Person',
            'join' => [

            ],
            'where' => [
            
            ],
            'order' => [

            ],
            'limit' => ''
        ],

        'voltdb' => [
            'driver' => 'voltdb',
            'host' => 'localhost',
            'username' => '',
            'password' => '',
            'port' => 21212
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'cluster' => false,

        'teste-redis' => [
            'host' => 'ec2-54-243-141-208.compute-1.amazonaws.com',
            'password' => 'p415e6129f28e1d06b67223fa30230644bd41ff4b1c1528e23abe54c37c8dd526',
            'port' => '9989',
            'database' => 0,
            'type' => 'list'
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations'

];
