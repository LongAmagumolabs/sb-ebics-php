<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/controller/getCert.php';

// use App\Factories\X509\BNPPX509Generator;

use AndrewSvirin\Ebics\Contracts\EbicsResponseExceptionInterface;
use AndrewSvirin\Ebics\Services\FileKeyRingManager;
use AndrewSvirin\Ebics\Models\Bank;
use AndrewSvirin\Ebics\Models\User;
use AndrewSvirin\Ebics\EbicsClient;
use AndrewSvirin\Ebics\Tests\Factories\X509\BNPPX509Generator;

// Prepare `workspace` dir in the __PATH_TO_WORKSPACES_DIR__ manually.
$keyRingRealPath = __DIR__ . '/mykeys/keyring.json';
$password = 'mysecret';
// Use __IS_CERTIFIED__ true for French banks, otherwise use false.
$keyRingManager = new FileKeyRingManager();
echo "longdeptrai";
$keyRing = $keyRingManager->loadKeyRing($keyRingRealPath, $password);
$bank = new Bank("EBIXQUAL", "https://server-ebics.webank.fr:28103/WbkPortalFileTransfert/EbicsProtocol", 'VERSION_24');
$bank->setIsCertified(true);
$user = new User("LONGSEO", "LONGSEO");
$client = new EbicsClient($bank, $user, $keyRing);
// var_dump($client->getUserSignature($type='A', $createNew=false));
$client->setX509Generator(new BNPPX509Generator); 

try {
    $client->INI();
    $keyRingManager->saveKeyRing($keyRing, $keyRingRealPath);
    $keyRingManager->loadKeyRing($keyRingRealPath, $password);
} catch (EbicsResponseExceptionInterface $exception) {
    echo json_encode(array("Etat" => "ERROR", "Data" => sprintf(
        "INI request failed. EBICS Error code : %s\nMessage : %s\nMeaning : %s",
        $exception->getResponseCode(),
        $exception->getMeaning()
    )));
    exit;
}
try {
    $client->HIA();
    $keyRingManager->saveKeyRing($keyRing, $keyRingRealPath);
    $keyRingManager->loadKeyRing($keyRingRealPath, $password);
} catch (EbicsResponseExceptionInterface $exception) {
    echo json_encode(array("Etat" => "ERROR", "Data" => sprintf(
        "HIA request failed. EBICS Error code : %s\nMessage : %s\nMeaning : %s",
        $exception->getResponseCode(),
        $exception->getMeaning()
    )));
    exit;
}

echo json_encode(array("Etat" => "SUCCESS", "Data" => ''));
