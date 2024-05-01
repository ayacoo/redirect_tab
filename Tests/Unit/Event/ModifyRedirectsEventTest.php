<?php

declare(strict_types=1);

namespace Ayacoo\RedirectTab\Tests\Unit\Helper;

use Ayacoo\RedirectTab\Event\ModifyRedirectsEvent;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ModifyRedirectsEventTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function getRedirectsReturnsRedirects(): void
    {
        $subject = new ModifyRedirectsEvent([]);
        $expected = [];

        $result = $subject->getRedirects();

        self::assertEquals($expected, $result);
    }
}
