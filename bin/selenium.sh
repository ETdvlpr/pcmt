#!/bin/bash
######################################################################
# Copyright (c) 2019, VillageReach
# Licensed under the Non-Profit Open Software License version 3.0.
# SPDX-License-Identifier: NPOSL-3.0
######################################################################

echo "STARTING SELENIUM TESTS..."

./ddev.sh exec -T -d fpm bin/console --env=test akeneo:batch:job-queue-consumer-daemon

./ddev.sh exec -T fpm /srv/pim/vendor/bin/behat --colors --suite=selenium-core
./ddev.sh exec -T fpm /srv/pim/vendor/bin/behat --colors --suite=selenium-drafts
./ddev.sh exec -T fpm /srv/pim/vendor/bin/behat --colors --suite=selenium-permissions
./ddev.sh exec -T fpm /srv/pim/vendor/bin/behat --colors --suite=selenium-rules

# to run specific test, comment above lines and uncomment below:
#./ddev.sh exec -T fpm /srv/pim/vendor/bin/behat src/PcmtDraftBundle/FunctionalTests/features/selenium/approve_draft.feature
#./ddev.sh exec -T fpm /srv/pim/vendor/bin/behat --colors src/PcmtCoreBundle/FunctionalTests/features/selenium/create_concatenated_attribute.feature
#./ddev.sh exec -T fpm /srv/pim/vendor/bin/behat --colors src/PcmtCoreBundle/FunctionalTests/features/selenium/create_reference_data_county_code.feature
#./ddev.sh exec -T fpm /srv/pim/vendor/bin/behat --colors src/PcmtCoreBundle/FunctionalTests/features/selenium/create_reference_data_language_code.feature
#./ddev.sh exec -T fpm /srv/pim/vendor/bin/behat --colors src/PcmtDraftBundle/FunctionalTests/features/selenium/approve_draft.feature
#./ddev.sh exec -T fpm /srv/pim/vendor/bin/behat --colors src/PcmtDraftBundle/FunctionalTests/features/selenium/bulk_approve.feature
#./ddev.sh exec -T fpm /srv/pim/vendor/bin/behat --colors src/PcmtCoreBundle/FunctionalTests/features/selenium/create_family.feature
#./ddev.sh exec -T fpm /srv/pim/vendor/bin/behat --colors src/PcmtCoreBundle/FunctionalTests/features/selenium/edit_family.feature
#./ddev.sh exec -T fpm /srv/pim/vendor/bin/behat --colors src/PcmtRulesBundle/FunctionalTests/features/selenium/11_create_select_options_rule.feature
#./ddev.sh exec -T fpm /srv/pim/vendor/bin/behat --colors src/PcmtRulesBundle/FunctionalTests/features/selenium/13_edit_select_options_rule.feature
#./ddev.sh exec -T fpm /srv/pim/vendor/bin/behat --colors src/PcmtRulesBundle/FunctionalTests/features/selenium/15_run_select_options_rule.feature
#./ddev.sh exec -T fpm /srv/pim/vendor/bin/behat --colors src/PcmtRulesBundle/FunctionalTests/features/selenium/18_delete_select_options_rule.feature
#./ddev.sh exec -T fpm /srv/pim/vendor/bin/behat --colors src/PcmtRulesBundle/FunctionalTests/features/selenium/21_create_family_to_family_rule.feature
#./ddev.sh exec -T fpm /srv/pim/vendor/bin/behat --colors src/PcmtRulesBundle/FunctionalTests/features/selenium/23_edit_family_to_family_rule.feature
#./ddev.sh exec -T fpm /srv/pim/vendor/bin/behat --colors src/PcmtRulesBundle/FunctionalTests/features/selenium/25_run_family_to_family_rule.feature
#./ddev.sh exec -T fpm /srv/pim/vendor/bin/behat --colors src/PcmtRulesBundle/FunctionalTests/features/selenium/28_delete_family_to_family_rule.feature
