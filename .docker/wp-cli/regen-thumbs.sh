#!/bin/sh
set -e # exit on any errors (implies linear flow)

# >&2 echo "Regen all images"
# MSYS_NO_PATHCONV=1 docker-compose run --rm wp-cli wp media regenerate --yes

MIMETYPES=image/jpeg,image/gif,image/png,image/bmp,image/tiff,image/x-icon

>&2 echo "Get number of attachments"
TOTALIMAGES=$(wp post list --post_type=attachment --post_mime_type=${MIMETYPES} --posts_per_page=-1 --format=count)
>&2 echo "Attempting to regenerate ${TOTALIMAGES} attachments (in sets of 1000)"

DENOM=1000
PAGES=$(( (TOTALIMAGES + DENOM - 1)/DENOM )) # little math trick to round up result
CURRENTPAGE=1
echo number of pages to process is ${PAGES}

wp_regenerate_thumbs () {
  local ATTACHMENTS=$(wp post list --post_type=attachment --post_mime_type=${MIMETYPES} --posts_per_page=${DENOM} --paged=${CURRENTPAGE} --format=ids)
  echo
  echo "Batch ${CURRENTPAGE} (page ${CURRENTPAGE} of ${PAGES})"
  wp media regenerate ${ATTACHMENTS} --yes
  CURRENTPAGE=$((CURRENTPAGE + 1))
}

for i in $(seq 1 $PAGES); do
  wp_regenerate_thumbs
done
