<?php



namespace Solenoid\Core;



class Middleware
{
    public static Core $core;



    # Returns [self]
    public function __construct (Core &$core)
    {
        // (Getting the value)
        self::$core = $core;
    }

    # Returns [Middleware]
    public static function create (Core &$core)
    {
        // Returning the value
        return new Middleware( $core );
    }
}



?>