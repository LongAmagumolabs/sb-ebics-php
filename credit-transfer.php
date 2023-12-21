<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/controller/getCert.php';

use AndrewSvirin\Ebics\Builders\CustomerCreditTransfer\CustomerCreditTransferBuilder;
use AndrewSvirin\Ebics\Contexts\FULContext;
use AndrewSvirin\Ebics\Exceptions\NoDownloadDataAvailableException;
use AndrewSvirin\Ebics\Contracts\EbicsResponseExceptionInterface;
use AndrewSvirin\Ebics\Services\FileKeyRingManager;
use AndrewSvirin\Ebics\Models\Bank;
use AndrewSvirin\Ebics\Models\User;
use AndrewSvirin\Ebics\EbicsClient;
use AndrewSvirin\Ebics\Models\Document;

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
    $context = new FULContext();

    $builder = new CustomerCreditTransferBuilder();

    $creditorData = [
        ['MARKDEF1820', 'DE09820000000083001503', 'Creditor Name 1', 100.10, 'EUR', 'Test payment  1'],
        ['GIBASKBX', 'SK4209000000000331819272', 'Creditor Name 2', 200.02, 'EUR', 'Test payment  2'],
        ['GIBASKBX', 'SK4209000000000331819272', 'Creditor Name 3', 200.02, 'EUR', 'Test payment  3'],
        ['GIBASKBX', 'SK4209000000000331819272', 'Creditor Name 4', 200.02, 'EUR', 'Test payment  4'],
        ['GIBASKBX', 'SK4209000000000331819272', 'Creditor Name 5', 200.02, 'EUR', 'Test payment  5'],
        ['GIBASKBX', 'SK4209000000000331819272', 'Creditor Name 6', 200.02, 'EUR', 'Test payment  6'],
        ['GIBASKBX', 'SK4209000000000331819272', 'Creditor Name 7', 200.02, 'EUR', 'Test payment  7'],
        ['GIBASKBX', 'SK4209000000000331819272', 'Creditor Name 8', 200.02, 'EUR', 'Test payment  8'],
        ['GIBASKBX', 'SK4209000000000331819272', 'Creditor Name 9', 200.02, 'EUR', 'Test payment  9'],
        ['GIBASKBX', 'SK4209000000000331819272', 'Creditor Name 10', 200.02, 'EUR', 'Test payment  10'],
        ['GIBASKBX', 'SK4209000000000331819272', 'Creditor Name 11', 200.02, 'EUR', 'Test payment  11'],
    ];

    $builder->createInstance('ZKBKCHZZ80A', 'SE7500800000000000001123', 'Debitor Name');
    foreach ($creditorData as $data) {
        $builder->addTransaction($data[0], $data[1], $data[2], $data[3], $data[4], $data[5]);
    }
    $customerCreditTransfer = $builder->popInstance();

    var_dump($customerCreditTransfer);
    $ful = $client->FUL('pain.001.001.03.sct', $customerCreditTransfer, $context);
    $content = $ful->getDataDocument();
} catch (NoDownloadDataAvailableException $exception) {
    echo "No data to download today !";
    var_dump($exception->getResponse());
} catch (EbicsResponseExceptionInterface $exception) {
    echo "Download failed. EBICS Error code : %s\nMessage : %s\nMeaning : %s",
    var_dump($exception->getResponse());
    var_dump($exception->getResponseCode());
    var_dump($exception->getMeaning());
}
