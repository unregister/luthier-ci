<?php

/*
 * Luthier CI
 *
 * (c) 2018 Ingenia Software C.A
 *
 * This file is part of Luthier CI, a plugin for CodeIgniter 3. See the LICENSE
 * file for copyright information and license details
 */

namespace Luthier;

use Luthier\RouteBuilder as Route;

/**
 * CLI handler for Luthier CI
 * 
 * (Due security reasons, mostly commands defined here are disbled in 'production'
 * and 'testing' environments)
 * 
 * @author Anderson Salas <anderson@ingenia.me>
 */
class Cli
{
    /**
     * Registers all 'luthier make' commands
     * 
     * @return void
     */
    public static function maker()
    {
        if(ENVIRONMENT !== 'development')
        {
            return;
        }

        Route::group('luthier', function(){
            Route::group('make', function(){
                Route::cli('controller/{(.+):name}',function($name){
                    self::makeContoller($name);
                });

                Route::cli('model/{(.+):name}',function($name){
                    self::makeModel($name);
                });

                Route::cli('helper/{(.+):name}',function($name){
                    self::makeHelper($name);
                });

                Route::cli('library/{(.+):name}',function($name){
                    self::makeLibrary($name);
                });

                Route::cli('middleware/{(.+):name}',function($name){
                    self::makeMiddleware($name);
                });

                Route::cli('migration/{name}/{((sequential|timestamp)):type?}',function($name, $type = 'timestamp'){
                    self::makeMigration($name, $type);
                });

                Route::cli('auth', function(){
                    self::makeAuth();
                });
            });
        });
    }

    /**
     * Registers the 'luthier migrate' command
     * 
     * @return void
     */
    public static function migrations()
    {
        if(ENVIRONMENT !== 'development')
        {
            return;
        }

        Route::group('luthier', function(){
            Route::group('migrate', function(){
                Route::cli('{version?}',function($version = null){
                    self::migrate($version);
                });
            });
        });
    }

    /**
     * Creates a new controller
     *
     * @param  string $name Controller name
     *
     * @return void
     */
    private static function makeContoller($name, $resource = false)
    {
        // FIXME: Add a nice syntax for this (a method in the RouteBuilder class maybe?)
        $isResource = isset($_SERVER['argv'][5]) && $_SERVER['argv'][5] == '--resource';

        $dir = [];

        if(count(explode('/', $name)) > 0)
        {
            $dir  = explode('/', $name);
            $name = array_pop($dir);
        }

        $name = ucfirst($name) . 'Controller';
        $path = APPPATH . 'controllers/' . ( empty($dir) ? $name : implode('/', $dir) . '/' . $name ) . '.php';

        if(!empty($dir))
        {
            Utils::rmkdir($dir,'controllers');
        }

        if(file_exists($path))
        {
            show_error('The file already exists!');
        }

        $file = <<<CONTROLLER
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class $name extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    %CONTROLLER_BODY%
}
CONTROLLER;


        if(!$isResource)
        {
            $controllerBody ='
    /**
     * Index action
     */
    public function index()
    {

    }
';
        }
        else
        {
            $controllerBody = '
    /**
     * Index action
     */
    public function index()
    {

    }

    /**
     * Create action
     */
    public function create()
    {

    }

    /**
     * Store action
     */
    public function store()
    {

    }

    /**
     * Show action
     *
     * @param  string  $id
     */
    public function show($id)
    {

    }

    /**
     * Edit action
     *
     * @param  string  $id
     */
    public function edit($id)
    {

    }

    /**
     * Update action
     *
     * @param  string  $id
     */
    public function update($id)
    {

    }

    /**
     * Destroy action
     *
     * @param  string $id
     */
    public function destroy($id)
    {

    }
';
        }

        $file = str_ireplace('%CONTROLLER_BODY%', $controllerBody, $file);

        file_put_contents($path, $file);

        echo "\nCREATED:\n" . realpath($path) . "\n";
    }

    /**
     * Creates a new model
     *
     * @param  string $name Model name
     *
     * @return void
     */
    private static function makeModel($name)
    {
        $dir = [];

        if(count(explode('/', $name)) > 0)
        {
            $dir  = explode('/', $name);
            $name = array_pop($dir);
        }

        $name = ucfirst($name) . '_model';
        $path = APPPATH . 'models/' . ( empty($dir) ? $name : implode('/', $dir) . '/' . $name ) . '.php';

        if(!empty($dir))
        {
            Utils::rmkdir($dir,'models');
        }

        if(file_exists($path))
        {
            show_error('The file already exists!');
        }

        $file = <<<MODEL
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class $name extends CI_Model
{

}
MODEL;

        file_put_contents($path, $file);

        echo "\nCREATED:\n" . realpath($path) . "\n";
    }


    /**
     * Creates a helper
     *
     * @param  string  $name helper name
     *
     * @return void
     */
    private static function makeHelper($name)
    {
        $dir = [];

        if(count(explode('/', $name)) > 0)
        {
            $dir  = explode('/', $name);
            $name = array_pop($dir);
        }

        $name = ucfirst($name) . '_helper';
        $path = APPPATH . 'helpers/' . ( empty($dir) ? $name : implode('/', $dir) . '/' . $name ) . '.php';

        if(!empty($dir))
        {
            Utils::rmkdir($dir,'helpers');
        }

        if(file_exists($path))
        {
            show_error('The file already exists!');
        }

        $file = <<<HELPER
<?php

defined('BASEPATH') OR exit('No direct script access allowed');


HELPER;

        file_put_contents($path, $file);

        echo "\nCREATED:\n" . realpath($path) . "\n";
    }


    /**
     * Creates a middleware
     * 
     * @param string $name Middleware name
     * 
     * @return void
     */
    private static function makeMiddleware($name)
    {
        $dir = [];

        if(count(explode('/', $name)) > 0)
        {
            $dir  = explode('/', $name);
            $name = array_pop($dir);
        }

        $name = ucfirst($name) . 'Middleware';
        $path = APPPATH . 'middleware/' . ( empty($dir) ? $name : implode('/', $dir) . '/' . $name ) . '.php';

        if(!empty($dir))
        {
            Utils::rmkdir($dir,'middleware');
        }

        if(file_exists($path))
        {
            show_error('The file already exists!');
        }

        $file = <<<MIDDLEWARE
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class $name implements Luthier\MiddlewareInterface
{

    /**
     * Middleware entry point
     *
     * @return void
     */
    public function run(\$args = [])
    {

    }
}
MIDDLEWARE;

        file_put_contents($path, $file);

        echo "\nCREATED:\n" . realpath($path) . "\n";
    }

    /**
     * Creates a new library
     *
     * @param  string $name Library name
     *
     * @return void
     */
    private static function makeLibrary($name)
    {
        $dir = [];

        if(count(explode('/', $name)) > 0)
        {
            $dir  = explode('/', $name);
            $name = array_pop($dir);
        }

        $name = ucfirst($name) ;
        $path = APPPATH . 'libraries/' . ( empty($dir) ? $name : implode('/', $dir) . '/' . $name ) . '.php';

        if(!empty($dir))
        {
            Utils::rmkdir($dir,'libraries');
        }

        if(file_exists($path))
        {
            show_error('The file already exists!');
        }

        $file = <<<LIBRARY
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class $name
{

}
LIBRARY;

        file_put_contents($path, $file);

        echo "\nCREATED:\n" . realpath($path) . "\n";
    }

    /**
     * Creates a new migration
     *
     * @param  string  $name Name
     * @param  string  $type Type (sequential|date)
     *
     * @return void
     */
    private static function makeMigration($name, $type)
    {
        if(!file_exists(APPPATH . '/migrations'))
        {
            mkdir(APPPATH . '/migrations');
        }

        $name = trim(str_ireplace(' ', '_', $name));

        if($type == 'timestamp')
        {
            $filename = date('Y') . date('m') . date('d') . date('H') . date('i') . date('s') . '_' . $name;
        }
        else
        {
            $migrations = scandir(APPPATH . '/migrations');
            $last = 0;

            foreach($migrations as $migration)
            {
                if($migration == '.' || $migration == '..')
                {
                    continue;
                }

                $_number = substr($migration,0,4);

                if(preg_match('/^[0-9]{3}_$/',$_number))
                {
                    if($_number > $last)
                    {
                        $last = (int) $_number;
                    }
                }
            }
            $last++;
            $filename = str_pad($last, 3, '0', STR_PAD_LEFT) . '_' . $name;
        }

        $path = APPPATH . 'migrations/' . $filename . '.php';

        if(file_exists($path))
        {
            show_error('The file already exists!');
        }

        $file = <<<MIGRATION
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_{$name} extends CI_Migration
{
    public function up()
    {

    }

    public function down()
    {

    }
}
MIGRATION;

        file_put_contents($path, $file);

        echo "\nCREATED:\n" . realpath($path) . "\n";
    }

    /**
     * Creates all SimpleAuth required files
     * 
     * @return void
     */
    private static function makeAuth()
    {
        Utils::rcopy(__DIR__ . '/Resources/SimpleAuth/Framework', APPPATH);
    }

    /**
     * Runs a migration
     *
     * @param  string  $version (Optional)
     *
     * @return void
     */
    private static function migrate($version = null)
    {
        if($version == 'reverse')
        {
            self::migrate('0');
            return;
        }

        if($version == 'refresh')
        {
            self::migrate('0');
            self::migrate();
            return;
        }

        ci()->load->library('migration');

        $migrations = ci()->migration->find_migrations();

        $_migrationsTable = new \ReflectionProperty('CI_Migration', '_migration_table');
        $_migrationsTable->setAccessible(true);
        $_migrationsTable = $_migrationsTable->getValue(ci()->migration);

        $old = ci()->db->get($_migrationsTable)->result()[0]->version;

        $migrate = function() use($version)
        {
            if($version === null)
            {
                return ci()->migration->latest();
            }

            return ci()->migration->version($version);
        };

        $result = $migrate();

        if($result === FALSE)
        {
            show_error(ci()->migration->error_string());
        }

        $current = ci()->db->get($_migrationsTable)->result()[0]->version;

        echo "\n";

        if($old == $current)
        {
            echo "Nothing to migrate. \n";
        }
        else
        {
            $migrated   = [];
            $index      = 0;
            $migrations = $old < $current ? $migrations : array_reverse($migrations, true);
            $ascendent  = $old < $current;

            foreach($migrations as $name => $path)
            {
                if($ascendent)
                {
                    if( $current >=  $name)
                    {
                        echo 'MIGRATED: ' . basename($migrations[$name]) . "\n";
                    }
                }
                else
                {
                    if( $current <= $name)
                    {
                        echo 'REVERSED: ' . basename($migrations[$name]) . "\n";
                    }
                }
            }
        }
    }
}