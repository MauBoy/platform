import { Component, Mixin, State } from 'src/core/shopware';
import CriteriaFactory from 'src/core/factory/criteria.factory';
import template from './sw-product-list.twig';
import './sw-product-list.scss';

Component.register('sw-product-list', {
    template,

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('listing'),
        Mixin.getByName('placeholder')
    ],

    data() {
        return {
            products: [],
            showDeleteModal: false,
            sortBy: 'productNumber',
            sortDirection: 'DESC',
            isLoading: false,
            total: 0
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        productStore() {
            return State.getStore('product');
        },

        productColumns() {
            return this.getProductColumns();
        }
    },

    filters: {
        stockColorVariant(value) {
            if (value > 25) {
                return 'success';
            }
            if (value < 25 && value > 0) {
                return 'warning';
            }

            return 'error';
        }
    },

    methods: {
        onEdit(product) {
            if (product && product.id) {
                this.$router.push({
                    name: 'sw.product.detail',
                    params: {
                        id: product.id
                    }
                });
            }
        },

        onInlineEditSave(product) {
            const productName = product.name || this.placeholder(product, 'name');

            return product.save().then(() => {
                this.createNotificationSuccess({
                    title: this.$tc('sw-product.list.titleSaveSuccess'),
                    message: this.$tc('sw-product.list.messageSaveSuccess', 0, { name: productName })
                });
            }).catch(() => {
                product.discardChanges();

                this.createNotificationError({
                    title: this.$tc('global.notification.notificationSaveErrorTitle'),
                    message: this.$tc('global.notification.notificationSaveErrorMessage', 0, { entityName: productName })
                });
            });
        },

        onInlineEditCancel(product) {
            product.discardChanges();
        },

        getList() {
            this.isLoading = true;
            const params = this.getListingParams();

            params.criteria = CriteriaFactory.equals('product.parentId', null);

            params.associations = {
                manufacturer: {}
            };

            if (!params.sortBy) {
                params.sortBy = this.sortBy;
                params.sortDirection = this.sortDirection;
            }

            if (params.sortBy === 'productNumber') {
                params.sortings = [{
                    field: 'productNumber',
                    order: params.sortDirection,
                    naturalSorting: true
                }];
            }

            this.products = [];

            return this.productStore.getList(params).then((response) => {
                this.total = response.total;
                this.products = response.items;
                this.isLoading = false;

                return this.products;
            });
        },

        onChangeLanguage() {
            this.getList();
        },

        onDeleteProduct(id) {
            this.showDeleteModal = id;
        },

        onCloseDeleteModal() {
            this.showDeleteModal = false;
        },

        onConfirmDelete(id) {
            this.showDeleteModal = false;

            return this.productStore.getById(id).delete(true).then(() => {
                this.getList();
            });
        },

        getProductColumns() {
            return [{
                property: 'name',
                label: this.$tc('sw-product.list.columnName'),
                routerLink: 'sw.product.detail',
                inlineEdit: 'string',
                allowResize: true,
                primary: true
            }, {
                property: 'productNumber',
                label: this.$tc('sw-product.list.columnProductNumber'),
                align: 'right',
                allowResize: true
            }, {
                property: 'manufacturer.name',
                label: this.$tc('sw-product.list.columnManufacturer'),
                allowResize: true
            }, {
                property: 'active',
                label: this.$tc('sw-product.list.columnActive'),
                inlineEdit: 'boolean',
                allowResize: true,
                align: 'center'
            }, {
                property: 'price.gross',
                label: this.$tc('sw-product.list.columnPrice'),
                allowResize: true,
                align: 'right'
            }, {
                property: 'stock',
                label: this.$tc('sw-product.list.columnInStock'),
                inlineEdit: 'number',
                allowResize: true,
                align: 'right'
            }];
        }
    }
});
