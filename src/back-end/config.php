<?php

$path = dirname(__FILE__, 2);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$conn = new Mysqli($_ENV['HOST'], $_ENV['USER'], $_ENV['PASS'], $_ENV['BASE']);