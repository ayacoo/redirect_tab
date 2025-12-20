<?php

declare(strict_types=1);

namespace Ayacoo\RedirectTab\Form\Element;

use Ayacoo\RedirectTab\Service\RedirectDemandService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Redirects\Utility\RedirectConflict;

class RedirectElement extends AbstractFormElement
{
    private ViewInterface $view;

    public function render(): array
    {
        $redirectDemandService = GeneralUtility::makeInstance(RedirectDemandService::class);
        $redirectDemandService->setData($this->data ?? []);

        $request = $GLOBALS['TYPO3_REQUEST'];
        $currentPage = ($request->getQueryParams()['page'] ?? $request->getParsedBody()['page'] ?? 1);
        if ($currentPage < 1) {
            $currentPage = 1;
        }

        $this->prepareView($request);

        $this->view->assignMultiple([
            'redirects' => $redirectDemandService->getRedirects((int)$currentPage),
            'demand' => $redirectDemandService->getDemand(),
            'pagination' => $redirectDemandService->preparePagination($redirectDemandService->getDemand()),
            'returnUrl' => $this->buildRedirectUrl((int)$currentPage),
            'recordUid' => (int)$this->data['effectivePid'],
            'defaultIntegrityStatus' => RedirectConflict::NO_CONFLICT,
        ]);

        $result = $this->initializeResultArray();
        $result['html'] = $this->view->render('List');
        return $result;
    }

    protected function prepareView(ServerRequestInterface $request): void
    {
        $viewFactory = GeneralUtility::makeInstance(ViewFactoryInterface::class);
        $viewFactoryData = new ViewFactoryData(
            templateRootPaths: ['EXT:redirect_tab/Resources/Private/Templates/Backend'],
            partialRootPaths: ['EXT:redirect_tab/Resources/Private/Partials/Backend'],
            layoutRootPaths: ['EXT:redirect_tab/Resources/Private/Layouts'],
            request: $request,
        );
        $this->view = $viewFactory->create($viewFactoryData);
    }

    protected function buildRedirectUrl(int $currentPage): UriInterface|Uri|null
    {
        $backendUriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $uriParameters = [
            'edit' => [
                'pages' => [
                    $this->data['effectivePid'] => 'edit',
                ],
            ],
            'page' => $currentPage,
        ];

        try {
            return $backendUriBuilder->buildUriFromRoute('record_edit', $uriParameters);
        } catch (RouteNotFoundException) {
            return null;
        }
    }
}
