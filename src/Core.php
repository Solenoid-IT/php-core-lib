<?php



namespace Solenoid\Core;



use \Solenoid\Core\Routing\Destination;
use \Solenoid\Core\Routing\Router;
use \Solenoid\Core\MVC\Model;
use \Solenoid\Core\MVC\View;
use \Solenoid\Core\Env;

use \Solenoid\HTTP\Request;
use \Solenoid\Log\Logger;
use \Solenoid\Tasker\Tasker;



class Core
{
    private static Core                                   $instance;



    public static int                           $creation_timestamp;

    public static string                                   $basedir;

    public static ?Request                                 $request;
    public static array                                  $path_args;

    public static string                                   $context;

    public static string                                    $app_id;
    public static string                               $app_version;

    public static string                                      $host;

    public static string                                  $env_type;
    public static Env                                          $env;

    public static Store                                      $store;

    public static array                                    $loggers;

    public static Gate                                        $gate;
    public static Middleware                            $middleware;

    public static Model                                      $model;
    public static Service                                  $service;

    public static View                                        $view;

    public static Blade                                      $blade;

    public static string                                   $gate_ns;

    public static array                          $middleware_groups;

    public static array                                     $routes;
    public static ?Destination                      $fallback_route;

    public static array                                 $route_tags;

    public static Tasker                                    $tasker;



    # Returns [self]
    private function __construct (array $config)
    {
        // (Getting the value)
        self::$creation_timestamp = time();



        // (Getting the value)
        $request = Request::read();



        // (Getting the values)
        self::$request     = $request ? $request : null;
        self::$context     = self::$request ? 'http' : 'cli';
        self::$basedir     = $config['basedir'];
        self::$app_id      = $config['app_id'];
        self::$app_version = $config['app_version'];



        // (Getting the value)
        self::$host = self::get_host();



        foreach ($config['envs'] as $env_type => $env)
        {// Processing each entry
            if ( in_array( self::$host, $env->hosts ) )
            {// Match OK
                // (Getting the values)
                self::$env_type = $env_type;
                self::$env      = $env;



                // Breaking the iteration
                break;
            }
        }



        if ( !isset( self::$env ) )
        {// Value not found
            // (Setting the value)
            self::$env_type = 'dev';

            // (Creating an Env)
            self::$env = Env::create( Env::TYPE_DEV, [ 'dev.app' ] );
        }



        // (Setting the value)
        $loggers = [];

        // (Getting the values)
        $loggers['error'] = Logger::create( $config['logs'][ self::$context ]['error'] );
        $loggers['call']  = Logger::create( $config['logs'][ self::$context ]['call'] );



        // (Getting the value)
        self::$loggers = $loggers;



        // (Getting the values)
        self::$store   = Store::create( $this );
        self::$model   = Model::create( $this );
        self::$service = Service::create( $this );



        if ( $config['timezone'] )
        {// Value found
            // (Setting the value)
            date_default_timezone_set( $config['timezone'] );
        }



        // (Setting the error reporting)
        error_reporting( E_ERROR );



        switch ( self::$env->type )
        {
            case 'dev':
                switch ( self::$context )
                {
                    case 'http':
                        // (Setting the ini)
                        ini_set( 'display_errors', 1 );
                        ini_set( 'display_startup_errors', 1 );
                    break;

                    case 'cli':
                        // (Doing nothing)
                    break;
                }
            break;

            case 'prod':
                // (Setting the ini)
                ini_set( 'display_errors', 0 );
                ini_set( 'display_startup_errors', 0 );
            break;
        }



        if ( self::$context === 'http' )
        {// Match OK
            // (Getting the values)
            self::$gate       = Gate::create( $this );
            self::$middleware = Middleware::create( $this );

            self::$view       = View::create( $this );

            self::$blade      = Blade::create( $config['blade']['views'], $config['blade']['cache'], self::$env->type !== 'prod' );
        }



        // (Initializing a Tasker)
        self::$tasker = Tasker::init( self::$basedir, self::$loggers['error'], self::$loggers['call'] );



        // (Setting the value)
        ini_set( 'log_errors_max_len', '0' );
    }



    # Returns [void]
    public static function init (array $config)
    {
        if ( !isset( self::$instance ) )
        {// Value not found
            // (Creating a Core)
            self::$instance = new Core( $config );
        }
    }



    # Returns [void]
    public static function set_routes (array $routes, ?Destination $fallback_route = null)
    {
        // (Getting the values)
        self::$routes         = $routes;
        self::$fallback_route = $fallback_route;
    }

    # Returns [void]
    public static function set_gate (string $gate)
    {
        // (Getting the value)
        self::$gate_ns = $gate;
    }

    # Returns [void]
    public static function set_middlewares (array $groups)
    {
        // (Getting the value)
        self::$middleware_groups = $groups;
    }



    # Returns [bool] | Throws [Exception]
    public static function resolve_path ()
    {
        if ( !Router::create( self::$instance )->resolve_destination() )
        {// (Unable to resolve the destination)
            // (Setting the value)
            $message = "Unable to resolve the destination";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return true;
    }



    # Returns [string|false]
    public static function get_host ()
    {
        // Returning the value
        return Request::exists() ? Request::get_host() : gethostname();
    }



    # Returns [string]
    public static function asset (string $path, bool $static = false)
    {
        if ( $static )
        {// Value is true
            // Returning the value
            return $path;
        }



        // (Getting the value)
        $asset_path = $path;

        switch ( self::$env->type )
        {
            case 'dev':
                // (Getting the value)
                $asset_path = $path . '?ts=' . self::$creation_timestamp;
            break;

            case 'prod':
                // (Getting the value)
                $asset_path = $path . '?v=' . self::$app_version;
            break;
        }



        // Returning the value
        return $asset_path;
    }



    # Returns [void]
    public static function display_errors (bool $state)
    {
        // (Setting the ini)
        ini_set( 'display_errors', ( $state ? 1 : 0 ) );
    }



    # Returns [assoc]
    public static function to_array ()
    {
        // Returning the value
        return get_object_vars( self::$instance );
    }
}



?>