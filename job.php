<?php
if ($argc > 0) {
    fwrite(STDOUT, '# of arguments: ' . $argc . "\r\n");
    foreach ($argv as $k => $v) {
        fwrite(STDOUT, $k+1 . ': ' . $v . "\r\n");
    }
}
?>
