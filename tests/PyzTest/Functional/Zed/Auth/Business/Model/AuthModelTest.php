<?php
namespace PyzTest\Functional\Zed\Auth\Business\Model;

use Codeception\Test\Unit;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\ValidationData;
use Orm\Zed\Driver\Persistence\DstDriverQuery;
use Pyz\Zed\Auth\AuthConfig;
use Pyz\Zed\Auth\Business\Model\DriverAuth;
use Pyz\Zed\Auth\Business\Model\Jwt;
use Pyz\Zed\Auth\Business\Model\JwtNumberGenerator;
use Pyz\Zed\Auth\Business\Model\Verify\DriverVerify;
use Pyz\Zed\Auth\Business\Model\Verify\SequenceVerify;
use Pyz\Zed\Auth\Business\Model\Verify\ValidationDataVerify;
use Pyz\Zed\Driver\Business\DriverFacade;
use Pyz\Zed\Driver\Business\Exception\DriverNotExistsException;
use Spryker\Zed\SequenceNumber\Business\SequenceNumberFacade;

class AuthModelTest extends Unit
{

    protected const DRIVER_EMAIL_ACTIVE = 'frida.fahrerin@durst.shop';
    protected const DRIVER_EMAIL_INACTIVE = 'fritz.fahrer@durst.shop';
    protected const DRIVER_PASSWORD = 'test123';

    protected const FAKE_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJodHRwOlwvXC93d3cuZHVyc3QuZGUiLCJhdWQiOiJodHRwOlwvXC93d3cuZHVyc3QuZGUiLCJqdGkiOiJvbGl2ZXIuZ2FpbEBkdXJzdC5zaG9wIiwiaWF0IjoxNTYwNzYwNTMyLCJuYmYiOjE1NjA3Mjk2MDAsImV4cCI6MTU2MDgxNjAwMCwic3ViIjoiT2xpdmVyIEdhaWwiLCJzZXF1ZW5jZSI6IjEiLCJpZERyaXZlciI6Mn0.Rv7ImKxhIy1EOGjfHb3DhXdBKELOXr5VK936XUYuaauNt8Cj_Koa3OqzuPMp_V4J7TM-HRexh_GYkkGMr-DptA';
    protected const FUTURE_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJodHRwOlwvXC93d3cuZHVyc3QuZGUiLCJhdWQiOiJodHRwOlwvXC93d3cuZHVyc3QuZGUiLCJqdGkiOiJmcmlkYS5mYWhyZXJpbkBkdXJzdC5zaG9wIiwiaWF0IjoxNTYwOTM2ODMwLCJuYmYiOjE1NjA5MDI0MDAsImV4cCI6MjE3NzM2NjQwMCwic3ViIjoiRnJpZGEgRmFocmVyaW4iLCJzZXF1ZW5jZSI6IjEiLCJpZERyaXZlciI6MX0.wyZAPUJ3ST08Ivm3-XmN3B4XQ2ZfnuRhCumuMsr6CvglcKDWK1k9NJeLqPmzoKp65SpduUgXgC1bs9Pa8p7gvQ';
    protected const PAST_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJodHRwOlwvXC93d3cuZHVyc3QuZGUiLCJhdWQiOiJodHRwOlwvXC93d3cuZHVyc3QuZGUiLCJqdGkiOiJmcmlkYS5mYWhyZXJpbkBkdXJzdC5zaG9wIiwiaWF0IjoxNTYwOTM3MDkzLCJuYmYiOjE1NjA5MDI0MDAsImV4cCI6MCwic3ViIjoiRnJpZGEgRmFocmVyaW4iLCJzZXF1ZW5jZSI6IjEiLCJpZERyaXZlciI6MX0.aq_sGyYlPCoUfcKCZNDp9w1Sw6Ll4j5eJWAv37T59rryePzkGDx8dhW7-h1TEIZiD1RSWzfnnqJJuNyWZ3Wwpg';

    protected const DRIVER_ID = 1;

    protected const TOKEN_FORMAT = '%s.%s.%s';

    /**
     * @var \PyzTest\Functional\Zed\Auth\AuthBusinessTester
     */
    protected $tester;

    /**
     * @var \Pyz\Zed\Auth\Business\Model\DriverAuthInterface
     */
    protected $authModel;

    /**
     * @var \Pyz\Zed\Auth\Business\Model\JwtInterface
     */
    protected $jwt;

    /**
     * @return void
     */
    protected function _before(): void
    {
        $authConfig = new AuthConfig();

        $this->jwt = new Jwt(
            new Builder(),
            new Parser(),
            new Sha512(),
            $authConfig,
            new JwtNumberGenerator(
                new SequenceNumberFacade(),
                $authConfig->getJwtDriverTokenReferenceDefaults()
            ),
            [
                new DriverVerify(
                    new Parser(),
                    new Sha512()
                ),
                new SequenceVerify(
                    new Parser(),
                    new DriverFacade()
                ),
                new ValidationDataVerify(
                    new Parser(),
                    new ValidationData(),
                    new DriverFacade(),
                    $authConfig
                )
            ]
        );

        $this->authModel = new DriverAuth(
            $this->jwt,
            new DriverFacade()
        );
    }

    /**
     * @return void
     */
    protected function _after(): void
    {
    }

    /**
     * @return void
     */
    public function testTokenLifetimeIsInPast(): void
    {
        $authorized = $this
            ->authModel
            ->isAuthorized(self::PAST_TOKEN);

        $this
            ->assertFalse($authorized);
    }

    /**
     * @return void
     *
     * @throws \Pyz\Zed\Auth\Business\Exception\DriverEmailNotFoundException
     * @throws \Pyz\Zed\Auth\Business\Exception\JwtTokenNotGeneratedException
     */
    public function testActiveDriverCanAuthenticate(): void
    {
        $token = $this
            ->authModel
            ->authenticate(
                self::DRIVER_EMAIL_ACTIVE,
                self::DRIVER_PASSWORD
            );

        $this
            ->assertStringMatchesFormat(
                self::TOKEN_FORMAT,
                $token
            );
    }

    /**
     * @return void
     *
     * @throws \Pyz\Zed\Auth\Business\Exception\DriverEmailNotFoundException
     * @throws \Pyz\Zed\Auth\Business\Exception\JwtTokenNotGeneratedException
     */
    public function testInactiveDriverAuthenticationThrowsException(): void
    {
        $this
            ->expectException(DriverNotExistsException::class);

        $this
            ->authModel
            ->authenticate(
                self::DRIVER_EMAIL_INACTIVE,
                self::DRIVER_PASSWORD
            );
    }

    /**
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function testDriverIsAuthorized(): void
    {
        $this
            ->simulateLogin();

        $authorized = $this
            ->authModel
            ->isAuthorized(self::FUTURE_TOKEN);

        $this
            ->assertTrue($authorized);
    }

    /**
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function testDriverIsNotAuthorizedByFakeToken(): void
    {
        $this
            ->simulateLogin();

        $authorized = $this
            ->authModel
            ->isAuthorized(self::FAKE_TOKEN);

        $this
            ->assertFalse($authorized);
    }

    /**
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function testActiveDriverCanLogout(): void
    {
        $this
            ->simulateLogin();

        $this
            ->authModel
            ->logout(self::FUTURE_TOKEN);

        $driver = (DstDriverQuery::create())
            ->findOneByIdDriver(self::DRIVER_ID);

        $this
            ->assertNull(
                $driver->getCurrentSequence()
            );
    }

    /**
     * @return void
     */
    public function testFakeDriverLogoutThrowsException(): void
    {
        $this
            ->expectException(DriverNotExistsException::class);

        $this
            ->authModel
            ->logout(self::FAKE_TOKEN);
    }

    /**
     * @return void
     */
    public function testGetLoggedInDriverByToken(): void
    {
        $driver = $this
            ->authModel
            ->getDriverByToken(self::FUTURE_TOKEN);

        $this
            ->assertEquals(
                self::DRIVER_ID,
                $driver->getIdDriver()
            );
    }

    /**
     * @return void
     */
    public function testGetDriverByFakeTokenThrowsException(): void
    {
        $this
            ->expectException(DriverNotExistsException::class);

        $this
            ->authModel
            ->getDriverByToken(self::FAKE_TOKEN);
    }

    /**
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function simulateLogin(): void
    {
        $driver = (DstDriverQuery::create())
            ->findOneByIdDriver(self::DRIVER_ID);
        $driver
            ->setCurrentSequence(1)
            ->save();
    }
}
