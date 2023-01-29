<?php

use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;

require __DIR__ . '/../dbal.php';

// we are using the ORM here in order to hide the differences between the type of BLOBs
// offered by the different drivers
$config = ORMSetup::createAttributeMetadataConfiguration(
    [dirname(__DIR__) . '/../src'],
    true,
);

$conn = connect();

$conn->executeStatement('DROP TABLE IF EXISTS messages');
//$conn->executeStatement('DROP SEQUENCE IF EXISTS messages_id_seq');
$em = new Doctrine\ORM\EntityManager($conn, $config);

$schemaTool = new SchemaTool($em);
$schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());

$msg1 = new Message('ABC');
$em->persist($msg1);

$msg2 = new Message('XYZ');
$em->persist($msg2);

$em->flush();

// make sure we fetch the entities from the database
$em->clear();

$messages = $em->createQuery('SELECT m FROM Message m')->getResult();
foreach ($messages as $msg) {
    printf('#%d: %s' . PHP_EOL, $msg->id, stream_get_contents($msg->attachment));
}
