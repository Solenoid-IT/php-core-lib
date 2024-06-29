<?php



namespace Solenoid\Core\App;



use \Solenoid\Core\Routing\Router;
use \Solenoid\HTTP\Request;



class WebApp extends App
{
    private static self $instance;

    public Router  $router;
    public Request $request;



    # Returns [self] | Throws [Exception]
    private function __construct (array $config, Router &$router)
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
        $this->router = &$router;



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
    public static function init (array $config, Router &$router)
    {
        if ( !isset( self::$instance ) )
        {// Value not found
            // (Getting the value)
            self::$instance = new self( $config, $router );
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
        $target = $this->router->resolve( '/' . $_GET[ $this->route_handler ], $_SERVER['REQUEST_METHOD'] );

        if ( $target === false )
        {// (Target not found)
            // (Setting the header)
            http_response_code(404);

            // (Printing the value)
            echo 'ROUTE NOT FOUND';
        }



        try
        {
            // (Running the target)
            $target->run_app($this);
        }
        catch (\Exception $e)
        {
            // (Setting the response-code)
            http_response_code(500);



            // Throwing an exception
            throw $e;



            // Returning the value
            return false;
        }



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