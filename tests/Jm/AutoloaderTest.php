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
     *  Tests if it works to append or prepend a
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

        // clean up
        unlink($tmpfile . '.php');
    }


    /**
     * @see http://www.php.net/manual/de/function.tempnam.php#61436
     * @author Ron Korving
     */
    protected function tempdir($dir, $prefix='', $mode=0700) {
        if (substr($dir, -1) != '/') {
            $dir .= '/';
        }
        do {
            $path = $dir.$prefix.mt_rand(0, 9999999);
        } while (!mkdir($path, $mode));
        return $path;
    }


    /**
     * Thats that a class in path that has been prepended,
     * and has the same names as a class in a path that has been
     * appended, will get loaded in advance
     */
    public function testPrependPath() {
        $ptmpdir = $this->tempdir(sys_get_temp_dir());
        $atmpdir = $this->tempdir(sys_get_temp_dir());

        $ptmpfile = tempnam($ptmpdir, 'Class');
        $atmpfile = $atmpdir . '/' . basename($ptmpfile);
        file_put_contents($atmpfile, '');

        $classname = basename($ptmpfile);

        // rename it to PHP so that the Autoloader can find it
        rename($ptmpfile, $ptmpfile . '.php');
        rename($atmpfile, $atmpfile . '.php');

        // register a shutdown function to make *sure* that
        // the tmpfile will be remove although autoloading fails
        register_shutdown_function(function() use ($ptmpfile, $atmpfile){
            if(file_exists($ptmpfile . '.php')) {
                unlink($ptmpfile . '.php');
            }
            if(file_exists($atmpfile . '.php')) {
                unlink($atmpfile . '.php');
            }
        });

        file_put_contents($ptmpfile . '.php',
            '<?php class Namespace_' . $classname . '{ function a() {return 1;}}');

        file_put_contents($atmpfile . '.php',
            '<?php class Namespace_' . $classname . '{ function a() {return 2;}}');

        require_once 'Jm/Autoloader.php';
        $ret = Jm_Autoloader::singleton()->addPath($atmpdir, 'Namespace');
        $ret = Jm_Autoloader::singleton()->addPath($ptmpdir, 'Namespace', TRUE);

        $classname = 'Namespace_' . $classname;
        $o = new $classname();
        $this->assertEquals($o->a(), 1);

        // clean up
        unlink($ptmpfile . '.php');       
        unlink($atmpfile . '.php');       
    }


    /**
     *  Tests if it works to append or prepend a namespace prefixed
     *  path to the search paths array
     */
    public function testAddNamespacePrefixedPath() {
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
            '<?php class Test_Namespace_' . $classname . '{}');

        require_once 'Jm/Autoloader.php';
        $ret = Jm_Autoloader::singleton()->addPath($tmpdir, 'Test_Namespace');
        $classname = 'Test_Namespace_' . $classname;
        new $classname();

        // another call to autoload should return true
        $ret = Jm_Autoloader::singleton()->autoload($classname);
        $this->assertTrue($ret, 'Failed to assign that the return value of'
            . ' autoload() on second call is true');

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

