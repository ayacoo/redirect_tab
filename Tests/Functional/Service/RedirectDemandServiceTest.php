<?php

declare(strict_types=1);

namespace Ayacoo\Tiktok\Tests\Functional\Service;

use Ayacoo\RedirectTab\Service\RedirectDemandService;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Configuration\SiteWriter;
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

        $identifier = 'website-local';
        $configuration = [
            'rootPageId' => 1,
            'base' => 'http://example.com/',
        ];

        $siteWriter = $this->get(SiteWriter::class);
        try {
            $siteWriter->write($identifier, $configuration);
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

    #[Test]
    public function getRedirectsWithMultiDomainsReturnsArray(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/Fixtures/sys_redirect_with_multi_domains.csv');

        $identifier = 'website-local';
        $configuration = [
            'rootPageId' => 1,
            'base' => 'http://example.com/',
        ];

        $siteWriter = $this->get(SiteWriter::class);
        try {
            $siteWriter->write($identifier, $configuration);
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
