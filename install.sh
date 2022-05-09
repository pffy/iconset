#!/usr/bin/env bash

## name     : install.sh
## author   : The Pffy Authors https://pffy.dev
## git      : https://github.com/pffy/iconset
## license  : https://opensource.org/licenses/MIT

mkdir -p ~/pffypasta
cp iconset.php ~/pffypasta/iconset
sudo chmod u+x ~/pffypasta/iconset
sudo ln -sf ~/pffypasta/iconset /usr/local/bin/iconset
