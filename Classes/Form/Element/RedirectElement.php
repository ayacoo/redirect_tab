<?php

declare(strict_types=1);

namespace Ayacoo\RedirectTab\Form\Element;

use Ayacoo\RedirectTab\Service\RedirectDemandService;
use Psr\Http\Message\UriInterface;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class RedirectElement extends AbstractFormElement
{
    private StandaloneView $view;

    public function render(): array
    {
        /** @var RedirectDemandService $redirectDemandService */
        $redirectDemandService = GeneralUtility::makeInstance(RedirectDemandService::class);
        $redirectDemandService->setData($this->data ?? []);

        $request = $GLOBALS['TYPO3_REQUEST'];
        $currentPage = ($request->getQueryParams()['page'] ?? $request->getParsedBody()['page'] ?? 1);
        if ($currentPage < 1) {
            $currentPage = 1;
        }

        $this->prepareView();

        $this->view->assignMultiple([
            'redirects' => $redirectDemandService->getRedirects((int) $currentPage),
            'demand' => $redirectDemandService->getDemand(),
            'pagination' => $redirectDemandService->preparePagination($redirectDemandService->getDemand()),
            'returnUrl' => $this->buildRedirectUrl((int) $currentPage),
            'recordUid' => (int)$this->data['effectivePid'],
        ]);

        $result = $this->initializeResultArray();
        $result['html'] = $this->view->render();
        return $result;
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

    protected function buildRedirectUrl(int $currentPage): UriInterface|Uri
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

        return $backendUriBuilder->buildUriFromRoute('record_edit', $uriParameters);
    }
}
