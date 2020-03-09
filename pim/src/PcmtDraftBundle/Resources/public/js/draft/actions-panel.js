/*
 * Copyright (c) 2020, VillageReach
 * Licensed under the Non-Profit Open Software License version 3.0.
 * SPDX-License-Identifier: NPOSL-3.0
 */

'use strict';

define(
    [
        'underscore',
        'oro/translator',
        'backbone',
        'pim/form',
        'pcmt/draft/template/actions-panel'
    ], function(
        _,
        __,
        Backbone,
        BaseForm,
        template
    ) {
        return BaseForm.extend({

            template: _.template(template),
            events: {
                'click .draft-bulk-approve': 'approveDrafts'
            },

            initialize: function() {
            },

            render: function () {
                this.$el.html(this.template({
                    bulkApproveText: __('pcmt.entity.draft.bulk_actions.approve')
                }));
            },

            approveDrafts() {
                this.getRoot().trigger('pcmt:drafts:approve');
            }
        });
    }
);
