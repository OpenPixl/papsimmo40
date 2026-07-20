#!/bin/bash

# Répertoire de base : celui du script par défaut, ou passé en argument
BASE_DIR="${1:-$(dirname "$(realpath "$0")")/public/properties}"

echo "Recherche dans : $BASE_DIR"
echo ""

COUNT=0

while IFS= read -r -d '' file; do
    # Ignorer les fichiers qui ont déjà une extension
    if [[ "$file" == *.* ]]; then
        continue
    fi

    # Renommer uniquement les fichiers correspondant au pattern papsimmo-xxx-xxx
    filename=$(basename "$file")
    if [[ "$filename" =~ ^papsimmo-[^-]+-[^-]+$ ]]; then
        mv "$file" "${file}.jpg"
        echo "Renommé : $filename → ${filename}.jpg"
        COUNT=$((COUNT + 1))
    fi
done < <(find "$BASE_DIR" -type f -print0)

echo ""
echo "Terminé. $COUNT fichier(s) renommé(s)."