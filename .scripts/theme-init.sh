#!/bin/bash

THEME_FOLDER=./wp-content/themes/_s

# Replaces strings from first array with same potision string in second array
unset REPLACE
declare -A REPLACE=( ["'_s'"]="'xrm'" [_s_]="xrm_" [ _s]=" xRM" [_s-]="xrm-" [Text Domain: _s]="Text Domain: xrm" )

for K in "${!REPLACE[@]}"; do
  find ${THEME_FOLDER}/* -type f ! -path '*.png*' ! -path "${THEME_FOLDER}/inc/cmb2/*" -print0 | xargs -0 sed -i "s/${K}/${REPLACE[$K]}/g";
done

# Can al;so add a replcae for 2 spaces and new name to one space and new name

# Can also add theme rename

# can also add erase this file

# This format works btw:
# find ./wp-content/themes/_s/* -type f -not -path "./wp-content/themes/_s/inc/cmb2/*" -print0 | xargs -0 sed -i "s/${STRING}/${NEW_STRING}/g";

# Search for: '_s' and replace with: 'megatherium-is-awesome'
# Search for: _s_ and replace with: megatherium_is_awesome_
# Search for: Text Domain: _s and replace with: Text Domain: megatherium-is-awesome in style.css.
# Search for:  _s and replace with:  Megatherium_is_Awesome
# Search for: _s- and replace with: megatherium-is-awesome-
