<?php
/**
 * Durst - project - ExportOpenOrdersManager.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-12-09
 * Time: 23:09
 */

namespace Pyz\Zed\Integra\Business\Model;


use DateTime;
use Generated\Shared\Transfer\IntegraCredentialsTransfer;
use Pyz\Zed\Integra\Business\Model\Connection\FtpManagerInterface;
use Pyz\Zed\Integra\Business\Model\Connection\WebServiceManagerInterface;
use Pyz\Zed\Integra\Business\Model\Export\ExportInterface;
use Pyz\Zed\Integra\IntegraConfig;
use Symfony\Component\Filesystem\Filesystem;

class ExportOpenOrdersManager extends ExportManager implements ExportManagerInterface
{
    /**
     * @var WebServiceManagerInterface
     */
    protected $webServiceManager;

    public function __construct(
        IntegraConfig $config,
        Filesystem $filesystem,
        IntegraCredentialsInterface $credentials,
        FtpManagerInterface $ftpManager,
        ExportInterface $export,
        WebServiceManagerInterface $webServiceManager
    ) {
        $this->config = $config;
        $this->filesystem = $filesystem;
        $this->credentials = $credentials;
        $this->ftpManager = $ftpManager;
        $this->export = $export;
        $this->webServiceManager = $webServiceManager;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     *
     * @return void
     */
    public function exportOrders(int $idBranch): void
    {
        $this->iterations = 0;
        $data = $this->export->getMappedData($idBranch);
        $credentials = $this
            ->credentials
            ->getCredentialsByIdBranch($idBranch);

        $this->updateDeliveryTimesForCustomer($credentials, $data);

        $absoluteFilename = $this->writeCsvFile($data);
        $this
            ->ftpManager
            ->sendFile(
                $credentials,
                $absoluteFilename,
                $this->export->getExportType()
            );

        $this->export->updateOrders();
        $this->deleteTmpFile($absoluteFilename);
    }

    /**
     * @param IntegraCredentialsTransfer $credentials
     * @param array $data
     */
    protected function updateDeliveryTimesForCustomer(IntegraCredentialsTransfer $credentials, array $data)
    {
        $this->convertDataToAssoc($data);

        foreach ($data as $item)
        {
            $this
                ->webServiceManager
                ->addDeliveryTimesToCustomer(
                    $credentials,
                    $item['Kundennr.'],
                    $this->reformatIsoDateToGermanFormat($item['Lieferzeit Start']),
                    $this->reformatIsoDateToGermanFormat($item['Lieferzeit End']),
                    $this->getDayOfWeekFromDateString($item['Lieferzeit Start'])
                );
        }
    }

    /**
     * @param string $dateString
     * @return string
     */
    protected function reformatIsoDateToGermanFormat(string $dateString) : string
    {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $dateString);
        return  $date->format('d.m.Y H:i:s');
    }

    /**
     * @param string $dateString
     * @return int
     */
    protected function getDayOfWeekFromDateString(string $dateString) : int
    {
        return date('N', $dateString);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function convertDataToAssoc(array $data) : array
    {
        dump($data);
        die();
    }
}
