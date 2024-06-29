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
        $args = array_slice( $argv, 1 );

        if ( count($args) < 2 )
        {// (There are no args)
            // Printing the value
            echo "\n\nphp $argv[0] <task> <method> ...<args>\n\n";

            // Returning the value
            return $this;
        }



        // (Getting the value)
        $target = Target::link( $args[1], $args[2], array_slice( $args, 2 ) );



        // (Running the target)
        $target->run_app($this);



        // Returning the value
        return $this;
    }
}



?>