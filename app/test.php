<?

require_once 'autoloader.php';

$storage = new \Lib\Locations\Storage\PhpArray();


$locationsCollection = new \Lib\Locations\LocationsCollection($storage);


$location1 = new \Lib\Locations\Location();
$location1->setAddress('Maciejewicza 34/6, 71-004 Szczecin')
    ->setDescription('Nessun netmedia')
    ->setLatitude(-0.1121)
    ->setLongitude(1.3312);

$location2 = new \Lib\Locations\Location();
$location2->setAddress('Zbożowa 4, 71-004 Szczecin')
    ->setDescription('Home.pl')
    ->setLatitude(-0.145)
    ->setLongitude(1.111)
    ->setAsHeadquarters();

$location3 = new \Lib\Locations\Location();
$location3->setAddress('Zbożowa 4, 71-004 Szczecin')
    ->setDescription('Home.pl')
    ->setLatitude(-0.145)
    ->setLongitude(1.111);




$locationsCollection->addLocation($location1);
$locationsCollection->addLocation($location2);
$locationsCollection->addLocation($location3);

$location2->setDescription('Home.pl sp. z o.o.');

echo '<pre>';

print_r($locationsCollection->getAll());