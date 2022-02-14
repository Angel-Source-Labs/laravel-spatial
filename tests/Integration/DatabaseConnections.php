<?php

namespace Tests\Integration;

trait DatabaseConnections
{
    public $dbDriver;

    public function useMySqlConnection($app)
    {
        $this->dbDriver = 'mysql';
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.myisam' => false]);
        $app['config']->set('database.connections.mysql.host', env('DB_HOST', '127.0.0.1'));
        $app['config']->set('database.connections.mysql.port', env('DB_PORT', '33068'));
        $app['config']->set('database.connections.mysql.database', env('DB_DATABASE', 'spatial_test'));
        $app['config']->set('database.connections.mysql.username', env('DB_USERNAME', 'root'));
        $app['config']->set('database.connections.mysql.password', env('DB_PASSWORD', ''));
        $app['config']->set('database.connections.mysql.modes', [
            'ONLY_FULL_GROUP_BY',
            'STRICT_TRANS_TABLES',
            'NO_ZERO_IN_DATE',
            'NO_ZERO_DATE',
            'ERROR_FOR_DIVISION_BY_ZERO',
            'NO_ENGINE_SUBSTITUTION',
        ]);
    }

    public function useMySql57Connection($app)
    {
        $this->useMySqlConnection($app);
        $app['config']->set('database.connections.mysql.port', env('DB_PORT', '33067'));
    }

    public function usePercona57Connection($app)
    {
        $this->useMySqlConnection($app);
        $app['config']->set('database.connections.mysql.port', env('DB_PORT', '33167'));
    }

    public function usePerconaConnection($app)
    {
        $this->useMySqlConnection($app);
        $app['config']->set('database.connections.mysql.port', env('DB_PORT', '33168'));
    }

    public function useMariaDBConnection($app)
    {
        $this->useMySqlConnection($app);
        $app['config']->set('database.connections.mysql.port', env('DB_PORT', '33107'));
    }

    public function useMariaDB102Connection($app)
    {
        $this->useMySqlConnection($app);
        $app['config']->set('database.connections.mysql.port', env('DB_PORT', '33102'));
    }

    public function usePostgresConnection($app)
    {
        $this->dbDriver = 'pgsql';
        config(['database.default' => 'pgsql']);
        $app['config']->set('database.connections.pgsql.host', env('DB_HOST', '127.0.0.1'));
        $app['config']->set('database.connections.pgsql.port', env('DB_PORT', '54322'));
        $app['config']->set('database.connections.pgsql.database', env('DB_DATABASE', 'spatial_test'));
        $app['config']->set('database.connections.pgsql.username', env('DB_USERNAME', 'postgres'));
        $app['config']->set('database.connections.pgsql.password', env('DB_PASSWORD', 'password'));
    }

    protected function useSQLiteConnection($app)
    {
        $this->dbDriver = 'sqlite';
        config(['database.default' => 'testbench']);
        config(['database.connections.testbench' => [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]]);
    }

    protected function useSqlServerConnection($app)
    {
        config(['database.default' => 'sqlsrv']);
    }
}