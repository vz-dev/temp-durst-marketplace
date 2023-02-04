<?php
/**
 * Durst - project - BaseCampaignValidator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 23.07.21
 * Time: 14:59
 */

namespace Pyz\Zed\Campaign\Business\Validator;


use Pyz\Zed\Campaign\Business\CampaignFacadeInterface;
use Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface;

class BaseCampaignValidator
{
    /**
     * @var \Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\Campaign\Business\CampaignFacadeInterface
     */
    protected $facade;

    /**
     * BaseCampaignValidator constructor.
     * @param \Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\Campaign\Business\CampaignFacadeInterface $facade
     */
    public function __construct(
        CampaignQueryContainerInterface $queryContainer,
        CampaignFacadeInterface $facade
    )
    {
        $this->queryContainer = $queryContainer;
        $this->facade = $facade;
    }
}
