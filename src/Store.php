<?php



namespace Solenoid\Core;



class Store
{
    public static Core $core;



    # Returns [self]
    public function __construct (Core &$core)
    {
        // (Getting the value)
        self::$core = $core;
    }

    # Returns [Store]
    public static function create (Core &$core)
    {
        // Returning the value
        return new Store( $core );
    }
}



?>