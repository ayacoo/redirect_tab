<?php
declare(strict_types=1);

namespace Ayacoo\RedirectTab\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Site\Entity\NullSite;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\DebugUtility;
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
     * Prepares information for the pagination of the module
     * @param Demand|null $demand
     * @return array
     */
    protected function preparePagination(Demand $demand = null): array
    {
        if ($demand) {
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
        }
        return $pagination ?? [];
    }

    /**
     * @return array
     */
    protected function getRedirects(): array
    {
        /** @var Site $site */
        $site = $this->data['site'];
        if (!$site instanceof NullSite) {
            $languageUid = (int)$this->data['databaseRow']['sys_language_uid'];
            $language = $site->getLanguageById($languageUid);
            $host = $language->getBase()->getHost();

            $demand = new Demand(
                $site->getRootPageId(),
                '*',
                '',
                't3://page?uid=' . $this->data['effectivePid'],
                0
            );
            $this->redirectRepository = GeneralUtility::makeInstance(RedirectRepository::class, $demand);
            $redirectsForWildcardHostWithPageId = $this->redirectRepository->findRedirectsByDemand($demand);

            $demand = new Demand(
                $site->getRootPageId(),
                '*',
                '',
                $this->data['databaseRow']['slug'],
                0
            );
            $this->redirectRepository = GeneralUtility::makeInstance(RedirectRepository::class, $demand);
            $redirectsForWildcardHostWithSlug = $this->redirectRepository->findRedirectsByDemand($demand);

            $demand = new Demand(
                $site->getRootPageId(),
                $host,
                '',
                't3://page?uid=' . $this->data['effectivePid'],
                0
            );
            $this->redirectRepository = GeneralUtility::makeInstance(RedirectRepository::class, $demand);
            $redirectsForPageHostWithPageId = $this->redirectRepository->findRedirectsByDemand($demand);

            $demand = new Demand(
                $site->getRootPageId(),
                $host,
                '',
                $this->data['databaseRow']['slug'],
                0
            );
            $this->redirectRepository = GeneralUtility::makeInstance(RedirectRepository::class, $demand);
            $redirectsForPageHostWithSlug = $this->redirectRepository->findRedirectsByDemand($demand);

            $redirects = array_merge(
                $redirectsForWildcardHostWithPageId,
                $redirectsForPageHostWithPageId,
                $redirectsForWildcardHostWithSlug,
                $redirectsForPageHostWithSlug,
            );
        }
        return array($demand, $redirects ?? []);
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
