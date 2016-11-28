<?php

use \classes\ClientRepository;

require_once dirname(__FILE__) . '/autoload.php';

$clientRepository = new ClientRepository();

$client = $clientRepository->getRandomClient();
var_dump($client);

$client->setStatus((int)!$client->getStatus());

$clientRepository->save($client);

