<?php



namespace Solenoid\Core;



use \Solenoid\System\File;
use \Solenoid\System\Directory;
use \Solenoid\System\Resource;



class Storage
{
    private static array $entries = [];

    private string $path;
    private bool   $chroot;
    private array  $users;
    private ?int   $umask;



    # Returns [self]
    public function __construct (string $path, bool $chroot = false, array $users = [], ?int $umask = null)
    {
        // (Getting the values)
        $this->path   = Directory::select( $path )->normalize()->get_path();
        $this->chroot = $chroot;
        $this->users  = $users;
        $this->umask  = $umask;
    }



    # Returns [void]
    public static function add (string $id, Storage $storage)
    {
        // (Getting the value)
        self::$entries[ $id ] = $storage;
    }

    # Returns [Storage|false]
    public static function select (string $id)
    {
        // Returning the value
        return self::$entries[ $id ] ?? false;
    }



    # Returns [bool]
    public function verify_path (string $entry_path)
    {
        // Returning the value
        return strpos( Resource::select( $this->path . $entry_path )->normalize()->get_path(), $this->path ) === 0;
    }



    # Returns [string|false]
    public function read (string $file_path)
    {
        // (Getting the value)
        $abs_file_path = File::select( $this->path . $file_path )->normalize()->get_path();

        if ( $this->chroot )
        {// Value is true
            if ( !$this->verify_path( $file_path ) ) return false;
        }



        // (Getting the value)
        $current_user = get_current_user();

        if ( $this->users )
        {// Value is not empty
            if ( !in_array( $current_user, $this->users ) )
            {// Match failed
                // Returning the value
                return false;
            }
        }



        // Returning the value
        return File::select( $abs_file_path )->read();
    }

    # Returns [self|false]
    public function write (string $file_path, string $content = '', bool $append = false)
    {
        // (Getting the value)
        $abs_file_path = File::select( $this->path . $file_path )->normalize()->get_path();

        if ( $this->chroot )
        {// Value is true
            if ( !$this->verify_path( $file_path ) ) return false;
        }



        // (Getting the value)
        $current_user = get_current_user();

        if ( $this->users )
        {// Value is not empty
            if ( !in_array( $current_user, $this->users ) )
            {// Match failed
                // Returning the value
                return false;
            }
        }



        if ( File::select( $abs_file_path )->write( $content, ( $append ? 'append' : 'replace' ), $this->umask ) === false )
        {// (Unable to write to the file)
            // Returning the value
            return false;
        }



        // Returning the value
        return $this;
    }



    # Returns [string|false]
    public function get_type (string $entry_path)
    {
        // (Getting the value)
        $abs_entry_path = Resource::select( $this->path . $entry_path )->normalize()->get_path();

        if ( $this->chroot )
        {// Value is true
            if ( !$this->verify_path( $entry_path ) ) return false;
        }



        // (Getting the value)
        $current_user = get_current_user();

        if ( $this->users )
        {// Value is not empty
            if ( !in_array( $current_user, $this->users ) )
            {// Match failed
                // Returning the value
                return false;
            }
        }



        // Returning the value
        return Resource::select( $abs_entry_path )->get_type();
    }

    # Returns [int|false]
    public function get_size (string $entry_path)
    {
        // (Getting the value)
        $abs_entry_path = Resource::select( $this->path . $entry_path )->normalize()->get_path();

        if ( $this->chroot )
        {// Value is true
            if ( !$this->verify_path( $entry_path ) ) return false;
        }



        // (Getting the value)
        $current_user = get_current_user();

        if ( $this->users )
        {// Value is not empty
            if ( !in_array( $current_user, $this->users ) )
            {// Match failed
                // Returning the value
                return false;
            }
        }



        if ( Resource::select( $abs_entry_path )->is_file() )
        {// Match OK
            // (Executing the command)
            $bytes = (int) trim( shell_exec("wc -c < \"$abs_entry_path\"") );
        }
        else
        {// Match failed
            // (Executing the command)
            $bytes = (int) explode( ' ', trim( shell_exec("du -s \"$abs_entry_path\"") ) )[0];
        }



        // Returning the value
        return $bytes;
    }



    # Returns [self|false]
    public function make_dir (string $path)
    {
        // (Getting the value)
        $abs_entry_path = Resource::select( $this->path . $path )->normalize()->get_path();

        if ( $this->chroot )
        {// Value is true
            if ( !$this->verify_path( $path ) ) return false;
        }



        // (Getting the value)
        $current_user = get_current_user();

        if ( $this->users )
        {// Value is not empty
            if ( !in_array( $current_user, $this->users ) )
            {// Match failed
                // Returning the value
                return false;
            }
        }



        if ( Directory::select( $abs_entry_path )->make( $this->umask ) === false )
        {// (Unable to make the directory)
            // Returning the value
            return false;
        }



        // Returning the value
        return $this;
    }

    # Returns [self|false]
    public function move (string $src_entry_path, string $dst_entry_path)
    {
        // (Getting the value)
        $abs_src_entry_path = Resource::select( $this->path . $src_entry_path )->normalize()->get_path();

        if ( $this->chroot )
        {// Value is true
            if ( !$this->verify_path( $src_entry_path ) ) return false;
        }



        // (Getting the value)
        $current_user = get_current_user();

        if ( $this->users )
        {// Value is not empty
            if ( !in_array( $current_user, $this->users ) )
            {// Match failed
                // Returning the value
                return false;
            }
        }



        // (Getting the value)
        $abs_dst_entry_path = Resource::select( $this->path . $dst_entry_path )->normalize()->get_path();

        if ( $this->chroot )
        {// Value is true
            if ( !$this->verify_path( $dst_entry_path ) ) return false;
        }



        if ( Resource::select( $abs_src_entry_path )->move( $abs_dst_entry_path ) === false )
        {// (Unable to move the resource)
            // Returning the value
            return false;
        }



        // Returning the value
        return $this;
    }

    # Returns [self|false]
    public function copy (string $src_entry_path, string $dst_entry_path)
    {
        // (Getting the value)
        $abs_src_entry_path = Resource::select( $this->path . $src_entry_path )->normalize()->get_path();

        if ( $this->chroot )
        {// Value is true
            if ( !$this->verify_path( $src_entry_path ) ) return false;
        }



        // (Getting the value)
        $current_user = get_current_user();

        if ( $this->users )
        {// Value is not empty
            if ( !in_array( $current_user, $this->users ) )
            {// Match failed
                // Returning the value
                return false;
            }
        }



        // (Getting the value)
        $abs_dst_entry_path = Resource::select( $this->path . $dst_entry_path )->normalize()->get_path();

        if ( $this->chroot )
        {// Value is true
            if ( !$this->verify_path( $dst_entry_path ) ) return false;
        }



        if ( Resource::select( $abs_src_entry_path )->copy( $abs_dst_entry_path ) === false )
        {// (Unable to copy the resource)
            // Returning the value
            return false;
        }



        // Returning the value
        return $this;
    }

    # Returns [self|false]
    public function remove (string $entry_path)
    {
        // (Getting the value)
        $abs_entry_path = Resource::select( $this->path . $entry_path )->normalize()->get_path();

        if ( $this->chroot )
        {// Value is true
            if ( !$this->verify_path( $entry_path ) ) return false;
        }



        // (Getting the value)
        $current_user = get_current_user();

        if ( $this->users )
        {// Value is not empty
            if ( !in_array( $current_user, $this->users ) )
            {// Match failed
                // Returning the value
                return false;
            }
        }



        if ( Resource::select( $abs_entry_path )->remove() === false )
        {// (Unable to remove the entry)
            // Returning the value
            return false;
        }



        // Returning the value
        return $this;
    }



    # Returns [bool]
    public function file_exists (string $path)
    {
        // (Getting the value)
        $abs_entry_path = Resource::select( $this->path . $path )->normalize()->get_path();

        if ( $this->chroot )
        {// Value is true
            if ( !$this->verify_path( $path ) ) return false;
        }



        // (Getting the value)
        $current_user = get_current_user();

        if ( $this->users )
        {// Value is not empty
            if ( !in_array( $current_user, $this->users ) )
            {// Match failed
                // Returning the value
                return false;
            }
        }



        // Returning the value
        return File::select( $abs_entry_path )->exists();
    }

    # Returns [bool]
    public function dir_exists (string $path)
    {
        // (Getting the value)
        $abs_entry_path = Resource::select( $this->path . $path )->normalize()->get_path();

        if ( $this->chroot )
        {// Value is true
            if ( !$this->verify_path( $path ) ) return false;
        }



        // (Getting the value)
        $current_user = get_current_user();

        if ( $this->users )
        {// Value is not empty
            if ( !in_array( $current_user, $this->users ) )
            {// Match failed
                // Returning the value
                return false;
            }
        }



        // Returning the value
        return Directory::select( $abs_entry_path )->exists();
    }



    # Returns [self|false]
    public function build ()
    {
        // (Setting the value)
        $basedir = '/';

        if ( !$this->dir_exists( $basedir ) )
        {// (Directory not found)
            if ( !$this->make_dir( $basedir ) )
            {// (Unable to make the directory)
                // Returning the value
                return false;
            }
        }



        // Returning the value
        return $this;
    }



    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return $this->path;
    }
}



?>