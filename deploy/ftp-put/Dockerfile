######################################################################
# Copyright (c) 2020, VillageReach
# Licensed under the Non-Profit Open Software License version 3.0.
# SPDX-License-Identifier: NPOSL-3.0
######################################################################

FROM pcmt/cron
RUN apk add --update --no-cache bash openssh gettext
RUN addgroup -S docker -g 1000 && \
    adduser -S docker -G docker -u 1000 -s /bin/bash -H && \
    mkdir -p /tmp/ftp-put && \
    chown docker:docker /tmp/ftp-put
ADD set-env.sh /
ADD ftp-cmd.sh /