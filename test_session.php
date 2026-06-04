<?php
session_start();
if (!isset($_SESSION['test_counter'])) {
    $_SESSION['test_counter'] = 0;
}
$_SESSION['test_counter']++;
echo "<h3>Session Debugger</h3>";
echo "Session ID: " . session_id() . "<br>";
echo "Counter: " . $_SESSION['test_counter'] . "<br>";
echo "Save Path: " . (session_save_path() ?: 'Default (sys_get_temp_dir)') . "<br>";
echo "Is Save Path Writeable: " . (is_writable(session_save_path() ?: sys_get_temp_dir()) ? 'Yes' : 'No') . "<br>";
echo "PHP version: " . phpversion() . "<br>";
