<?php
/**
 * Durst - project - MerchantConfig.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.12.21
 * Time: 11:45
 */

namespace Pyz\Zed\Merchant;

use Spryker\Zed\Kernel\AbstractBundleConfig;

class MerchantConfig extends AbstractBundleConfig
{
    public const DEFAULT_URL_BRANCH_CHOOSE = '/merchant-center/branch/choose';

    public const BRANCH_HASH_SALT = 'ip5sha%oreegh^i?aB#aihah0Ab7eib0';

    protected const HEIDELPAY_PRIVATE_KEY_KEY = 'Yy5CvsXiuwpncvRZY7ys';
    protected const HEIDELPAY_PRIVATE_KEY_VI = 'wKq6DXwuXh2dkf9nGWMw';
    protected const HEIDELPAY_PRIVATE_KEY_METHOD = 'AES-256-CBC';

    /**
     * @var array
     */
    protected $branchIgnorable = [
        [
            'bundle' => 'merchant-auth',
            'controller' => 'login',
            'action' => 'index'
        ],
        [
            'bundle' => 'merchant-auth',
            'controller' => 'logout',
            'action' => 'index'
        ],
        [
            'bundle' => 'merchant-auth',
            'controller' => 'logout',
            'action' => 'success'
        ],
        [
            'bundle' => 'merchant-center',
            'controller' => 'branch',
            'action' => 'choose'
        ],
        [
            'bundle' => 'merchant-center',
            'controller' => 'branch',
            'action' => 'decision'
        ],
        [
            'bundle' => 'terms-of-service',
            'controller' => 'form',
            'action' => 'index'
        ],
        [
            'bundle' => 'merchant-center',
            'controller' => 'order',
            'action' => 'driver-detail'
        ],
        [
            'bundle' => 'merchant-center',
            'controller' => 'deposit-tool',
            'action' => 'index'
        ],
        [
            'bundle' => 'merchant-center',
            'controller' => 'order',
            'action' => 'driver-detail-frame'
        ],
    ];

    /**
     * @return array
     */
    public function getBranchIgnorable()
    {
        return $this->branchIgnorable;
    }

    /**
     * @return string
     */
    public function getBranchChooseUrl()
    {
        return static::DEFAULT_URL_BRANCH_CHOOSE;
    }

    /**
     * @return string
     */
    public function getBranchHashSalt()
    {
        return static::BRANCH_HASH_SALT;
    }

    /**
     * @return string
     */
    public function getHeidelpayPrivateKeyKey(): string
    {
        return static::HEIDELPAY_PRIVATE_KEY_KEY;
    }

    /**
     * @return string
     */
    public function getHeidelpayPrivateKeyVI(): string
    {
        return static::HEIDELPAY_PRIVATE_KEY_VI;
    }

    /**
     * @return string
     */
    public function getHeidelpayPrivateKeyMethod(): string
    {
        return static::HEIDELPAY_PRIVATE_KEY_METHOD;
    }
}
