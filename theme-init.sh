#!/bin/bash

THEME_FOLDER=./wp-content/themes/_s

# Replaces strings from first array with same potision string in second array
unset REPLACE
declare -A REPLACE=( ["'_s'"]="'shakti'" [_s_]="shakti_" [ _s]=" Shakti" [_s-]="shakti-" [Text Domain: _s]="Text Domain: shakti" )

for K in "${!REPLACE[@]}"; do
  find ${THEME_FOLDER}/* -type f ! -path '*.png*' ! -path "${THEME_FOLDER}/inc/cmb2/*" -print0 | xargs -0 sed -i "s/${K}/${REPLACE[$K]}/g";
done

# This format works btw:
# find ./wp-content/themes/_s/* -type f -not -path "./wp-content/themes/_s/inc/cmb2/*" -print0 | xargs -0 sed -i "s/${STRING}/${NEW_STRING}/g";

# Search for: '_s' and replace with: 'megatherium-is-awesome'
# Search for: _s_ and replace with: megatherium_is_awesome_
# Search for: Text Domain: _s and replace with: Text Domain: megatherium-is-awesome in style.css.
# Search for:  _s and replace with:  Megatherium_is_Awesome
# Search for: _s- and replace with: megatherium-is-awesome-
