<?php

declare(strict_types=1);

namespace Ayacoo\Tiktok\Tests\Functional\Service;

use Ayacoo\RedirectTab\Service\RedirectDemandService;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class RedirectDemandServiceTest extends FunctionalTestCase
{
    protected array $coreExtensionsToLoad = ['redirects'];

    protected array $testExtensionsToLoad = ['redirect_tab'];

    private RedirectDemandService $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->get(RedirectDemandService::class);
    }

    /**
     * @test
     */
    public function getRedirectsReturnsPerDefaultAnEmptyArray(): void
    {
        $result = $this->subject->getRedirects(1);

        self::assertCount(0, $result);
    }
}
