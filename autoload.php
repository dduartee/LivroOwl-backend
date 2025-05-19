<?php
function autoLoader($class) {
  $class = str_replace('\\', '/', $class);
  $file = __DIR__ . '/' . $class . '.php';
  if(file_exists($file)) {
    include_once($file);
    return 1;
  } else {
    echo "Arquivo $file não existe...";
    return 0;
  }
}
spl_autoload_register('autoLoader');