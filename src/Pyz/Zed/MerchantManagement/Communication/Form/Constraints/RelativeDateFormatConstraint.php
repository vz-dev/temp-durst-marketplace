<?php
/**
 * Durst - project - RelativeDateFormatConstraint.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-11
 * Time: 20:47
 */

namespace Pyz\Zed\MerchantManagement\Communication\Form\Constraints;


use Symfony\Component\Validator\Constraint;

class RelativeDateFormatConstraint extends Constraint
{
    /**
     * @var string
     */
    protected  $message = 'Es muss eine PHP-konforme relative Zeitangabe verwendet werden. Siehe Hinweis-Text.';

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
