<?php

use Ratchet\Server\IoServer;
use Baytek\Laravel\Content;


$server = IoServer::factory(
    new ContentBroadcaster(),
    8080
);

$server->run();