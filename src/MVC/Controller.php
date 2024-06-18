<?php



namespace Solenoid\Core\MVC;



use \Solenoid\Core\App\App;



class Controller
{
    public static App $app;



    # Returns [self]
    public function __construct (App &$app)
    {
        // (Getting the value)
        self::$app = &$app;
    }
}



?>