<?php



namespace Solenoid\Core;



class Gate
{
    public static string $class_path;



    # Returns [void]
    public static function config (string $class_path)
    {
        // (Getting the value)
        self::$class_path = $class_path;
    }
}



?>