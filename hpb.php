<?php
require __DIR__ .'/vendor/autoload.php';
require __DIR__ .'/controller/getCert.php';

// use App\Factories\X509\BNPPX509Generator;

use AndrewSvirin\Ebics\Contracts\EbicsResponseExceptionInterface;
use AndrewSvirin\Ebics\Services\FileKeyRingManager;
use AndrewSvirin\Ebics\Models\Bank;
use AndrewSvirin\Ebics\Models\User;
use AndrewSvirin\Ebics\EbicsClient;
use AndrewSvirin\Ebics\Tests\Factories\X509\BNPPX509Generator;

// Prepare `workspace` dir in the __PATH_TO_WORKSPACES_DIR__ manually.
$keyRingRealPath = __DIR__ .'/mykeys/keyring.json';
$password = 'mysecret';
// Use __IS_CERTIFIED__ true for French banks, otherwise use false.
$keyRingManager = new FileKeyRingManager();
$keyRing = $keyRingManager->loadKeyRing($keyRingRealPath, $password);
$bank = new Bank("EBIXQUAL", "https://server-ebics.webank.fr:28103/WbkPortalFileTransfert/EbicsProtocol");
$bank->setIsCertified(true);
$user = new User("LONGSEO", "LONGSEO");
$client = new EbicsClient($bank, $user, $keyRing);
$client->setX509Generator(new BNPPX509Generator);

try {
    $client->HPB();
    $keyRingManager->saveKeyRing($keyRing, $keyRingRealPath);
} catch (EbicsResponseExceptionInterface $exception) {
    echo sprintf(
        "HPB request failed. EBICS Error code : %s\nMessage : %s\nMeaning : %s",
        $exception->getResponseCode(),
        $exception->getMeaning()
    );
}

echo json_encode(array("Etat"=>"SUCCESS", "Data" =>''));