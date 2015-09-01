<?php
// create_product.php
require_once __DIR__."/../bootstrap.php";

$newProductName = $argv[1];

$entityManager = $container['entityManager'];

$product = new ExampleApp\Orm\Product();
$product->setName($newProductName);
$product->setExtra(['ha' => ['bla', 'ble']]);

$entityManager->persist($product);
$entityManager->flush();

echo "Created Product with ID " . $product->getId() . "\n";