<?php

declare(strict_types=1);

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use Morozov\PhpTek2023\Message;

require __DIR__ . '/../dbal.php';
require __DIR__ . '/create-input-stream.php';

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
printf('Peak memory usage: %s bytes.' . PHP_EOL . PHP_EOL, number_format(memory_get_peak_usage()));

$msg = new Message($attachment);
$em->persist($msg);

printf('Entity persisted.' . PHP_EOL);
printf('Peak memory usage: %s bytes' . PHP_EOL . PHP_EOL, number_format(memory_get_peak_usage()));

$em->flush();

printf('Entity manager flushed.' . PHP_EOL);
printf('Peak memory usage: %s bytes' . PHP_EOL . PHP_EOL, number_format(memory_get_peak_usage()));

fclose($attachment);

// make sure we fetch the entities from the database
$em->clear();

$messages = $em->createQuery('SELECT m FROM Message m')->getResult();
foreach ($messages as $msg) {
    $output = fopen('/dev/null', 'w');
    $copied = stream_copy_to_stream($msg->attachment, $output);
    fclose($output);

    printf('#%d: Copied %s bytes' . PHP_EOL, $msg->id, number_format($copied));
}

printf('Peak memory usage: %s bytes' . PHP_EOL, number_format(memory_get_peak_usage()));
