<?php

declare(strict_types=1);

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use Morozov\PhpTek2023\Message;

require __DIR__ . '/../dbal.php';
require __DIR__ . '/blob-functions.php';

// we are using the ORM here in order to hide the differences between the type of BLOBs
// offered by the different drivers
$config = ORMSetup::createAttributeMetadataConfiguration(
    [dirname(__DIR__) . '/../src'],
    true,
);

$conn = connect();

$conn->executeStatement('DROP TABLE IF EXISTS messages');

if ($conn->getDatabasePlatform() instanceof PostgreSQLPlatform) {
    $conn->executeStatement('DROP SEQUENCE IF EXISTS messages_id_seq');
}

$em = new Doctrine\ORM\EntityManager($conn, $config);

$schemaTool = new SchemaTool($em);
$schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());

$attachment = create_input_stream();
printf('Stream created.' . PHP_EOL);
print_peak_memory_usage();

$msg = new Message($attachment);
$em->persist($msg);

printf('Entity persisted.' . PHP_EOL);
print_peak_memory_usage();

$em->flush();

printf('Entity manager flushed.' . PHP_EOL);
print_peak_memory_usage();

fclose($attachment);

// make sure we fetch the entities from the database
$em->clear();

$messages = $em->createQuery('SELECT m FROM Message m')->getResult();
foreach ($messages as $msg) {
    $copied = copy_stream_to_dev_null($msg->attachment);

    printf('Copied %s.' . PHP_EOL, format_as_mebibytes($copied));
}

print_peak_memory_usage();
