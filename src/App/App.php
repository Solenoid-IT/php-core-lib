<?php



namespace Solenoid\Core\App;



use \Solenoid\Core\Env;



abstract class App
{
    private static array $events = [];



    public static string  $mode;

    public static string  $basedir;
    public static string  $id;
    public static string  $name;
    public static string  $timezone;

    public static ?Env    $env;
    public static array   $route_tags;



    # Returns [self]
    public function __construct (array $config)
    {
        // (Getting the values)
        self::$basedir = realpath( $config['basedir'] );

        self::$id      = $config['id'];
        self::$name    = $config['name'];



        // (Getting the value)
        self::$timezone = $config['timezone'] ?? date_default_timezone_get();

        // (Setting the default timezone)
        date_default_timezone_set( self::$timezone );



        // (Setting the ini)
        ini_set( 'log_errors_max_len', '0' );



        // (Setting the value)
        error_reporting( E_ERROR );



        // (Setting the cwd)
        chdir( self::$basedir );



        // (Getting the value)
        $env = Env::detect();
    


        // (Getting the value)
        self::$env = $env ? $env : null;



        /*

        // (Setting the ini)
        ini_set( 'display_errors', self::$env->type === 'dev' ? 'on' : 'off' );
        ini_set( 'display_startup_errors', self::$env->type === 'dev' ? 'on' : 'off' );

        */



        // (Getting the value)
        self::$mode = self::fetch_mode();



        // (Setting the ini)
        ini_set( 'display_errors', self::$mode === 'http' ? 'on' : 'off' );
        ini_set( 'display_startup_errors', self::$mode === 'http' ? 'on' : 'off' );
    }



    # Returns [string]
    public static function fetch_mode ()
    {
        // Returning the value
        return isset( $_SERVER['REQUEST_METHOD'] ) ? 'http' : 'cli';
    }



    # Returns [void]
    public static function detect_mode ()
    {
        // (Getting the value)
        self::$mode = self::fetch_mode();
    }



    # Returns [void]
    public static function on (string $event_type, callable $function)
    {
        // (Getting the value)
        self::$events[ $event_type ][] = $function;
    }

    # Returns [void]
    public static function trigger_event (string $event_type, array $data = [])
    {
        foreach ( self::$events[ $event_type ] as $function )
        {// Processing each entry
            // (Calling the function)
            $function( $data );
        }
    }



    # Returns [void]
    abstract public function run ();
}



?>