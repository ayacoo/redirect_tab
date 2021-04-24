<?php
declare(strict_types=1);

namespace Ayacoo\RedirectTab\Event;

final class ModifyRedirectsEvent {

    private array $redirects;

    public function __construct(array $redirects)
    {
        $this->redirects = $redirects;
    }

    /**
     * @return array
     */
    public function getRedirects(): array
    {
        return $this->redirects;
    }

    /**
     * @param array $redirects
     */
    public function setRedirects(array $redirects): void
    {
        $this->redirects = $redirects;
    }
}
