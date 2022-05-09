#!/usr/bin/env bash

## name     : install.sh
## author   : The Pffy Authors https://pffy.dev
## git      : https://github.com/pffy/iconset
## license  : https://opensource.org/licenses/MIT

mkdir -p ~/pffypasta
cp iconset.php ~/pffypasta/iconset
echo "adding to ~/pffypasta ..."
sudo chmod u+x ~/pffypasta/iconset

echo "setting up iconset command ..."
sudo ln -sf ~/pffypasta/iconset /usr/local/bin/iconset

echo "done."
iconset
