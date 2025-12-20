<?php

declare(strict_types=1);

namespace Ayacoo\RedirectTab\Service;

use Ayacoo\RedirectTab\Event\ModifyRedirectsEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Site\Entity\NullSite;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Redirects\Repository\Demand;
use TYPO3\CMS\Redirects\Repository\RedirectRepository;

class RedirectDemandService
{
    public function __construct(
        protected RedirectRepository $redirectRepository,
        protected EventDispatcherInterface $eventDispatcher,
        protected ?Demand $demand = null,
        protected array $data = []
    ) {
    }

    /**
     * @param int $page
     * @return array
     */
    public function getRedirects(int $page = 1): array
    {
        $redirects = [];

        /** @var Site $site */
        $site = $this->data['site'] ?? null;
        if (!$site instanceof NullSite && $site !== null) {
            $languageUid = (int)$this->data['databaseRow']['sys_language_uid'];
            $language = $site->getLanguageById($languageUid);
            $host = $language->getBase()->getHost();

            $this->demand = new Demand(
                $page,
                'source_host',
                'asc',
                Demand::DEFAULT_REDIRECT_TYPE,
                ['*', $host],
                '',
                't3://page?uid=' . $this->data['effectivePid'],
            );
            $redirects = $this->redirectRepository->findRedirectsByDemand($this->demand);
        }

        $event = $this->eventDispatcher->dispatch(new ModifyRedirectsEvent($redirects));
        return $event->getRedirects();
    }

    public function preparePagination(?Demand $demand): array
    {
        $pagination = [];
        if ($demand !== null) {
            $count = $this->redirectRepository->countRedirectsByByDemand($demand);
            $numberOfPages = ceil($count / $demand->getLimit());
            $endRecord = $demand->getOffset() + $demand->getLimit();
            if ($endRecord > $count) {
                $endRecord = $count;
            }

            $pagination = [
                'current' => $demand->getPage(),
                'numberOfPages' => $numberOfPages,
                'hasLessPages' => $demand->getPage() > 1,
                'hasMorePages' => $demand->getPage() < $numberOfPages,
                'startRecord' => $demand->getOffset() + 1,
                'endRecord' => $endRecord,
            ];
            if ($pagination['current'] < $pagination['numberOfPages']) {
                $pagination['nextPage'] = $pagination['current'] + 1;
            }
            if ($pagination['current'] > 1) {
                $pagination['previousPage'] = $pagination['current'] - 1;
            }
        }
        return $pagination;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getDemand(): ?Demand
    {
        return $this->demand;
    }
}
