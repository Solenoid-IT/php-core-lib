<?php



namespace Solenoid\Core\App;



use \Solenoid\Core\App\App;
use \Solenoid\Core\Routing\Route;



class WebApp extends App
{
    # Returns [self]
    public function __construct (array $config)
    {
        // (Calling the function)
        parent::__construct( $config );
    }



    # Returns [self|false]
    public function run ()
    {
        if ( !App::$env )
        {// Value not found
            // (Setting the status)
            http_response_code( 404 );

            // Printing the value
            echo 'ENV NOT FOUND';



            // Returning the value
            return false;
        }



        // (Resolving the route)
        $target = Route::resolve( $_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'] );

        if ( $target === false )
        {// (Target not found)
            // (Setting the status)
            http_response_code( 404 );

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
        $target->run_app( $this );



        // Returning the value
        return $this;
    }
}



?>