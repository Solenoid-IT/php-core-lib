<?php



namespace Solenoid\Core\Routing;



class Target
{
    private array $middlewares = [];

    private array $args = [];
    private array $tags = [];



    private $function;

    private string $class;
    private string $fn;



    # Returns [self]
    public static function define (callable $function)
    {
        // (Creating a Target)
        $target = new Target();



        // (Getting the value)
        $target->function = $function;



        // Returning the value
        return $target;
    }

    # Returns [self]
    public static function link (string $class, string $fn)
    {
        // (Creating a Target)
        $target = new Target();



        // (Getting the values)
        $target->class = $class;
        $target->fn    = $fn;



        // Returning the value
        return $target;
    }



    # Returns [self]
    public function add_middleware (string $class)
    {
        // (Appending the value)
        $this->middlewares[] = $class;



        // Returning the value
        return $this;
    }



    # Returns [self]
    public function set_args (array $value)
    {
        // (Getting the value)
        $this->args = $value;



        // Returning the value
        return $this;
    }



    # Returns [self]
    public function add_tag (string $value)
    {
        // (Appending the value)
        $this->tags[] = $value;



        // Returning the value
        return $this;
    }

    # Returns [array<string>]
    public function list_tags ()
    {
        // Returning the value
        return $this->tags;
    }



    # Returns [self|mixed]
    public function run ()
    {
        // (Setting the value)
        $middleware_lock = false;

        foreach ( $this->middlewares as $middleware )
        {// Processing each entry
            if ( call_user_func_array( [ $middleware, 'run' ], [] ) === false )
            {// (There is a middleware lock)
                // (Setting the value)
                $middleware_lock = true;

                // Breaking the iteration
                break;           
            }
        }



        if ( $middleware_lock )
        {// Value is true
            // Returning the value
            return $this;
        }



        // (Setting the value)
        $response = null;

        if ( isset( $this->function ) )
        {// Match OK
            // (Getting the value)
            #$response = ( $this->function )();
            $response = call_user_func_array( $this->function, $this->args );
        }
        else
        if ( isset( $this->class ) && isset( $this->fn ) )
        {// Match OK
            // (Getting the value)
            #$response = call_user_func_array( [ ( new ( $this->class )() ), $this->fn ], $this->args );
            $response = call_user_func_array( [ $this->class, $this->fn ], $this->args );
        }



        // Returning the value
        return $response;
    }



    # Returns [string]
    public function __toString ()
    {
        // (Setting the value)
        $value = '';

        if ( isset( $this->function ) )
        {// Match OK
            // (Setting the value)
            $value = '()';
        }
        else
        if ( isset( $this->class ) && isset( $this->fn ) )
        {// Match OK
            // (Getting the value)
            $value = "$this->class::$this->fn()";
        }



        // Returning the value
        return $value;
    }
}



?>