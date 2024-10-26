<?php

declare(strict_types=1);

namespace Ayacoo\Tiktok\Tests\Functional\Service;

use Ayacoo\RedirectTab\Service\RedirectDemandService;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Configuration\SiteConfiguration;
use TYPO3\CMS\Core\Site\Entity\NullSite;
use TYPO3\CMS\Core\Site\Entity\Site;
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

    #[Test]
    public function getRedirectsReturnsPerDefaultAnEmptyArray(): void
    {
        $result = $this->subject->getRedirects();

        self::assertCount(0, $result);
    }

    #[Test]
    public function getRedirectsWithNullSiteReturnsEmptyArray(): void
    {
        $site = new NullSite();
        $data = [];
        $data['site'] = $site;
        $this->subject->setData($data);
        $result = $this->subject->getRedirects();

        self::assertCount(0, $result);
    }

    #[Test]
    public function getRedirectsReturnsArray(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/Fixtures/sys_redirect.csv');

        $mainSite = new Site('1-main', 1, [
            'base' => 'https://example.com/',
            'languages' => [
                [
                    'languageId' => 0,
                    'base' => 'https://example.com/',
                    'locale' => 'en-US',
                ],
                [
                    'languageId' => 2,
                    'base' => 'https://example.com/de/',
                    'locale' => 'de-DE',
                ],
            ],
        ]);
        $siteFinder = $this->createSiteFinder($mainSite);
        $site = $siteFinder->getSiteByPageId(1);

        $data = [];
        $data['site'] = $site;
        $data['databaseRow']['sys_language_uid'] = 0;
        $data['effectivePid'] = 1;

        $this->subject->setData($data);
        $result = $this->subject->getRedirects(1);

        self::assertCount(1, $result);
    }

    #[Test]
    public function getRedirectsWithMultiDomainsReturnsArray(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/Fixtures/sys_redirect_with_multi_domains.csv');

        $mainSite = new Site('1-main', 1, [
            'base' => 'https://example.org/',
            'languages' => [
                [
                    'languageId' => 0,
                    'base' => 'https://example.com/',
                    'locale' => 'en-US',
                ],
                [
                    'languageId' => 2,
                    'base' => 'https://example.com/de/',
                    'locale' => 'de-DE',
                ],
            ],
        ]);
        $siteFinder = $this->createSiteFinder($mainSite);
        $site = $siteFinder->getSiteByPageId(1);

        $data = [];
        $data['site'] = $site;
        $data['databaseRow']['sys_language_uid'] = 0;
        $data['effectivePid'] = 1;

        $this->subject->setData($data);
        $result = $this->subject->getRedirects(1);

        self::assertCount(2, $result);
    }

    private function createSiteFinder(Site ...$sites): SiteFinder
    {
        $siteConfigurationMock = $this->createMock(SiteConfiguration::class);
        $sitesArray = array_combine(
            array_map(static function (Site $site) { return $site->getIdentifier(); }, $sites),
            $sites
        );
        $siteConfigurationMock->method('getAllExistingSites')->willReturn($sitesArray);
        return new SiteFinder($siteConfigurationMock, $this->createMock(FrontendInterface::class));
    }
}
