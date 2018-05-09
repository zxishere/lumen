#!/bin/bash
git fetch --all && git reset --hard origin/master
log=$(git log -1 --pretty=%B)
echo "last commit is : $log"
echo "生产环境更新完毕"