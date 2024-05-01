<?php

declare(strict_types=1);

namespace Ayacoo\Tiktok\Tests\Functional\Service;

use Ayacoo\RedirectTab\Service\RedirectDemandService;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Configuration\Event\SiteConfigurationBeforeWriteEvent;
use TYPO3\CMS\Core\Configuration\SiteConfiguration;
use TYPO3\CMS\Core\Site\Entity\NullSite;
use TYPO3\CMS\Core\Site\SiteFinder;
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
        $result = $this->subject->getRedirects();

        self::assertCount(0, $result);
    }

    /**
     * @test
     */
    public function getRedirectsWithNullSiteReturnsEmptyArray(): void
    {
        $site = new NullSite();
        $data = [];
        $data['site'] = $site;
        $this->subject->setData($data);
        $result = $this->subject->getRedirects();

        self::assertCount(0, $result);
    }

    /**
     * @test
     */
    public function getRedirectsReturnsArray(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/Fixtures/sys_redirect.csv');

        $identifier = 'website-local';
        $configuration = [
            'rootPageId' => 1,
            'base' => 'http://example.com/',
        ];


        $event = new SiteConfigurationBeforeWriteEvent($identifier, $configuration);
        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $eventDispatcherMock->expects(self::once())->method('dispatch')->with(self::anything())->willReturn($event);

        $siteConfiguration = new SiteConfiguration(
            $this->instancePath . '/typo3conf/sites/',
            $eventDispatcherMock
        );

        try {
            $siteConfiguration->write($identifier, $configuration);
        } catch (\Exception $exception) {
            self::markTestSkipped($exception->getMessage());
        }

        $siteFinder = new SiteFinder();
        $site = $siteFinder->getSiteByPageId(1);

        $data = [];
        $data['site'] = $site;
        $data['databaseRow']['sys_language_uid'] = 0;
        $data['effectivePid'] = 1;

        $this->subject->setData($data);
        $result = $this->subject->getRedirects(1);

        self::assertCount(1, $result);
    }

    /**
     * @test
     */
    public function getRedirectsWithMultiDomainsReturnsArray(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/Fixtures/sys_redirect_with_multi_domains.csv');

        $identifier = 'website-local';
        $configuration = [
            'rootPageId' => 1,
            'base' => 'http://example.com/',
        ];


        $event = new SiteConfigurationBeforeWriteEvent($identifier, $configuration);
        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $eventDispatcherMock->expects(self::once())->method('dispatch')->with(self::anything())->willReturn($event);

        $siteConfiguration = new SiteConfiguration(
            $this->instancePath . '/typo3conf/sites/',
            $eventDispatcherMock
        );

        try {
            $siteConfiguration->write($identifier, $configuration);
        } catch (\Exception $exception) {
            self::markTestSkipped($exception->getMessage());
        }

        $siteFinder = new SiteFinder();
        $site = $siteFinder->getSiteByPageId(1);

        $data = [];
        $data['site'] = $site;
        $data['databaseRow']['sys_language_uid'] = 0;
        $data['effectivePid'] = 1;

        $this->subject->setData($data);
        $result = $this->subject->getRedirects(1);

        self::assertCount(2, $result);
    }
}
