#!/bin/bash

echo "🔧 Running ESLint auto-fix..."

# Navigate to frontend directory
cd frontend

# Run ESLint auto-fix
echo "Running npm run lint:fix..."
npm run lint:fix

# Check if there are any remaining issues
echo "Checking for remaining issues..."
npm run lint:check

echo "✅ ESLint fixes completed!"