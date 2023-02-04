<?php
/**
 * Durst - project - TestController.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.08.21
 * Time: 12:54
 */

namespace Pyz\Zed\Jwt\Communication\Controller;

use Generated\Shared\Transfer\JwtParameterTransfer;
use Generated\Shared\Transfer\JwtTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;

/**
 * @method \Pyz\Zed\Jwt\Business\JwtFacadeInterface getFacade()
 */
class TestController extends AbstractController
{
    public function testAction()
    {
        $now = new \DateTime('now');

        $jwt = (new JwtTransfer())
            ->setSubject('Subject')
            ->setNotBefore($now)
            ->setExpiration($now)
            ->setIssuedAt($now)
            ->setId('ich@du.de')
            ->setSign('oliver.gail@durst.shop');

        $para1 = (new JwtParameterTransfer())
            ->setKey('Key1')
            ->setValue('Value1');
        $para2 = (new JwtParameterTransfer())
            ->setKey('Key2')
            ->setValue('Value2');

        $jwt
            ->addAdditionalParameter(
                $para1
            )
            ->addAdditionalParameter(
                $para2
            );

        dump($this->getFacade()->validateJwt($jwt));

        $t = $this
            ->getFacade()
            ->prepareToken(
                $jwt
            );

        dump($t);

        $verified = $this->getFacade()->verifyJwt($t, 'OLIVER.gail@durst.shop');

        dump($verified);

        die;
    }
}
