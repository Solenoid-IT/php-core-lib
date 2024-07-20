<?php



namespace Solenoid\Core\Routing;



use \Solenoid\Core\App\App;
use \Solenoid\Perf\Analyzer;



class Target
{
    public array $middleware_groups;
    public array $tags;

    public $function;

    public string $class;
    public string $fn;
    public array  $fa;

    public array  $args;



    # Returns [self]
    public function __construct ()
    {
        // (Setting the values)
        $this->middleware_groups = [];
        $this->tags              = [];

        $this->args              = [];
    }



    # Returns [Target]
    public static function define (callable $function)
    {
        // (Creating a Target)
        $target = new Target();

        // (Getting the value)
        $target->function = $function;



        // Returning the value
        return $target;
    }

    # Returns [Target]
    public static function link (string $class, string $fn, array $args = [])
    {
        // (Creating a Target)
        $target = new Target();



        // (Getting the values)
        $target->class  = $class;
        $target->fn     = $fn;
        $target->fa     = $args;



        // Returning the value
        return $target;
    }



    # Returns [self]
    public function set_middlewares (array $groups)
    {
        // (Getting the value)
        $this->middleware_groups = $groups;



        // Returning the value
        return $this;
    }
    
    # Returns [self]
    public function set_tags (array $tags)
    {
        // (Getting the value)
        $this->tags = $tags;



        // Returning the value
        return $this;
    }



    // Returns [mixed|false] | Throws [Exception]
    public function run (mixed $data = null)
    {
        try
        {
            if ( $this->function )
            {// (Target has been defined)
                // (Calling the function)
                $response = ( $this->function )( $data );
            }
            else
            if ( $this->class && $this->fn )
            {// (Target has been linked)
                // (Calling the user function by array)
                $response = call_user_func_array( [ ( new ($this->class)() ), $this->fn ], [ $data ] );
            }
        }
        catch (\Exception $e)
        {
            // (Getting the value)
            $message = (string) $e;

            // Throwing an exception
            throw $e;

            // Returning the value
            return false;
        }



        // Returning the value
        return $response;
    }

    // Returns [self|false] | Throws [Exception]
    public function run_app (App &$app)
    {
        // (Getting the values)
        $app_context   = App::fetch_context();
        $app_initiator = App::fetch_initiator();



        // (Getting the value)
        $app->target = &$this;



        try
        {
            if ( $app->loggers[$app_context]['activity'] )
            {// Value found
                // (Creating an Analyzer)
                $performance_analyzer = Analyzer::create();

                // (Opening the analyzer)
                $performance_analyzer->open();
            }



            // (Calling the user function by array)
            $gate_lock = call_user_func_array( [ $app->gate, 'run' ], [  ] ) === false;

            if ( !$gate_lock )
            {// (There is no a gate lock)
                // (Setting the value)
                $middleware_lock = false;

                foreach ( $this->middleware_groups as $group )
                {// Processing each entry
                    // (Getting the value)
                    $middlewares = $app->middlewares[$group];

                    if ( !isset($middlewares) )
                    {// (Group not found)
                        if ( $app->loggers[$app_context]['error'] )
                        {// Value found
                            // (Getting the value)
                            $message = "Middleware group '$group' not found";

                            // (Pushing the message)
                            $app->loggers[$app_context]['error']->push( $app->{ $app_initiator } . ' -> ' . $message );
                        }



                        // Continuing the iteration
                        continue;
                    }



                    foreach ( $middlewares as $middleware )
                    {// Processing each entry
                        if ( call_user_func_array( [ $middleware, 'run' ], [  ] ) === false )
                        {// (There is a middleware lock)
                            // (Setting the value)
                            $middleware_lock = true;

                            // Breaking the iteration
                            break 2;
                        }
                    }
                }



                if ( !$middleware_lock )
                {// (There is no a middleware lock)
                    if ( $this->function )
                    {// (Target has been defined)
                        // (Calling the function)
                        $response = ( $this->function )( $app );
                    }
                    else
                    if ( $this->class && $this->fn )
                    {// (Target has been linked)
                        // (Calling the user function by array)
                        $response = call_user_func_array( [ ( new ($this->class)($app) ), $this->fn ], $this->fa );
                    }



                    if ( $response !== null )
                    {// (Function returns something)
                        // (Setting the header)
                        header('Content-Type: application/json');

                        // Printing the value
                        echo json_encode( $response );
                    }
                }
            }



            if ( $app->loggers[$app_context]['activity'] )
            {// Value found
                // (Closing the analyzer)
                $performance_analyzer->close();



                // (Pushing the message)
                $app->loggers[$app_context]['activity']->push( ( $app->{ $app_initiator } ) . ' -> ' . $performance_analyzer );
            }
        }
        catch (\Exception $e)
        {
            // (Getting the value)
            $message = (string) $e;



            if ( $app_context === 'http' )
            {// Match OK
                // (Setting the response-code)
                http_response_code(500);
            }



            if ( $app->loggers[$app_context]['error'] )
            {// Value found
                // (Pushing the message)
                $app->loggers[$app_context]['error']->push( $app->{ $app_initiator } . ' -> ' . $message );
            }



            // Throwing an exception
            throw $e;



            // Returning the value
            return false;
        }



        // Returning the value
        return $this;
    }
}



?>