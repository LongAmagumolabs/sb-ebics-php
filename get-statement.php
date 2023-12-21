<?php
require __DIR__ .'/vendor/autoload.php';
require __DIR__ .'/controller/getCert.php';

use AndrewSvirin\Ebics\Exceptions\NoDownloadDataAvailableException;
use AndrewSvirin\Ebics\Contracts\EbicsResponseExceptionInterface;
use AndrewSvirin\Ebics\Services\FileKeyRingManager;
use AndrewSvirin\Ebics\Models\Bank;
use AndrewSvirin\Ebics\Models\User;
use AndrewSvirin\Ebics\EbicsClient;

// Prepare `workspace` dir in the __PATH_TO_WORKSPACES_DIR__ manually.
$keyRingRealPath = __DIR__ . '/mykeys/keyring.json';
$password = 'mysecret';
// Use __IS_CERTIFIED__ true for French banks, otherwise use false.
$keyRingManager = new FileKeyRingManager();
$keyRing = $keyRingManager->loadKeyRing($keyRingRealPath, $password);
$bank = new Bank("EBIXQUAL", "https://server-ebics.webank.fr:28103/WbkPortalFileTransfert/EbicsProtocol");
$bank->setIsCertified(true);
$user = new User("LONGSEO", "LONGSEO");
$client = new EbicsClient($bank, $user, $keyRing);

try {
    /* @var \AndrewSvirin\Ebics\EbicsClient $client */
    //Fetch data from your bank
    $fdl = $client->FDL('camt.xxx.cfonb120.stm');
    //Plain format (like CFONB)
    $content = $fdl->getData();
    //XML format (Like MT942)
    $xmlContent = $fdl->getDataDocument();
} catch (NoDownloadDataAvailableException $exception) {
    echo "No data to download today !";
} catch (EbicsResponseExceptionInterface $exception) {
    echo sprintf(
        "Download failed. EBICS Error code : %s\nMessage : %s\nMeaning : %s",
        $exception->getResponseCode(),
        $exception->getMeaning()
    );
}
