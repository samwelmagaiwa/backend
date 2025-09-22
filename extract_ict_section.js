const fs = require('fs');

// Read the file
const content = fs.readFileSync('frontend/src/components/views/forms/both-service-form.vue', 'utf8');
const lines = content.split('\n');

console.log('=== ICT DIRECTOR REVIEW SECTION (around line 1906) ===');
for (let i = 1860; i <= 1950; i++) {
  if (lines[i]) {
    console.log(`${i + 1}: ${lines[i]}`);
  }
}