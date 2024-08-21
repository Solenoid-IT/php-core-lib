<?php



namespace Solenoid\Core\App;



use \Solenoid\Log\Logger;
use \Solenoid\Core\Blade;
use \Solenoid\Core\Env;
use \Solenoid\Core\Storage;
use \Solenoid\Core\Routing\Target;



class App
{
    private static self $inst;



    public int     $ts;

    public string  $basedir;

    public string  $id;
    public string  $name;

    public string  $history;

    public string  $gate;
    public array   $middlewares;

    public array   $loggers;

    public Blade   $blade;

    public Env     $env;

    public string  $credentials;
    public Storage $storage;

    public string  $timezone;

    public ?Target $target;



    # Returns [self] | Throws [Exception]
    public function __construct (array $config)
    {
        // (Getting the value)
        $this->ts = time();



        // (Getting the values)
        $this->basedir     = realpath( $config['basedir'] );

        $this->id          = $config['id'];
        $this->name        = $config['name'];

        $this->history     = $config['history'];

        $this->gate        = $config['gate'];
        $this->middlewares = $config['middlewares'];



        // (Setting the value)
        $this->loggers = [];

        foreach ( $config['logs'] as $context => $v )
        {// Processing each entry
            foreach ( $v as $type => $file_path )
            {// Processing each entry
                // (Getting the value)
                $this->loggers[$context][$type] = new Logger($file_path);
            }
        }



        if ( $config['blade'] )
        {// Value found
            // (Getting the value)
            $this->blade = new Blade( $config['blade']['views'], $config['blade']['cache'], true );
        }



        // (Getting the values)
        $this->credentials = $config['credentials'];
        $this->storage     = new Storage( $config['storage']['folder_path'], $config['storage']['chroot'] );



        // (Getting the value)
        $this->timezone = $config['timezone'] ?? date_default_timezone_get();

        // (Setting the default timezone)
        date_default_timezone_set( $this->timezone );



        // (Setting the value)
        $this->target = null;



        // (Setting the ini)
        ini_set( 'log_errors_max_len', '0' );



        // (Setting the value)
        error_reporting(E_ERROR);



        // (Setting the directory)
        chdir( $this->basedir );
    }



    # Returns [array<assoc>]
    public function fetch_history ()
    {
        // (Getting the value)
        $file_content = file_get_contents( $this->history );

        if ( $file_content === false )
        {// (Unable to read the file content)
            // Returning the value
            return false;
        }



        // Returning the value
        return json_decode( $file_content, true );
    }

    # Returns [array<assoc>]
    public function fetch_credentials ()
    {
        // (Getting the value)
        $file_content = file_get_contents( $this->credentials );

        if ( $file_content === false )
        {// (Unable to read the file content)
            // Returning the value
            return false;
        }



        // Returning the value
        return json_decode( $file_content, true );
    }



    # Returns [string]
    public static function fetch_context ()
    {
        // Returning the value
        return isset( $_SERVER['REQUEST_METHOD'] ) ? 'http' : 'cli';
    }

    # Returns [string]
    public static function fetch_initiator ()
    {
        // Returning the value
        return self::fetch_context() === 'http' ? 'request' : 'task';
    }



    # Returns [self]
    public static function register (App &$app)
    {
        if ( !isset( self::$inst ) )
        {// Value not found
            // (Getting the value)
            self::$inst = &$app;
        }



        // Returning the value
        return self::$inst;
    }

    # Returns [self]
    public static function get ()
    {
        // Returning the value
        return self::$inst;
    }
}



?>