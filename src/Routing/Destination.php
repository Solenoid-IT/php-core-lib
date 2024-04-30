<?php



namespace Solenoid\Core\Routing;



use \Solenoid\Core\Core;



class Destination
{
    public                 $function;

    public string        $controller;
    public string            $action;

    public array  $middleware_groups;

    public array               $tags;



    # Returns [self]
    public function __construct ()
    {
        // (Setting the values)
        $this->middleware_groups = [];
        $this->tags              = [];
    }



    # Returns [Destination]
    public static function define (callable $function)
    {
        // (Creating a Destination)
        $destination = new Destination();

        // (Getting the value)
        $destination->function = $function;



        // Returning the value
        return $destination;
    }

    # Returns [Destination]
    public static function link (string $controller, string $action)
    {
        // (Creating a Destination)
        $destination = new Destination();

        // (Getting the values)
        $destination->controller = $controller;
        $destination->action     = $action;



        // Returning the value
        return $destination;
    }



    # Returns [self]
    public function set_middlewares (array $groups)
    {
        // (Getting the value)
        $this->middleware_groups = $groups;



        // Returning the value
        return $this;
    }

    # Returns [bool]
    public function process_middlewares ()
    {
        foreach ($this->middleware_groups as $group)
        {// Processing each entry
            foreach (Core::$middleware_groups[ $group ] as $middleware)
            {// Processing each entry
                if ( call_user_func_array( [ $middleware, 'run' ], [  ] ) === false )
                {// Match OK
                    // Returning the value
                    return false;
                }
            }
        }



        // Returning the value
        return true;
    }



    # Returns [self]
    public function set_tags (array $tags)
    {
        // (Getting the value)
        $this->tags = $tags;



        // Returning the value
        return $this;
    }



    # Returns [string]
    public function get_type ()
    {
        // Returning the value
        return isset( $this->function ) ? 'define' : 'link';
    }



    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return $this->get_type() === 'define' ? 'define' : "$this->controller::$this->action";
    }
}



?>