<?php
/**
 * Durst - project - Document.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.01.20
 * Time: 16:43
 */

namespace Pyz\Zed\Easybill\Business\Resource;

use ArrayObject;
use DateTime;
use Generated\Shared\Transfer\EasybillDocumentPositionTransfer;
use Generated\Shared\Transfer\EasybillDocumentTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;
use Pyz\Shared\Easybill\EasybillConstants;
use Pyz\Shared\HttpRequest\HttpRequestConstants;
use Pyz\Zed\Easybill\Business\Exception\EasybillException;

class Document extends AbstractResource implements DocumentInterface
{
    protected const KEY_NUMBER = 'number';

    protected const URL = '/documents';
    protected const URL_FINALIZE = '/done';

    /**
     * {@inheritDoc}
     *
     * @param int $customerId
     * @return \Generated\Shared\Transfer\string
     */
    public function createInvoice(int $idCustomer): string
    {
        $requestTransfer = $this
        ->createHttpRequestTransfer(
            $this->createInvoiceDocumentTransfer($idCustomer)->toArray(true),
            HttpRequestConstants::HTTP_VERB_POST,
            $this->getUrl()
        );

        $responseTransfer = $this
            ->httpRequestService
            ->sendRequest($requestTransfer);

        $this->checkResponseCode($responseTransfer);
        $body = json_decode($responseTransfer->getBody(), true);
        $this->checkBody($body);
        $this->finalizeInvoice($body[static::KEY_ID]);

        return $body[static::KEY_NUMBER];
    }

    /**
     * @param int $id
     *
     * @return void
     */
    protected function finalizeInvoice(int $id): void
    {
        $requestTransfer = $this
            ->createHttpRequestTransfer(
                [],
                HttpRequestConstants::HTTP_VERB_PUT,
                $this->getFinalizeUrl($id)
            );

        $responseTransfer = $this
            ->httpRequestService
            ->sendRequest($requestTransfer);

        $this->checkFinalizeResponseCode($responseTransfer, $id);
    }

    /**
     * @param \Generated\Shared\Transfer\HttpResponseTransfer $response
     * @param int $id
     *
     * @return void
     */
    protected function checkFinalizeResponseCode(HttpResponseTransfer $response, int $id): void
    {
        parent::checkResponseCode($response);
        if ($response->getCode() === EasybillConstants::CODE_RESOURCE_NOT_FOUND) {
            throw EasybillException::resourceNotFound($id);
        }
    }

    /**
     * @param array $body
     *
     * @return void
     */
    protected function checkBody(array $body): void
    {
        parent::checkBody($body);
        if (array_key_exists(static::KEY_NUMBER, $body) !== true) {
            throw EasybillException::noNumberInBody();
        }
    }

    /**
     * @param int $customerId
     *
     * @return \Generated\Shared\Transfer\EasybillDocumentTransfer
     */
    protected function createInvoiceDocumentTransfer(int $customerId): EasybillDocumentTransfer
    {
        return (new EasybillDocumentTransfer())
            ->setOrderNumber('DE-B-01')
            ->setType(EasybillConstants::DOCUMENT_TYPE_INVOICE)
            ->setCustomerId($customerId)
            ->setDocumentDate(
                (new DateTime('now'))->format(EasybillConstants::DATE_FORMAT)
            )
            ->setItems(new ArrayObject([
                (new EasybillDocumentPositionTransfer())
                    ->setType(EasybillConstants::DOCUMENT_POSITION_TYPE_POSITION)
                    ->setDescription('Grundgebühr')
                    ->setItemType(EasybillConstants::DOCUMENT_POSITION_ITEM_TYPE_UNDEFINED)
                    ->setNumber('100000001')
                    ->setPosition(1)
                    ->setQuantity(1)
                    ->setSinglePriceNet(252100.8)
                    ->setVatPercent(19.0),
                (new EasybillDocumentPositionTransfer())
                    ->setType(EasybillConstants::DOCUMENT_POSITION_TYPE_POSITION)
                    ->setDescription('Transaktionskosten')
                    ->setItemType(EasybillConstants::DOCUMENT_POSITION_ITEM_TYPE_UNDEFINED)
                    ->setNumber('100000001')
                    ->setPosition(2)
                    ->setQuantity(413)
                    ->setSinglePriceNet(12.605)
                    ->setVatPercent(19.0),
            ]));
    }

    /**
     * @return string
     */
    protected function getUrl(): string
    {
        return $this->config->getEasybillApiUrl(static::URL);
    }

    /**
     * @param int $id
     *
     * @return string
     */
    protected function getFinalizeUrl(int $id): string
    {
        return sprintf(
            '%s%d%s',
            $this->getUrl(),
            $id,
            static::URL_FINALIZE
        );
    }
}
