<?php 
$env = $app->detectEnvironment(function() {
    return file_exists(dirname(__FILE__) . '/../.env.php')
        ?
        'production'
        :
        'local';
});

 ?>