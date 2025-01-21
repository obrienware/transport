<?php
/**
 * Our own class autoloader
 * All our classes are in the same directory and have the same 
 * namespace (Transport) which we need to ignore for the purpose 
 * of include the class file.
 */ 
// spl_autoload_register(function ($fullyQualifiedClassName) {
//   if (stripos($fullyQualifiedClassName, 'test') !== false) {
//     return;
//   }
//   echo "fullyQualifiedClassName: $fullyQualifiedClassName\n";
//   $className = array_pop(explode('\\', $fullyQualifiedClassName));
//   require_once $className.'.php';
// });



spl_autoload_register( 'my_psr4_autoloader' );

/**
 * An example of a project-specific implementation.
 *
 * @param string $class The fully-qualified class name.
 * @return void
 */
function my_psr4_autoloader($class) {
    // replace namespace separators with directory separators in the relative 
    // class name, append with .php
    $class_path = str_replace('\\', '/', $class);
    
    $file =  __DIR__ . '/classes/' . $class_path . '.php';
    // echo "file: $file\n";

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
}