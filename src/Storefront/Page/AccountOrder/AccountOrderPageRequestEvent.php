<?php declare(strict_types=1);

namespace Shopware\Storefront\Page\AccountOrder;

use Shopware\Core\Checkout\CheckoutContext;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\NestedEvent;
use Shopware\Core\Framework\Event\NestedEventCollection;
use Shopware\Storefront\Pagelet\AccountOrder\AccountOrderPageletRequestEvent;
use Shopware\Storefront\Pagelet\ContentHeader\ContentHeaderPageletRequestEvent;
use Symfony\Component\HttpFoundation\Request;

class AccountOrderPageRequestEvent extends NestedEvent
{
    public const NAME = 'account-order.page.request';

    /**
     * @var CheckoutContext
     */
    protected $context;

    /**
     * @var Request
     */
    protected $httpRequest;

    /**
     * @var AccountOrderPageRequest
     */
    protected $pageRequest;

    public function __construct(Request $httpRequest, CheckoutContext $context, AccountOrderPageRequest $pageRequest)
    {
        $this->context = $context;
        $this->httpRequest = $httpRequest;
        $this->pageRequest = $pageRequest;
    }

    public function getEvents(): ?NestedEventCollection
    {
        return new NestedEventCollection([
            new ContentHeaderPageletRequestEvent($this->httpRequest, $this->context, $this->pageRequest->getHeaderRequest()),
            new AccountOrderPageletRequestEvent($this->httpRequest, $this->context, $this->pageRequest->getAccountOrderRequest()),
        ]);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getContext(): Context
    {
        return $this->context->getContext();
    }

    public function getCheckoutContext(): CheckoutContext
    {
        return $this->context;
    }

    public function getHttpRequest(): Request
    {
        return $this->httpRequest;
    }

    public function getAccountOrderPageRequest(): AccountOrderPageRequest
    {
        return $this->pageRequest;
    }
}