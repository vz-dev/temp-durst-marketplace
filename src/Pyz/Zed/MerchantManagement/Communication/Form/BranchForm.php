<?php
/**
 * Durst - project - BranchForm.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 04.04.18
 * Time: 10:56
 */

namespace Pyz\Zed\MerchantManagement\Communication\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;

class BranchForm extends AbstractBranchForm
{
    protected const LABEL_BUTTON_SUBMIT = 'Erstellen';

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired([
            self::OPTION_MERCHANT_OPTIONS,
            self::OPTION_SALUTATION_OPTIONS,
        ]);
    }
}
