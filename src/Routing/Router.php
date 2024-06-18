<?php



namespace Solenoid\Core\Routing;



use \Solenoid\Core\Routing\Target;



class Router
{
    public array   $routes;
    public ?Target $fallback_target;



    # Returns [self]
    public function __construct (array $routes, ?Target $fallback_target = null)
    {
        // (Getting the value)
        $this->routes = $routes;



        if ( !$fallback_target )
        {// Value not found
            // (Getting the value)
            $fallback_target = Target::define( function () { http_response_code(404); } );
        }

        // (Getting the value)
        $this->fallback_target = $fallback_target;
    }



    # Returns [Target|false]
    public function resolve (string $route, string $method)
    {
        // (Setting the value)
        $args = [];



        // (Getting the value)
        $target = $this->routes[$route][$method] ?? false;

        if ( $target === false )
        {// Value not found
            foreach ( $this->routes as $app_route => $v )
            {// Processing each entry
                foreach ( $v as $app_method => $app_target )
                {// Processing each entry
                    if ( $app_method !== $method ) continue;

                    if ( $route[0] === '/' && $route[ strlen($route) - 1 ] === '/' )
                    {// (Route is defined as regex)
                        if ( preg_match( $app_route, $route, $matches ) === 1 )
                        {// Match OK
                            // (Getting the value)
                            $args = $matches;

                            // (Getting the value)
                            $target = $app_target;



                            // Breaking the iteration
                            break 2;
                        }
                    }
                    else
                    {// (Route is not defined as regex)
                        // (Getting the values)
                        $route_parts     = explode( '/', $route );
                        $app_route_parts = explode( '/', $app_route );

                        if ( count($route_parts) !== count($app_route_parts) ) continue;



                        // (Getting the value)
                        $diff = array_diff( $route_parts, $app_route_parts );

                        if ( !$diff )
                        {// (Parts are equals)
                            // (Getting the value)
                            $target = $app_target;



                            // Breaking the iteration
                            break 2;
                        }



                        foreach ( $route_parts as $k => $v )
                        {// Processing each entry
                            if ( $route_parts[$k] !== $app_route_parts[$k] )
                            {// (Values are different)
                                if ( preg_match( '/\[\s*([^\s]+)\s*\]/', $app_route_parts[$k], $matches ) === 1 )
                                {// Match OK
                                    // (Getting the value)
                                    $args[ $matches[1] ] = $route_parts[$k];
                                }
                                else
                                {// Match failed
                                    // Breaking the iteration
                                    break 2;
                                }
                            }
                        }

                        if ( $args )
                        {// Value found
                            // (Getting the value)
                            $target = $app_target;



                            // Breaking the iteration
                            break 2;
                        }
                    }
                }
            }
        }

        if ( $target === false )
        {// Value not found
            if ( $this->fallback_target )
            {// Value found
                // (Getting the value)
                $target = $this->fallback_target;
            }
        }



        if ( $target )
        {// Value found
            // (Getting the value)
            $target->args = &$args;
        }



        // Returning the value
        return $target;
    }
}



?>