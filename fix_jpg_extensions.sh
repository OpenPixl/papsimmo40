#!/bin/sh

BASE_DIR="${1:-$(dirname "$(realpath "$0")")/public/properties}"

echo "Recherche dans : $BASE_DIR"
echo ""

TMPCOUNT=$(mktemp)
echo 0 > "$TMPCOUNT"

find "$BASE_DIR" -type f | while IFS= read -r file; do
    case "$file" in
        *.*) continue ;;
    esac

    filename=$(basename "$file")
    case "$filename" in
        papsimmo-*-*)
            mv "$file" "${file}.jpg"
            echo "Renommé : $filename → ${filename}.jpg"
            echo $(( $(cat "$TMPCOUNT") + 1 )) > "$TMPCOUNT"
            ;;
    esac
done

echo ""
echo "Terminé. $(cat "$TMPCOUNT") fichier(s) renommé(s)."
rm -f "$TMPCOUNT"