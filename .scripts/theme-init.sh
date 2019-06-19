#!/bin/bash

THEME_FOLDER=./wp-content/themes/_s

# Replaces strings from first array with same potision string in second array
unset REPLACE
declare -A REPLACE=( ["'_s'"]="'unito'" ["_s_"]="unito_" [" _s"]=" Unito" ["_s-"]="unito-" )

for K in "${!REPLACE[@]}"; do
  find "$THEME_FOLDER/*" -type f ! -path "*.png*" ! -path "$THEME_FOLDER/inc/cmb2/*" ! -path "*style.css" -print0 | xargs -0 sed -i "s/$K/${REPLACE[$K]}/g";
done

# Also need to replace Text Domain: _s in style.css

# Search for: '_s' and replace with: 'megatherium-is-awesome'
# Search for: _s_ and replace with: megatherium_is_awesome_
# Search for: Text Domain: _s and replace with: Text Domain: megatherium-is-awesome in style.css.
# Search for:  _s and replace with:  Megatherium_is_Awesome
# Search for: _s- and replace with: megatherium-is-awesome-
