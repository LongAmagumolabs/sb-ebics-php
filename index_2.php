<?php
require __DIR__ .'/vendor/autoload.php';

use AndrewSvirin\Ebics\Contracts\EbicsResponseExceptionInterface;
use AndrewSvirin\Ebics\EbicsClient;
use AndrewSvirin\Ebics\Models\Bank;
use AndrewSvirin\Ebics\Models\User;
use AndrewSvirin\Ebics\Services\FileKeyringManager;

$keyRingRealPath = 'mykeys/keyring.json';
$password = 'mysecret';
// Use IS_CERTIFIED true for French banks, otherwise use false.
$keyRingManager = new FileKeyringManager();
$keyRing = $keyRingManager->loadKeyRing($keyRingRealPath, $password);

// $bank = new Bank("PFEBICS", "https://isotest.postfinance.ch/ebicsweb/ebicsweb");
// $user = new User("PFC00532", "PFC00532");
$bank = new Bank("EBIXQUAL", "https://server-ebics.webank.fr:28103/WbkPortalFileTransfert/EbicsProtocol");
$user = new User("LONGSEO", "LONGSEO");

$bank->setIsCertified(true);
$client = new EbicsClient($bank, $user, $keyRing);

try {
    $client->INI();
    $keyRingManager->saveKeyRing($keyRing, $keyRingRealPath);
    $keyRingManager->loadKeyRing($keyRingRealPath, $password);
} catch (EbicsResponseExceptionInterface $exception) {
    echo json_encode(array("Etat"=>"ERROR", "Data" => sprintf(
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
    echo json_encode(array("Etat"=>"ERROR", "Data" => sprintf(
    "HIA request failed. EBICS Error code : %s\nMessage : %s\nMeaning : %s",
    $exception->getResponseCode(),
    $exception->getMeaning()
    )));
    exit;
}

echo json_encode(array("Etat"=>"SUCCESS", "Data" =>''));