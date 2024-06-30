<?php



namespace Solenoid\Core;



use \Solenoid\Core\App\App;

use \Solenoid\System\Process;



class Task
{
    public App $app;



    # Returns [self]
    public function __construct (App &$app)
    {
        // (Getting the value)
        $this->app = &$app;
    }



    # Returns [int|false]
    public static function start (string $id, string $fn = 'run', array $args = [], string $file_path = 'bootstrap.php')
    {
        // (Getting the value)
        $args = implode( ' ', $args );



        // Returning the value
        return Process::start("php $file_path $id $fn $args");
    }
}



?>