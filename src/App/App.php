<?php



namespace Solenoid\Core\App;



use \Solenoid\Core\Env;
use \Solenoid\Core\Routing\Target;



abstract class App
{
    public static string $mode;

    public static string  $basedir;
    public static string  $id;
    public static string  $name;
    public static string  $timezone;

    public static ?Env    $env;
    public static ?Target $target;



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



        // (Setting the ini)
        ini_set( 'display_errors', self::$env->type === 'dev' ? 'on' : 'off' );
        ini_set( 'display_startup_errors', self::$env->type === 'dev' ? 'on' : 'off' );
    }



    # Returns [void]
    public static function detect_mode ()
    {
        // (Getting the value)
        self::$mode = isset( $_SERVER['REQUEST_METHOD'] ) ? 'http' : 'cli';
    }



    # Returns [void]
    abstract public function run ();
}



?>