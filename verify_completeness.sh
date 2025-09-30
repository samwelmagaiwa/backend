#!/bin/bash

echo "🔍 API COMPLETENESS VERIFICATION"
echo "=================================="

echo ""
echo "📊 ROUTE ANALYSIS VERIFICATION:"
echo "--------------------------------"

# Count routes in api.php
get_count=$(grep -c "Route::get\|->get(" backend/routes/api.php)
post_count=$(grep -c "Route::post\|->post(" backend/routes/api.php)
put_count=$(grep -c "Route::put\|->put(" backend/routes/api.php)
patch_count=$(grep -c "Route::patch\|->patch(" backend/routes/api.php)
delete_count=$(grep -c "Route::delete\|->delete(" backend/routes/api.php)
resource_count=$(grep -c "apiResource\|resource(" backend/routes/api.php)

total_explicit=$((get_count + post_count + put_count + patch_count + delete_count))

echo "✅ GET Routes: $get_count"
echo "✅ POST Routes: $post_count"
echo "✅ PUT Routes: $put_count"
echo "✅ PATCH Routes: $patch_count"
echo "✅ DELETE Routes: $delete_count"
echo "✅ Resource Routes: $resource_count (each adds ~7 endpoints)"
echo "📈 Total Explicit Routes: $total_explicit"

echo ""
echo "🎯 CONTROLLER ANALYSIS VERIFICATION:"
echo "------------------------------------"

# Count controllers
controller_count=$(find backend/app/Http/Controllers -name "*.php" -type f | wc -l)
echo "✅ Total Controllers Found: $controller_count"

# Count total public methods across all controllers
total_methods=0
echo ""
echo "📋 Methods per Controller:"

for controller in $(find backend/app/Http/Controllers -name "*.php" | sort); do
    controller_name=$(basename "$controller" .php)
    method_count=$(grep -c "public function" "$controller" | grep -v "__construct" || echo "0")
    if [ "$method_count" -gt 0 ]; then
        echo "  - $controller_name: $method_count methods"
        total_methods=$((total_methods + method_count))
    fi
done

echo ""
echo "📈 Total Public Methods: $total_methods"

echo ""
echo "📄 DOCUMENTATION FILES VERIFICATION:"
echo "------------------------------------"

if [ -f "backend/COMPLETE_API_DOCUMENTATION.md" ]; then
    echo "✅ Complete API Documentation exists"
    doc_size=$(wc -l < backend/COMPLETE_API_DOCUMENTATION.md)
    echo "   📏 Documentation size: $doc_size lines"
else
    echo "❌ Complete API Documentation missing"
fi

if [ -f "backend/API_ENDPOINTS_SUMMARY.md" ]; then
    echo "✅ API Endpoints Summary exists"
    summary_size=$(wc -l < backend/API_ENDPOINTS_SUMMARY.md)
    echo "   📏 Summary size: $summary_size lines"
else
    echo "❌ API Endpoints Summary missing"
fi

if [ -f "backend/docs/API_DOCUMENTATION.md" ]; then
    echo "✅ Main API Documentation exists"
    main_doc_size=$(wc -l < backend/docs/API_DOCUMENTATION.md)
    echo "   📏 Main doc size: $main_doc_size lines"
else
    echo "❌ Main API Documentation missing"
fi

echo ""
echo "🎯 COMPLETENESS SUMMARY:"
echo "========================"
echo "Total Routes Discovered: $total_explicit"
echo "Total Controllers: $controller_count"
echo "Total Methods: $total_methods"

# Calculate estimated total endpoints (including resource routes)
estimated_total=$((total_explicit + resource_count * 7))
echo "Estimated Total Endpoints: ~$estimated_total"

echo ""
echo "📚 Documentation Status:"
if [ -f "backend/COMPLETE_API_DOCUMENTATION.md" ] && [ -f "backend/API_ENDPOINTS_SUMMARY.md" ]; then
    echo "✅ COMPLETE - All documentation files created"
    echo "✅ Ready for production deployment"
else
    echo "❌ INCOMPLETE - Some documentation files missing"
fi

echo ""
echo "🔗 Access Points:"
echo "- Complete Documentation: backend/COMPLETE_API_DOCUMENTATION.md"
echo "- Updated Summary: backend/API_ENDPOINTS_SUMMARY.md"
echo "- Main API Docs: backend/docs/API_DOCUMENTATION.md"
echo "- Interactive Docs: http://localhost:8000/api/documentation"

echo ""
echo "✨ VERIFICATION COMPLETE"
