<?php



namespace Solenoid\Core;



class Env
{
    const TYPE_DEV    = 'dev';
    const TYPE_PROD   = 'prod';
    const TYPE_CUSTOM = 'custom';



    public string $id;
    public string $type;
    public array  $hosts;
    public array  $data;



    # Returns [self]
    public function __construct (string $id, string $type = self::TYPE_CUSTOM, array $hosts = [ 'localhost' ], array $data = [])
    {
        // (Getting the values)
        $this->id    = $id;
        $this->type  = $type;
        $this->hosts = $hosts;
        $this->data  = $data;
    }
}



?>