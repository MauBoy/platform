import DomAccess from "../../helper/dom-access.helper";
import HttpClient from "../../service/http-client.service";
import OffCanvas from "../off-canvas/offcanvas.plugin";
import LoadingIndicator from "../loading-indicator/loading-indicator.plugin";
import DeviceDetection from "../../helper/device-detection.helper";
import CartWidget from "../actions/cart-widget.plugin";

const CART_MINI_OPEN_TRIGGER_DATA_ATTRIBUTE = 'data-cart-mini';
const CART_MINI_REMOVE_PRODUCT_TRIGGER_SELECTOR = '*[data-remove-product=true]';
const CART_MINI_FORM_SELECTOR = 'form[data-add-to-cart=true]';
const CART_MINI_POSITION = 'right';

export default class CartMini {

    /**
     * Constructor.
     */
    constructor() {
        this.client = new HttpClient(window.accessKey, window.contextToken);
        this._registerOpenTriggerEvents();
        this._registerFormEvents();
    }

    /**
     * Register events to handle opening the Cart Mini OffCanvas
     * by clicking a defined trigger selector
     * @private
     */
    _registerOpenTriggerEvents() {
        let event = (DeviceDetection.isTouchDevice()) ? 'touchstart' : 'click';

        document.addEventListener(event, (e) => {
            e.path.forEach(item => {
                if (DomAccess.hasAttribute(item, CART_MINI_OPEN_TRIGGER_DATA_ATTRIBUTE)) {
                    e.preventDefault();
                    this._onOpenCartMini(e);
                }
            });
        });
    }

    /**
     * On clicking the trigger item the OffCanvas shall open and the current
     * cart template may be fetched and shown inside the OffCanvas
     * @param {Event} e
     * @private
     */
    _onOpenCartMini(e) {
        e.preventDefault();

        OffCanvas.open(LoadingIndicator.getTemplate(), () => {
            this._fetchCartMini();
        }, CART_MINI_POSITION);

    }

    /**
     * Register events to handle form submission for adding any products to the cart
     * @private
     */
    _registerFormEvents() {
        let forms = document.querySelectorAll(CART_MINI_FORM_SELECTOR);

        forms.forEach(form => {
            form.addEventListener('submit', this._onFormSubmit.bind(this));
        });
    }

    /**
     * On submitting the form the OffCanvas shall open, the product has to be posted
     * against the storefront api and after that the current cart template needs to
     * be fetched and shown inside the OffCanvas
     * @param {Event} e
     * @private
     */
    _onFormSubmit(e) {
        e.preventDefault();

        const form = e.srcElement;
        const requestUrl = DomAccess.getAttribute(form, 'action');

        // Open the OffCanvas first
        OffCanvas.open(LoadingIndicator.getTemplate(), () => {
            // Fire POST request for adding the product to cart
            this.client.post(requestUrl.toLowerCase(), new FormData(form), () => {
                // Update the CartWidget in the header
                CartWidget.fetch();
                // Fetch the current cart template and replace the OffCanvas content
                this._fetchCartMini();
            });
        }, CART_MINI_POSITION);
    }

    /**
     * Register events to handle removing a product from the cart
     * @private
     */
    _registerRemoveProductTriggerEvents() {
        let forms = document.querySelectorAll(CART_MINI_REMOVE_PRODUCT_TRIGGER_SELECTOR);

        forms.forEach(form => {
            form.addEventListener('submit', this._onRemoveProductFromCart.bind(this));
        });
    }

    /**
     * On submitting the delete product form inside the OffCanvas a DELETE request
     * against the storefront api has to take place to remove the product. After that
     * the current cart template needs to be fetched and shown inside the OffCanvas
     * @param {Event} e
     * @private
     */
    _onRemoveProductFromCart(e) {
        e.preventDefault();

        const form = e.srcElement;
        const requestUrl = DomAccess.getAttribute(form, 'action');

        // Show loading indicator immediately after submitting
        form.innerHTML = LoadingIndicator.getTemplate();

        this.client.delete(requestUrl.toLowerCase(), () => {
            // Update the CartWidget in the header
            CartWidget.fetch();
            // Fetch the current cart template and replace the OffCanvas content
            this._fetchCartMini();
        });
    }

    /**
     * Fetch the current cart template and replace the OffCanvas content
     * @private
     */
    _fetchCartMini() {
        this.client.get(window.router["frontend.cart.detail"], (response) => {
            OffCanvas.setContent(response);
            this._registerRemoveProductTriggerEvents();
        });
    }

}