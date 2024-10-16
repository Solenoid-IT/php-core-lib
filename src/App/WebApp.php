<?php



namespace Solenoid\Core\App;



use \Solenoid\Core\Routing\Route;
use \Solenoid\HTTP\Request;



class WebApp extends App
{
    private static self $instance;

    public Request $request;



    # Returns [self] | Throws [Exception]
    private function __construct (array $config)
    {
        if ( parent::fetch_context() !== 'http' )
        {// Match failed
            // (Setting the value)
            $message = "Cannot create the instance :: This object can be used only into HTTP contexts";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return;
        }



        // (Calling the function)
        parent::__construct($config);



        // (Getting the value)
        $this->request = Request::fetch();



        // (Getting the value)
        $envs = $config['envs'][ self::fetch_context() ];

        if ( $envs )
        {// Value found
            foreach ( $envs as $env )
            {// Processing each entry
                if ( in_array( $this->request->server_name, $env->hosts ) )
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
    public static function init (array $config)
    {
        if ( !isset( self::$instance ) )
        {// Value not found
            // (Getting the value)
            self::$instance = new self( $config );
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
        // (Resolving the route)
        $target = Route::resolve( $_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'] );

        if ( $target === false )
        {// (Target not found)
            // (Setting the status)
            http_response_code(404);

            // (Printing the value)
            echo 'ROUTE NOT FOUND';



            // Returning the value
            return false;
        }



        if ( isset( $target->class ) && isset( $target->fn ) )
        {// Values found
            // (Getting the value)
            $target->fa = array_values( $target->args );
        }



        // (Running the target)
        $target->run_app($this);



        // Returning the value
        return $this;
    }



    # Returns [string]
    public function asset (string $value)
    {
        switch ( $this->env->type )
        {
            case 'dev':
                // (Getting the value)
                $value = $value . '?ts=' . $this->ts;
            break;

            case 'prod':
                // (Getting the value)
                $value = $value . '?v=' . array_keys( $this->fetch_history() )[0];
            break;
        }



        // Returning the value
        return $value;
    }
}



?>