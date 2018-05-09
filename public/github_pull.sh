#!/bin/bash
#export LANG=zh_CN.UTF-8
echo $PWD
git fetch --all && git reset --hard origin/master
echo "生产环境更新完毕"