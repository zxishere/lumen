#!/bin/bash
git fetch --all && git reset --hard origin/master
echo "生产环境更新完毕"