<?php



namespace Solenoid\Core;



class Gate
{
    public static Core $core;



    # Returns [self]
    public function __construct (Core &$core)
    {
        // (Getting the value)
        self::$core = $core;
    }

    # Returns [Gate]
    public static function create (Core &$core)
    {
        // Returning the value
        return new Gate( $core );
    }
}



?>