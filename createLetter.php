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
$ebicsBankLetter = new \AndrewSvirin\Ebics\EbicsBankLetter();

// Prepare `workspace` dir in the __PATH_TO_WORKSPACES_DIR__ manually.
$keyRingRealPath = __DIR__ .'/mykeys/keyring.json';
$password = 'mysecret';
// Use __IS_CERTIFIED__ true for French banks, otherwise use false.
$keyRingManager = new FileKeyRingManager();
$keyRing = $keyRingManager->loadKeyRing($keyRingRealPath, $password);
$bank = new Bank("EBIXQUAL", "https://server-ebics.webank.fr:28103/WbkPortalFileTransfert/EbicsProtocol");
$bank->setIsCertified(true);
$bank->setServerName("BNP Paribas");
$user = new User("LONGSEO", "LONGSEO");
$client = new EbicsClient($bank, $user, $keyRing);

$bankLetter = $ebicsBankLetter->prepareBankLetter(
    $client->getBank(),
    $client->getUser(),
    $client->getKeyRing()
);

$pdf = $ebicsBankLetter->formatBankLetter($bankLetter, $ebicsBankLetter->createPdfBankLetterFormatter());
file_put_contents('letter.pdf', $pdf);
