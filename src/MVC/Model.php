<?php



namespace Solenoid\Core\MVC;



use \Solenoid\Core\Core;



class Model
{
    public static Core $core;



    # Returns [self]
    public function __construct (Core &$core)
    {
        // (Getting the value)
        self::$core = $core;
    }

    # Returns [Model]
    public static function create (Core &$core)
    {
        // Returning the value
        return new Model( $core );
    }
}



?>