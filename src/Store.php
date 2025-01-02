<?php



namespace Solenoid\Core;



class Store
{
    private array $values = [];



    # Returns [mixed|false]
    protected function get (string $id)
    {
        // Returning the value
        return $this->values[ $id ] ?? false;
    }

    # Returns [void]
    protected function set (string $id, mixed &$value)
    {
        // (Getting the value)
        $this->values[ $id ] = &$value;
    }
}



?>