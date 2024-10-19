<?php



namespace Solenoid\Core\Routing;



use \Solenoid\Core\App\App;
use \Solenoid\Core\Middleware;



class Target
{
    private static array $events = [];



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



    # Returns [void]
    public static function on (string $event_type, callable $function)
    {
        // (Getting the value)
        self::$events[ $event_type ][] = $function;
    }

    # Returns [void]
    public static function trigger_event (string $event_type, array $data = [])
    {
        foreach ( self::$events[ $event_type ] as $function )
        {// Processing each entry
            // (Calling the function)
            $function( $data );
        }
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
        // (Getting the value)
        App::$target = &$this;



        try
        {
            // (Triggering the event)
            self::trigger_event( 'before-gate' );



            // (Calling the user function by array)
            $gate_lock = call_user_func_array( [ \App\Gate::class, 'run' ], [  ] ) === false;

            if ( !$gate_lock )
            {// (There is no a gate lock)
                // (Setting the value)
                $middleware_lock = false;

                foreach ( $this->middleware_groups as $group )
                {// Processing each entry
                    // (Getting the value)
                    $middlewares = Middleware::fetch( $group );

                    if ( $middlewares === false )
                    {// (Group not found)
                        // (Getting the value)
                        $message = "Middleware group '$group' not found";

                        // (Triggering the event)
                        self::trigger_event( 'error', [ 'message' => $message ] );



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



            // (Triggering the event)
            self::trigger_event( 'after-gate' );
        }
        catch (\Exception $e)
        {
            // (Getting the value)
            $message = (string) $e;



            // (Triggering the event)
            self::trigger_event( 'error', [ 'message' => $message ] );



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