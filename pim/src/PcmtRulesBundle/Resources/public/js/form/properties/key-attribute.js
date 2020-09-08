/**
 * @author    Yohan Blain <yohan.blain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
'use strict';

define([
        'jquery',
        'underscore',
        'oro/translator',
        'pim/form/common/fields/field',
        'pim/fetcher-registry',
        'pim/user-context',
        'pim/i18n',
        'pim/template/attribute/tab/properties/group'
    ],
    function (
        $,
        _,
        __,
        BaseField,
        fetcherRegistry,
        UserContext,
        i18n,
        template
    ) {
        return BaseField.extend({
            events: {
                'change select': function (event) {
                    this.errors = [];
                    this.updateModel(this.getFieldValue(event.target));
                    this.getRoot().render();
                    this.fetchOptions();
                }
            },
            template: _.template(template),
            attributes: {},
            families_key: '',

            /**
             * {@inheritdoc}
             */
            configure: function () {
                return $.when(
                    BaseField.prototype.configure.apply(this, arguments)
                );

            },
            generateKey: function() {
                let formData = this.getFormData();
                return (formData.source_family || '') + '-' + (formData.destination_family || '');
            },

            fetchOptions: function() {
                let key = this.generateKey();
                if (key === this.families_key) {
                    return;
                }
                this.families_key = key;
                this.attributes = [];
                let formData = this.getFormData();
                if (formData.source_family && formData.destination_family) {
                    return fetcherRegistry.getFetcher('key-attribute-for-rule')
                        .fetchForFamilies(formData.source_family, formData.destination_family)
                        .then(function (attributes) {
                            this.attributes = attributes;
                            this.getRoot().render();
                        }.bind(this));
                }
            },

            /**
             * {@inheritdoc}
             */
            renderInput: function (templateContext) {
                this.fetchOptions();
                return this.template(_.extend(templateContext, {
                    value: this.getFormData()[this.fieldName],
                    groups: _.sortBy(this.attributes, 'sort_order'),
                    i18n: i18n,
                    locale: UserContext.get('catalogLocale'),
                    labels: {
                        defaultLabel: __('pcmt.entity.rules.property.attribute.choose')
                    }
                }));
            },

            /**
             * {@inheritdoc}
             */
            postRender: function () {
                this.$('select.select2').select2();
            },

            /**
             * {@inheritdoc}
             */
            getFieldValue: function (field) {
                return $(field).val();
            }
        });
    });
