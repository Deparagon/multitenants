<?php





shell_exec('dos2unix install.sh');
$res = shell_exec('bash install.sh');
unlink(dirname(__FILE__).'/install.sh');
echo $res;
unlink(__FILE__);
exit;
