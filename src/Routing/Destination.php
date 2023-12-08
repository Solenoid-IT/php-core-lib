<?php



namespace Solenoid\Core\Routing;



use \Solenoid\Core\Core;



class Destination
{
    public string        $controller;
    public string            $action;

    public array  $middleware_groups;

    public array               $tags;



    # Returns [self]
    public function __construct (string $controller, string $action)
    {
        // (Getting the values)
        $this->controller = $controller;
        $this->action     = $action;

        // (Setting the values)
        $this->middleware_groups = [];
        $this->tags              = [];
    }



    # Returns [Destination]
    public static function create (string $controller, string $action)
    {
        // Returning the value
        return new Destination( $controller, $action );
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
    public function __toString ()
    {
        // Returning the value
        return "$this->controller::$this->action";
    }
}



?>