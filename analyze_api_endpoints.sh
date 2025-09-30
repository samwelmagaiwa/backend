#!/bin/bash

echo "=== API ENDPOINT ANALYSIS ==="
echo "Analyzing all controllers in the backend..."
echo ""

# Initialize counters
total_controllers=0
total_methods=0

# Find all controller files and analyze them
for controller in $(find backend/app/Http/Controllers -name "*.php" | sort); do
    echo "=== Processing: $controller ==="
    
    # Extract controller name
    controller_name=$(basename "$controller" .php)
    echo "Controller: $controller_name"
    
    # Extract all public methods
    methods=$(grep -n "public function" "$controller" | grep -v "__construct" | grep -v "__")
    method_count=$(echo "$methods" | wc -l)
    
    if [ -n "$methods" ]; then
        echo "Methods ($method_count):"
        while IFS= read -r line; do
            # Extract method name and parameters
            method_info=$(echo "$line" | sed 's/.*public function \([^(]*\)(\([^)]*\)).*/  - \1(\2)/')
            echo "$method_info"
            ((total_methods++))
        done <<< "$methods"
    else
        echo "Methods (0):"
    fi
    
    echo ""
    ((total_controllers++))
done

echo "=== SUMMARY ==="
echo "Total Controllers: $total_controllers"
echo "Total Public Methods: $total_methods"
echo ""

# Additional analysis of route file
echo "=== ROUTE ANALYSIS ==="
echo "Analyzing routes/api.php for endpoint patterns..."

# Count different types of routes
get_routes=$(grep -c "Route::get\|->get(" backend/routes/api.php)
post_routes=$(grep -c "Route::post\|->post(" backend/routes/api.php)
put_routes=$(grep -c "Route::put\|->put(" backend/routes/api.php)
patch_routes=$(grep -c "Route::patch\|->patch(" backend/routes/api.php)
delete_routes=$(grep -c "Route::delete\|->delete(" backend/routes/api.php)
resource_routes=$(grep -c "apiResource\|resource(" backend/routes/api.php)

echo "GET routes: $get_routes"
echo "POST routes: $post_routes"
echo "PUT routes: $put_routes"
echo "PATCH routes: $patch_routes"
echo "DELETE routes: $delete_routes"
echo "Resource routes: $resource_routes"

total_routes=$((get_routes + post_routes + put_routes + patch_routes + delete_routes))
echo "Total explicit routes: $total_routes"
echo "Note: Resource routes add multiple endpoints each"
