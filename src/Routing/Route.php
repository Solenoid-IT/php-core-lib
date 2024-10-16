<?php



namespace Solenoid\Core\Routing;



use \Solenoid\Core\Routing\Target;



class Route
{
    private static array  $routes;
    private static Target $fallback_target;

    public string $id;
    public string $method;



    # Returns [self]
    public function __construct (string $id, string $method)
    {
        // (Getting the values)
        $this->id     = $id;
        $this->method = $method;
    }



    # Returns [void]
    public static function handle (string|Route $route, Target $target)
    {
        if ( is_string( $route ) )
        {// Value is a string
            // (Getting the values)
            [ $method, $id ] = explode( ' ', $route, 2 );
        }
        else
        {// Value is a Route 
            // (Getting the values)
            $id     = $route->id;
            $method = $route->method;
        }



        // (Getting the value)
        self::$routes[ $id ][ $method ] = $target;
    }

    # Returns [void]
    public static function handle_fallback (Target $target)
    {
        // (Getting the value)
        self::$fallback_target = $target;
    }



    # Returns [Target|false]
    public static function resolve (string $id, string $method)
    {
        // (Getting the value)
        $params = [];



        // (Getting the value)
        $target = self::$routes[ $id ][ $method ];

        if ( !$target )
        {// Value not found
             foreach ( self::$routes as $defined_id => $v )
             {// Processing each entry
                foreach ( $v as $defined_method => $defined_target )
                {// Processing each entry
                    if ( $defined_method !== $method ) continue;



                    if ( $defined_id[0] === '/' && $defined_id[ strlen( $defined_id ) - 1 ] === '/' )
                    {// (ID is a regex)
                        if ( preg_match( $defined_id, $id, $matches ) === 1 )
                        {// Match OK
                            // (Getting the values)
                            $target = $defined_target;
                            $params = $matches;



                            // Breaking the iteration
                            break 2;
                        }
                    }
                    else
                    {// (ID is not a regex)
                        // (Getting the values)
                        $id_parts         = explode( '/', $id );
                        $defined_id_parts = explode( '/', $defined_id );

                        if ( count( $id_parts ) !== count( $defined_id_parts ) ) continue;



                        // (Getting the value)
                        $diff = array_diff( $id_parts, $defined_id_parts );

                        if ( !$diff )
                        {// (Parts are equals)
                            // (Getting the value)
                            $target = $defined_target;



                            // Breaking the iteration
                            break 2;
                        }



                        foreach ( $id_parts as $k => $v )
                        {// Processing each entry
                            if ( $id_parts[$k] !== $defined_id_parts[$k] )
                            {// (Values are different)
                                if ( preg_match( '/\[\s*([^\s]+)\s*\]/', $defined_id_parts[$k], $matches ) === 1 )
                                {// Match OK
                                    // (Getting the value)
                                    $params[ $matches[1] ] = $id_parts[$k];
                                }
                                else
                                {// Match failed
                                    // Breaking the iteration
                                    break 2;
                                }
                            }
                        }



                        if ( $params )
                        {// Value found
                            // (Getting the value)
                            $target = $defined_target;



                            // Breaking the iteration
                            break 2;
                        }
                    }
                }
             }
        }



        if ( !$target )
        {// Value not found
            if ( isset( self::$fallback_target ) )
            {// Value found
                // (Getting the value)
                $target = self::$fallback_target;
            }
        }



        if ( !$target )
        {// Value not found
            // (Setting the value)
            $target = false;
        }



        if ( $target )
        {// Value found
            // (Getting the value)
            $target->args = &$params;
        }



        // Returning the value
        return $target;
    }



    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return "$this->method $this->id";
    }
}



?>