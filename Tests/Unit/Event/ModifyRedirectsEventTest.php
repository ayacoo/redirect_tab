<?php

declare(strict_types=1);

namespace Ayacoo\RedirectTab\Tests\Unit\Helper;

use Ayacoo\RedirectTab\Event\ModifyRedirectsEvent;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ModifyRedirectsEventTest extends UnitTestCase
{
    #[Test]
    public function getRedirectsReturnsRedirects(): void
    {
        $subject = new ModifyRedirectsEvent([]);
        $expected = [];

        $result = $subject->getRedirects();

        self::assertEquals($expected, $result);
    }
}
