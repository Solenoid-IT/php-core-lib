<?php



namespace Solenoid\Core\MVC;



use \Solenoid\Core\Core;



class Controller
{
    public static Core $core;



    # Returns [self]
    public function __construct (Core &$core)
    {
        // (Getting the value)
        self::$core = $core;
    }

    # Returns [Controller]
    public static function create (Core &$core)
    {
        // Returning the value
        return new Controller( $core );
    }
}



?>