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



        /*

        if ( isset( $target->class ) && isset( $target->fn ) )
        {// Values found
            // (Getting the value)
            $target->fa = array_values( $target->args );
        }



        // (Running the target)
        $target->run_app( $this );

        */



        try
        {
            // (Triggering the event)
            App::trigger_event( 'start' );



            // (Getting the value)
            $gate_lock = call_user_func_array( [ \App\Gate::class, 'run' ], [] ) === false;

            if ( !$gate_lock )
            {// (There is no a gate lock)
                // (Getting the value)
                $response = $target->run();

                if ( $response !== $target )
                {// (There is no a middleware lock)
                    if ( $response !== null )
                    {// (Function returns something)
                        // (Setting the header)
                        header('Content-Type: application/json');

                        // Printing the value
                        echo json_encode( $response );
                    }
                }
            }



            // (Triggering the event)
            App::trigger_event( 'end' );
        }
        catch (\Exception $e)
        {
            // (Getting the value)
            $message = (string) $e;



            // (Triggering the event)
            App::trigger_event( 'error', [ 'message' => $message ] );



            if ( App::$env->type === 'dev' )
            {// Match OK
                // Throwing an exception
                throw $e;
            }



            // Returning the value
            return false;
        }



        // Returning the value
        return $this;
    }
}



?>