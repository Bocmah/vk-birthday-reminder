<?php

$dbOptions = parse_url(getenv('DATABASE_URL'));

return [
    "driver" => "pdo_pgsql",
    "user" => $dbOptions["user"],
    "password" => $dbOptions["pass"],
    "host" => $dbOptions["host"],
    "port" => $dbOptions["port"],
    "dbname" => ltrim($dbOptions["path"],'/')
];