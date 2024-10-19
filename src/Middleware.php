<?php



namespace Solenoid\Core;



class Middleware
{
    private static array $entries;



    # Returns [void]
    public static function add (string $group_id, string $class_path)
    {
        // (Getting the value)
        self::$entries[ $group_id ][] = $class_path;
    }



    # Returns [array<string>|false]
    public static function fetch (string $group_id)
    {
        // Returning the value
        return self::$entries[ $group_id ] ?? false;
    }
}



?>