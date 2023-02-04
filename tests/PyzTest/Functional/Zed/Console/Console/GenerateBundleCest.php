<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PyzTest\Functional\Zed\Console\Console;

use PyzTest\Functional\Zed\Console\ConsoleConsoleTester;
use PyzTest\Functional\Zed\Console\Helper\ConsoleHelper;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Zed
 * @group Console
 * @group Console
 * @group GenerateBundleCest
 * Add your own group annotations below this line
 */
class GenerateBundleCest
{
    /**
     * @skip
     *
     * @param \PyzTest\Functional\Zed\Console\ConsoleConsoleTester $i
     *
     * @return void
     */
    public function generateZedBundle(ConsoleConsoleTester $i)
    {
        $i->runSprykerCommand('code:generate:module:zed Acme -vvv');
        $i->seeInShellOutput('Generated: Pyz/Zed/Acme/AcmeConfig.php');
        $i->seeInShellOutput('Generated: ZedBundleCodeGenerator');
        $bundleDir = codecept_data_dir() . ConsoleHelper::SANDBOX_DIR . 'src/Pyz/Zed/Acme';
        $i->seeFileFound('AcmeConfig.php', $bundleDir);
        $i->seeFileFound('AcmeDependencyProvider.php', $bundleDir);
        $i->seeFileFound('AcmeFacade.php', $bundleDir . '/Business');
    }
}
