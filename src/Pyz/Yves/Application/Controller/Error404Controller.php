<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Yves\Application\Controller;

use Pyz\Shared\Config\Environment;
use Spryker\Yves\Kernel\Controller\AbstractController;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;

class Error404Controller extends AbstractController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        return $this->viewResponse([
            'error' => $this->getErrorMessage($request),
            'hideUserMenu' => true,
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     */
    protected function getErrorMessage(Request $request)
    {
        if (!Environment::isDevelopment()) {
            return '';
        }

        $exception = $request->query->get('exception');
        if ($exception instanceof FlattenException) {
            return $exception->getMessage();
        }

        return '';
    }
}
