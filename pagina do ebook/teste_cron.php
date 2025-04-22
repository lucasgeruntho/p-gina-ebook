<?php
file_put_contents(__DIR__ . "/cron_log.txt", "Rodou PHP em: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
