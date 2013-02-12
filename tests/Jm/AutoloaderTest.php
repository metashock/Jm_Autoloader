<?php
/**
 *
 * @package Jm_Autoloader
 */
/**
 *
 * @package Jm_Autoloader
 */
class AutoloaderTest extends PHPUnit_Framework_TestCase
{

    /**
     * This test should be executed using process isolation as
     * it will cause a fatal error if autoloading does not work.
     * 
     * @return void
     */
    public function testAutoload() {
        $tmpdir = sys_get_temp_dir();
        $tmpfile = tempnam($tmpdir, 'Class');
        $classname = basename($tmpfile);
        // rename it to PHP so that the Autoloader can find it
        rename($tmpfile, $tmpfile . '.php');

        // register a shutdown function to make *sure* that
        // the tmpfile will be remove although autoloading fails
        register_shutdown_function(function() use ($tmpfile){
            if(file_exists($tmpfile . '.php')) {
                unlink($tmpfile . '.php');
            }
        });

        file_put_contents($tmpfile . '.php',
            '<?php class ' . $classname . '{}');

        set_include_path(implode(PATH_SEPARATOR, array(
            $tmpdir,
            get_include_path()
        )));


        require_once 'Jm/Autoloader.php';
        $a = new $classname();

        // another call to autoload should return true
        $ret = Jm_Autoloader::singleton()->autoload($classname);
        $this->assertTrue($ret, 'Failed to assign that the return value of'
            . ' autoload() on second call is true');
        
        // clean up
        unlink($tmpfile . '.php');
    }



    /**
     *  Test if it works to append or prepend a
     *  path to the search paths array
     */
    public function testAddPath() {
        $tmpdir = sys_get_temp_dir();
        $tmpfile = tempnam($tmpdir, 'Class');
        $classname = basename($tmpfile);
        // rename it to PHP so that the Autoloader can find it
        rename($tmpfile, $tmpfile . '.php');

        // register a shutdown function to make *sure* that
        // the tmpfile will be remove although autoloading fails
        register_shutdown_function(function() use ($tmpfile){
            if(file_exists($tmpfile . '.php')) {
                unlink($tmpfile . '.php');
            }
        });

        file_put_contents($tmpfile . '.php',
            '<?php class ' . $classname . '{}');

        require_once 'Jm/Autoloader.php';
        $ret = Jm_Autoloader::singleton()->addPath($tmpdir);
        new $classname();

        // another call to autoload should return true
        $ret = Jm_Autoloader::singleton()->autoload($classname);
        $this->assertTrue($ret, 'Failed to assign that the return value of'
            . ' autoload() on second call is true');

        $ret = Jm_Autoloader::singleton()->addPath($tmpdir, TRUE);

        // clean up
        unlink($tmpfile . '.php');
    }


    /**
     * Tries to clone the Jm_Autoloader singleton. This should cause
     * an exception
     *
     * @expectedException         Exception
     * @expectedExceptionMessage  Jm_Autoloader is a singleton. Cannot make a clone of it 
     */
    public function testClone() {
        require_once 'Jm/Autoloader.php';
        $al = Jm_Autoloader::singleton();
        clone $al;
    }
}

