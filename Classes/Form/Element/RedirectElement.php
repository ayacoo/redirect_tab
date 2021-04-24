<?php
declare(strict_types=1);

namespace Ayacoo\RedirectTab\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Redirects\Repository\Demand;
use TYPO3\CMS\Redirects\Repository\RedirectRepository;

class RedirectElement extends AbstractFormElement
{
    /**
     * @var object|\Psr\Log\LoggerAwareInterface|\TYPO3\CMS\Core\SingletonInterface
     */
    private $view;

    /**
     * @var object|\Psr\Log\LoggerAwareInterface|\TYPO3\CMS\Core\SingletonInterface
     */
    private $redirectRepository;

    public function render(): array
    {
        $this->redirectRepository = GeneralUtility::makeInstance(RedirectRepository::class);

        $this->prepareView();
        list($demand, $redirects) = $this->getRedirects();

        $this->view->assignMultiple([
            'redirects' => $redirects,
            'demand' => $demand,
            'pagination' => $this->preparePagination($demand)
        ]);

        $result = $this->initializeResultArray();
        $result['html'] = $this->view->render();
        return $result;
    }

    /**
     * @return array
     */
    protected function getRedirects(): array
    {
        /** @var Site $site */
        $site = $this->data['site'];

        $languageUid = (int)$this->data['databaseRow']['sys_language_uid'];
        $language = $site->getLanguageById($languageUid);
        $host = $language->getBase()->getHost();

        $demand = new Demand(
            $site->getRootPageId(),
            'source_host',
            'asc',
            ['*', $host],
            '',
            't3://page?uid=' . $this->data['effectivePid'],
            [],
            0,
            null
        );
        $redirectsWithPageIdAsIdentifier = $this->redirectRepository->findRedirectsByDemand($demand);

        $oldDemand = new Demand(
            $site->getRootPageId(),
            'source_host',
            'asc',
            ['*', $host],
            '',
            $this->data['databaseRow']['slug'],
            [],
            0,
            null
        );
        $redirectsWithSlugAsIdentifier = $this->redirectRepository->findRedirectsByDemand($oldDemand);
        $redirects = array_merge($redirectsWithPageIdAsIdentifier, $redirectsWithSlugAsIdentifier);
        return array($demand, $redirects);
    }

    /**
     * Prepares information for the pagination of the module
     */
    protected function preparePagination(Demand $demand): array
    {
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
            'endRecord' => $endRecord
        ];
        if ($pagination['current'] < $pagination['numberOfPages']) {
            $pagination['nextPage'] = $pagination['current'] + 1;
        }
        if ($pagination['current'] > 1) {
            $pagination['previousPage'] = $pagination['current'] - 1;
        }
        return $pagination;
    }

    protected function prepareView(): void
    {
        $templateName = 'List';
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
        $this->view->setTemplate($templateName);
        $this->view->setTemplateRootPaths(['EXT:redirect_tab/Resources/Private/Templates/Backend']);
        $this->view->setPartialRootPaths(['EXT:redirect_tab/Resources/Private/Partials/Backend']);
        $this->view->setLayoutRootPaths(['EXT:redirect_tab/Resources/Private/Layouts']);
    }
}
