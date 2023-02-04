<?php
/**
 * Durst - project - TourStateMachineHandlerPlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-07
 * Time: 14:04
 */


namespace Pyz\Zed\Tour\Communication\Plugin\StateMachine;


use Generated\Shared\Transfer\StateMachineItemTransfer;
use InvalidArgumentException;
use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Pyz\Zed\Tour\Communication\Plugin\Command\ExportGoods;
use Pyz\Zed\Tour\Communication\Plugin\Command\ExportReturnAuto;
use Pyz\Zed\Tour\Communication\Plugin\Command\ExportReturnManual;
use Pyz\Zed\Tour\Communication\Plugin\Command\ForceEmptyExport;
use Pyz\Zed\Tour\Communication\Plugin\Command\NotifyMerchantGoods;
use Pyz\Zed\Tour\Communication\Plugin\Command\NotifyMerchantReturn;
use Pyz\Zed\Tour\Communication\Plugin\Command\PlanTour;
use Pyz\Zed\Tour\Communication\Plugin\Condition\AllOrdersClosed;
use Pyz\Zed\Tour\Communication\Plugin\Condition\AllOrdersClosedOnEnter;
use Pyz\Zed\Tour\Communication\Plugin\Condition\AreGoodsExported;
use Pyz\Zed\Tour\Communication\Plugin\Condition\BranchUsesEdiExportV2;
use Pyz\Zed\Tour\Communication\Plugin\Condition\HasValidOrders;
use Pyz\Zed\Tour\Communication\Plugin\Condition\IsAutoEdiExportEnabled;
use Pyz\Zed\Tour\Communication\Plugin\Condition\IsEmptyExportForced;
use Pyz\Zed\Tour\Communication\Plugin\Condition\IsReturnExported;
use Pyz\Zed\Tour\Communication\TourCommunicationFactory;
use Pyz\Zed\Tour\TourConfig;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\StateMachine\Dependency\Plugin\StateMachineHandlerInterface;

/**
 * Class TourStateMachineHandlerPlugin
 * @package Pyz\Zed\Tour\Communication\Plugin\StateMachine
 * @method TourFacadeInterface getFacade()
 * @method TourConfig getConfig()
 * @method TourCommunicationFactory getFactory()
 */
class TourStateMachineHandlerPlugin extends AbstractPlugin implements StateMachineHandlerInterface
{
    public const STATE_MACHINE_NAME = 'Tour';
    public const PROCESS_WHOLESALE_TOUR = 'WholesaleTour';

    /**
     * List of command plugins for this state machine for all processes. Array key is identifier in SM XML file.
     *
     * [
     *   'Command/Plugin' => new Command(),
     *   'Command/Plugin2' => new Command2(),
     * ]
     *
     * @return array
     * @api
     *
     */
    public function getCommandPlugins(): array
    {
        return [
            ExportGoods::COMMAND_NAME => new ExportGoods(),
            ExportReturnAuto::COMMAND_NAME => new ExportReturnAuto(),
            ExportReturnManual::COMMAND_NAME => new ExportReturnManual(),
            ForceEmptyExport::COMMAND_NAME => new ForceEmptyExport(),
            NotifyMerchantGoods::COMMAND_NAME => new NotifyMerchantGoods(),
            NotifyMerchantReturn::COMMAND_NAME => new NotifyMerchantReturn(),
            PlanTour::COMMAND_NAME => new PlanTour(),
        ];
    }

    /**
     * List of condition plugins for this state machine for all processes. Array key is identifier in SM XML file.
     *
     *  [
     *   'Condition/Plugin' => new Condition(),
     *   'Condition/Plugin2' => new Condition2(),
     * ]
     *
     * @return array
     * @api
     *
     */
    public function getConditionPlugins(): array
    {
        return [
            HasValidOrders::CONDITION_NAME => new HasValidOrders(),
            AreGoodsExported::CONDITION_NAME => new AreGoodsExported(),
            AllOrdersClosed::CONDITION_NAME => new AllOrdersClosed(),
            AllOrdersClosedOnEnter::CONDITION_NAME => new AllOrdersClosedOnEnter(),
            IsAutoEdiExportEnabled::CONDITION_NAME => new IsAutoEdiExportEnabled(),
            IsEmptyExportForced::CONDITION_NAME => new IsEmptyExportForced(),
            IsReturnExported::CONDITION_NAME => new IsReturnExported(),
            BranchUsesEdiExportV2::CONDITION_NAME => new BranchUsesEdiExportV2(),
        ];
    }

    /**
     * Name of state machine used by this handler.
     *
     * @return string
     * @api
     *
     */
    public function getStateMachineName(): string
    {
        return self::STATE_MACHINE_NAME;
    }

    /**
     * List of active processes used for this state machine.
     *
     * [
     *   'ProcessName',
     *   'ProcessName2 ,
     * ]
     *
     * @return string[]
     * @api
     *
     */
    public function getActiveProcesses(): array
    {
        return [
            self::PROCESS_WHOLESALE_TOUR
        ];
    }

    /**
     * Provide initial state name for item when state machine initialized. Using process name.
     *
     * @param string $processName
     *
     * @return string
     * @api
     *
     */
    public function getInitialStateForProcess($processName): string
    {
        switch ($processName) {
            case self::PROCESS_WHOLESALE_TOUR:
                return $this
                    ->getConfig()
                    ->getStateMachineInitialState();
        }

        throw new InvalidArgumentException(
            sprintf(
                'Initial state for process "%s" not found.',
                $processName
            )
        );
    }

    /**
     * This method is called when state of item was changed, client can create custom logic for example update it's related table with new stateId and processId.
     * StateMachineItemTransfer:identifier is id of entity from client.
     *
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return bool
     * @api
     *
     */
    public function itemStateUpdated(StateMachineItemTransfer $stateMachineItemTransfer): bool
    {
        return $this
            ->getFacade()
            ->itemStateUpdate(
                $stateMachineItemTransfer
            );
    }

    /**
     * This method should return all list of StateMachineItemTransfer, with (identifier, IdStateMachineProcess, IdItemState)
     *
     * @param int[] $stateIds
     *
     * @return \Generated\Shared\Transfer\StateMachineItemTransfer[]
     * @api
     *
     */
    public function getStateMachineItemsByStateIds(array $stateIds = []): array
    {
        return $this
            ->getFacade()
            ->getStateMachineItemsByStateIds(
                $stateIds
            );
    }
}
