<?php



namespace Solenoid\Core\App;



use \Solenoid\Log\Logger;
use \Solenoid\Core\Blade;
use \Solenoid\Core\Env;



class App
{
    public string $basedir;

    public string $id;
    public string $name;

    public string $history;

    public string $gate;
    public array  $middlewares;

    public array  $loggers;

    public array  $args;

    public string $route_handler;

    public Blade  $blade;

    public Env    $env;

    public string $credentials;
    public string $storage;



    # Returns [self] | Throws [Exception]
    public function __construct (array $config)
    {
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
                $this->loggers[$context][$type] = Logger::create($file_path);
            }
        }



        // (Setting the value)
        $this->args = [];



        // (Getting the value)
        $this->route_handler = $config['route_handler'];



        if ( $config['blade'] )
        {// Value found
            // (Getting the value)
            $this->blade = new Blade( $config['blade']['views'], $config['blade']['cache'], true );
        }



        // (Getting the values)
        $this->credentials = $config['credentials'];
        $this->storage     = $config['storage'];
    }



    /*

    # Returns [self]
    public static function init (array $config)
    {
        if ( !isset( self::$instance ) )
        {// Value not found
            // (Getting the value)
            self::$instance = new self($config);
        }



        // Returning the value
        return self::$instance;
    }

    */



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
        return isset($_SERVER) ? 'http' : 'cli';
    }

    # Returns [string]
    public static function fetch_initiator ()
    {
        // Returning the value
        return self::fetch_context() === 'http' ? 'request' : 'task';
    }
}



?>