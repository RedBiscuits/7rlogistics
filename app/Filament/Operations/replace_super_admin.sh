#!/bin/bash

# Loop through each file in the current directory and subdirectories
find . -type f -name "*.php" | while read -r file; do
    # Use sed to replace 'Super_Admin' with 'Operations' in each file
    sed -i 's/\bSuper_Admin\b/Operations/g' "$file"
    echo "Processed: $file"
done

echo "Replacement complete."

