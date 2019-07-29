#!/bin/bash

TF_ENV=$1
TF_CMD="${@:2}"

if [ -z "$AWS_SECRET_ACCESS_KEY" ]; then
    echo AWS Secret Access Key missing
    exit 1
fi

if [ -z "$AWS_ACCESS_KEY_ID" ]; then
    echo AWS Access Key Id missing
    exit 1
fi

if [ ! -r "/var/run/docker.sock" ]; then
    echo Docker socket not mounted
    exit 1
fi

if [ ! -d "$1" ]; then
    echo "Environment isn't known directory: $1"
    exit 1
fi

SSH_KEY="/tmp/.ssh/id_rsa"
if [ ! -r "$SSH_KEY" -o ! -f "$SSH_KEY" ]; then
    echo "SSH Key $SSH_KEY not accessible"
    exit 1
fi
cp -R /tmp/.ssh /root/.ssh
chmod 700 /root/.ssh
chmod 400 /root/.ssh/*

echo Starting ssh-agent and adding default key
eval `ssh-agent -s`
ssh-add

echo "On environment $TF_ENV, running terraform $TF_CMD"
cd $TF_ENV
terraform init
terraform "${@:2}"

