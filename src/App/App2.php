<?php



namespace Solenoid\Core\App;



use \Solenoid\Core\Env;
use \Solenoid\Core\Routing\Target;



abstract class App2
{
    public string $basedir;
    public string $id;
    public string $name;
    public string $timezone;

    public ?Env   $env;
    public Target $target;
    public string $gate;



    # Returns [self]
    public function __construct (array $config)
    {
        // (Getting the values)
        $this->basedir = realpath( $config['basedir'] );

        $this->id      = $config['id'];
        $this->name    = $config['name'];



        // (Getting the value)
        $this->timezone = $config['timezone'] ?? date_default_timezone_get();

        // (Setting the default timezone)
        date_default_timezone_set( $this->timezone );



        // (Setting the ini)
        ini_set( 'log_errors_max_len', '0' );



        // (Setting the value)
        error_reporting( E_ERROR );



        // (Setting the cwd)
        chdir( $this->basedir );



        // (Getting the value)
        $env = Env::detect();
    


        // (Getting the value)
        $this->env = $env ? $env : null;



        // (Setting the ini)
        ini_set( 'display_errors', $this->env->type === 'dev' ? 'on' : 'off' );
        ini_set( 'display_startup_errors', $this->env->type === 'dev' ? 'on' : 'off' );



        // (Setting the value)
        $this->target = null;



        // (Getting the value)
        $this->gate = $config['gate'];
    }



    # Returns [void]
    abstract public function run ();



    # Returns [string]
    public static function fetch_context ()
    {
        // Returning the value
        return isset( $_SERVER['REQUEST_METHOD'] ) ? 'http' : 'cli';
    }
}



?>