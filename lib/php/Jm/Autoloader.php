<?php
/**
 * This package allows autoloading of php classes as introduced in PHP5.
 * As autoloading eases framework coding and is therefore a basic concept of 
 * modern PHP frameworks, Jm_Autoload is one of the very few core compontents
 * of Jm.
 *
 * PHP Version >=5.3.0
 * 
 * @category  Autoloading
 * @package   Jm
 * @author    Thorsten Heymann <info@metashock.net>
 * @copyright 2012 Thorsten Heymann
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD
 * @version   GIT: $$GITVERSION$$
 * @link      http://www.metashock.de/pirum
 * @since     0.1.0
 */
/*
 * Register the autoload method on file load
 */
spl_autoload_register(array(Jm_Autoloader::singleton(), 'autoload'), true);
/**
 * Singleton class that allows to configure autoloading and provides the
 * autoload method itself. This may change to a Jm_Autoloader_Resolver in
 * upcoming versions.
 *
 * @category  Autoloading
 * @package   Jm
 * @author    Thorsten Heymann <info@metashock.net>
 * @copyright 2012 Thorsten Heymann
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD
 * @version   GIT: $$GITVERSION$$
 * @link      http://www.metashock.de/pirum
 * @since     0.1.0
 */
class Jm_Autoloader
{

    /**
     * Singleton instance
     *
     * @var Jm_Autoloader
     */
    protected static $instance;
    
    
    /**
     * Array of classpaths
     *
     * @var array
     */
    protected $paths;
    
    
    /**
     * Initializes $paths with the contents of include_path. Note
     * that you should configure your include path before the first 
     * usage of autoloading.
     *
     * The constructor is protected because Jm_Autoloader is currently
     * implemented as a singleton. This may change.
     *
     * @return Jm_Autoloader
     */
    protected function __construct(){
        $includePath = get_include_path();
        $this->paths = explode(PATH_SEPARATOR, $includePath);
    }

   
    /**
     *  Spl autoload method. Works for libs that follow the PEAR conventions.
     *  This means, a class located in the file INCLUDE_PATH/Foo/Bar.php
     *  must be named Foo_Bar.
     *
     *  @param string $classname The name of the class that should 
     *                           be autoloaded
     *
     *  @return boolean
     */
    public function autoload ($classname) {
        // if the class where already loaded. should not happen
        if (class_exists($classname)) {
            return true;
        }

        $path = str_replace('_', '/', $classname) . '.php';
        
        foreach ($this->paths as $basePath) {
            $tail = $path;
            if (!is_string($basePath)) {
                $namespace = $basePath[1];
                $basePath = $basePath[0];
                $tail = str_replace($namespace . '/', '', $tail);
            }
            if (file_exists($basePath . '/' . $tail)) {
                include_once $basePath . '/' . $tail;
                return true;
            }
        }
        // @codeCoverageIgnoreStart
        // returning false makes PHP throwing a fatal error
        return false;
        // @codeCoverageIgnoreEnd
    }


    /**
     * Adds a path to the search path array
     *
     * @param string  $path      A classpath
     * @param string  $namespace The root namespace of $path. If not empty or
     *                           omitted Jm_Autoloader expects that classes in 
     *                           the path are named like $namespace_Class_Name
     * @param boolean $prepend   If set to true the path will be prepended 
     *                           instead of being appended. defaults to false
     *
     * @return Jm_Autoloader
     */
    public function addPath($path, $namespace = '', $prepend = false) {
        if ($prepend === true) {
            return $this->prependPath($path, $namespace);
        } else {
            if(!empty($namespace)) {
                $path = array (
                    $path, str_replace('_', '/', $namespace)
                );
            }           
            $this->paths []= $path;
        }
        return $this;
    }


    /**
     * Adds a path to the beginning of the search path array. 
     * Wrapper for addPath($path, TRUE);
     *
     * @param string  $path      A classpath
     * @param boolean $namespace The namespace for path. If set to true the
     *                           path will be pre-pended instead of being 
     *                           appended. defaults to false
     *
     * @return Jm_Autoloader
     */
    public function prependPath($path, $namespace = '') {
        if(!empty($namespace)) {
            $path = array (
                $path, str_replace('_', '/', $namespace)
            );
        }
        $this->paths = array($path) + $this->paths;
        return $this;
    }

    
    /**
     * Use it to get a reference of the singleton object. Usually the 
     * singleton object will be created the first time the autoloader is 
     * triggered by Zend. When you call this method before that the singleton
     * object will be created.
     *
     * @return Jm_Autoloader
     */
    public static function singleton() {
        if (!self::$instance) {
            self::$instance = new Jm_Autoloader();
        }
        return self::$instance;
    }


    /**
     * Prevent us from being cloned.
     *
     * @return void 
     * @throws Exception
     */
    public function __clone() {
        throw new Exception(sprintf(
            '%s is a singleton. Cannot make a clone of it',
            __CLASS__
        ));
    }
}

