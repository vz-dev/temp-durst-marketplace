<?php
/**
 * Durst - project - FormFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 15.09.21
 * Time: 13:02
 */

namespace Pyz\Yves\CancelOrder\Form;

use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Yves\Kernel\AbstractFactory;
use Symfony\Component\Form\FormFactory as SymfonyFormFactory;
use Symfony\Component\Form\FormInterface;

class FormFactory extends AbstractFactory
{
    /**
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCancelOrderForm(
        array $options = []
    ): FormInterface
    {
        return $this
            ->getFormFactory()
            ->create(
                CancelOrderForm::class,
                null,
                $options
            );
    }

    /**
     * @return \Symfony\Component\Form\FormFactory
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getFormFactory(): SymfonyFormFactory
    {
        return $this
            ->getProvidedDependency(
                ApplicationConstants::FORM_FACTORY
            );
    }
}
