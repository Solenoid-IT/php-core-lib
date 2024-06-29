<?php



namespace Solenoid\Core\App;



use \Solenoid\Core\Routing\Target;



class SysApp extends App
{
    private static self $instance;

    public string  $host;



    # Returns [self] | Throws [Exception]
    private function __construct (array $config, string $host)
    {
        if ( parent::fetch_context() !== 'cli' )
        {// Match failed
            // (Setting the value)
            $message = "Cannot create the instance :: This object can be used only into CLI contexts";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return;
        }



        // (Calling the function)
        parent::__construct($config);



        // (Getting the value)
        $this->host = $host;



        // (Getting the value)
        $envs = $config['envs'][ self::fetch_context() ];

        if ( $envs )
        {// Value found
            foreach ( $envs as $env )
            {// Processing each entry
                if ( in_array( $this->host, $env->hosts ) )
                {// Match OK
                    // (Getting the value)
                    $this->env = $env;

                    // Breaking the iteration
                    break;
                }
            }



            // (Setting the ini)
            ini_set( 'display_errors', $this->env->type === 'dev' ? 'on' : 'off' );
            ini_set( 'display_startup_errors', $this->env->type === 'dev' ? 'on' : 'off' );
        }
    }



    # Returns [self] | Throws [Exception]
    public static function init (array $config, string $host)
    {
        if ( !isset( self::$instance ) )
        {// Value not found
            // (Getting the value)
            self::$instance = new self( $config, $host );
        }



        // Returning the value
        return self::$instance;
    }

    # Returns [self]
    public static function fetch ()
    {
        // Returning the value
        return self::$instance;
    }



    # Returns [self|false] | Throws [Exception]
    public function run ()
    {
        // (Accessing the value)
        global $argv;



        // (Getting the value)
        $args = $argv;

        // (Popping the array)
        array_shift($args);
        array_shift($args);



        if ( count($args) < 2 )
        {// (There are no args)
            // Printing the value
            echo 'php bootstrap.php <task> <method> ...<args> ';
        }
die(print_r($args));

/*
        // (Resolving the route)
        $target = Target::link();

        if ( $target === false )
        {// (Target not found)
            // (Setting the header)
            http_response_code(404);

            // (Printing the value)
            echo 'ROUTE NOT FOUND';
        }



        // (Running the target)
        $target->run_app($this);*/



        // Returning the value
        return $this;
    }
}



?>