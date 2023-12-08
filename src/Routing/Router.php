<?php



namespace Solenoid\Core\Routing;



use \Solenoid\Core\Core;
use \Solenoid\Perf\Analyzer;



class Router
{
    private static Core $core;



    # Returns [self]
    public function __construct (Core &$core)
    {
        // (Getting the value)
        self::$core = $core;
    }

    # Returns [Router]
    public static function create (Core &$core)
    {
        // Returning the value
        return new Router( $core );
    }



    # Returns [assoc|false]
    public static function fetch_path_args (string $client_path, string $route_path)
    {
        // (Setting the value)
        $args = [];



        // (Getting the values)
        $client_path_parts = explode( '/', $client_path );
        $route_path_parts  = explode( '/', $route_path );



        if ( count( $client_path_parts ) !== count( $route_path_parts ) )
        {// Match failed
            // Returning the value
            return false;
        }



        for ($i = 0; $i < count( $route_path_parts ); $i++)
        {// Iterating each index
            // (Getting the values)
            $client_path_part = $client_path_parts[ $i ];
            $route_path_part  = $route_path_parts[ $i ];



            if ( $client_path_part !== $route_path_part )
            {// Match failed
                if ( preg_match('/\[\ ([^\ ]+)\ \]/', $route_path_part, $matches ) === 0 )
                {// (Regex does not match the text)
                    // Returning the value
                    return false;
                }



                // (Getting the value)
                $args[ $matches[1] ] = $client_path_part;
            }
        }



        // Returning the value
        return $args;
    }



    # Returns [Destination|false]
    public function fetch_destination ()
    {
        // (Getting the values)
        $request = self::$core::$request;
        $routes  = self::$core::$routes;



        // (Getting the value)
        $path_list = array_keys($routes);

        foreach ($path_list as $path)
        {// Processing each entry
            // (Getting the value)
            $path_args = self::fetch_path_args( $request::$path, $path );

            if ( $path_args )
            {// Value found
                // (Getting the value)
                self::$core::$path_args = $path_args;



                // Returning the value
                return $routes[ $path ][ $request::$method ] ?? false;
            }
        }



        // (Setting the value)
        self::$core::$path_args = [];



        // Returning the value
        return $routes[ $request::$path ][ $request::$method ] ?? false;
    }

    # Returns [bool] | Throws [Exception]
    public function resolve_destination ()
    {
        try
        {
            // (Creating an Analyzer)
            $performance_analyzer = Analyzer::create();

            // (Opening the analyzer)
            $performance_analyzer->open();



            // (Fetching the destination)
            $destination = $this->fetch_destination();

            if ( $destination === false )
            {// (Destination not found)
                // (Getting the value)
                $destination = self::$core::$fallback_route;

                // (Setting the http status code)
                http_response_code( 404 );
            }



            // (Getting the value)
            self::$core::$route_tags = $destination->tags;



            // (Calling the user function by array)
            $continue = call_user_func_array( [ new self::$core::$gate_ns( self::$core ) , 'run' ], [  ] ) !== false;

            if ( $continue )
            {// Value is true
                // (Processing the middlewares)
                $continue = $destination->process_middlewares() !== false;

                if ( $continue )
                {// Value is true
                    // (Getting the values)
                    $controller = $destination->controller;
                    $action     = $destination->action;

                    $instance   = new $controller( self::$core );



                    // (Calling the user function by array)
                    #$response = call_user_func_array([ $instance , $action ], [ $request ]);
                    $response = call_user_func_array( [ $instance , $action ], [  ] );

                    if ( $response !== null )
                    {// (Controller method returns something)
                        // (Setting the header)
                        header('Content-Type: application/json');

                        // Printing the value
                        echo json_encode( $response );
                    }
                }
            }



            // (Closing the analyzer)
            $performance_analyzer->close();



            // (Pushing the message)
            self::$core::$loggers['call']->push( self::$core::$request . ' -> ' . $performance_analyzer );
        }
        catch (\Exception $e)
        {
            // (Getting the value)
            $message = (string) $e;

            // (Pushing the message)
            self::$core::$loggers['error']->push( self::$core::$request . ' -> ' . str_replace( "\n", " >> ", $message ) );

            // Throwing an exception
            throw $e;

            // Returning the value
            return false;
        }



        // Returning the value
        return true;
    }
}



?>