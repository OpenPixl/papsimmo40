#!/bin/bash

START_YEAR=2022
END_YEAR=2026

for ((year=$START_YEAR; year<=$END_YEAR; year++))
do
  echo "Traitement de l'année $year..."

  git log --since="$year-01-01" --until="$year-01-31 23:59:59" \
    --pretty=format:"COMMIT|%h|%an|%ad|%s" --date=iso --numstat \
    > log_${year}_M1.txt

  git log --since="$year-02-01" --until="$year-03-01 00:00:00" \
    --pretty=format:"COMMIT|%h|%an|%ad|%s" --date=iso --numstat \
    > log_${year}_M2.txt

  git log --since="$year-03-01" --until="$year-03-31 23:59:59" \
    --pretty=format:"COMMIT|%h|%an|%ad|%ss" --date=iso --numstat \
    > log_${year}_M3.txt

  git log --since="$year-04-01" --until="$year-04-30 23:59:59" \
    --pretty=format:"COMMIT|%h|%an|%ad|%s" --date=iso --numstat \
    > log_${year}_M4.txt
    
  git log --since="$year-05-01" --until="$year-05-31 23:59:59" \
    --pretty=format:"COMMIT|%h|%an|%ad|%s" --date=iso --numstat \
    > log_${year}_M5.txt
  git log --since="$year-06-01" --until="$year-06-3 23:59:59" \
    --pretty=format:"COMMIT|%h|%an|%ad|%s" --date=iso --numstat \
    > log_${year}_M6.txt
    
  git log --since="$year-07-01" --until="$year-07-31 23:59:59" \
    --pretty=format:"COMMIT|%h|%an|%ad|%s" --date=iso --numstat \
    > log_${year}_M7.txt
    
  git log --since="$year-08-01" --until="$year-08-31 23:59:59" \
    --pretty=format:"COMMIT|%h|%an|%ad|%s" --date=iso --numstat \
    > log_${year}_M8.txt
    
  git log --since="$year-09-01" --until="$year-09-30 23:59:59" \
    --pretty=format:"COMMIT|%h|%an|%ad|%s" --date=iso --numstat \
    > log_${year}_M9.txt
  git log --since="$year-10-01" --until="$year-10-31 23:59:59" \
    --pretty=format:"COMMIT|%h|%an|%ad|%s" --date=iso --numstat \
    > log_${year}_M10.txt
  git log --since="$year-11-01" --until="$year-11-30 23:59:59" \
    --pretty=format:"COMMIT|%h|%an|%ad|%s" --date=iso --numstat \
    > log_${year}_M11.txt
  git log --since="$year-12-01" --until="$year-12-31 23:59:59" \
    --pretty=format:"COMMIT|%h|%an|%ad|%s" --date=iso --numstat \
    > log_${year}_M12.txt

done

echo "Découpage terminé 👍"
