const fs = require('fs');

// Read the file
const content = fs.readFileSync('frontend/src/components/views/forms/both-service-form.vue', 'utf8');
const lines = content.split('\n');

console.log('=== DIVISIONAL DIRECTOR REVIEW SECTION (around line 1818) ===');
for (let i = 1780; i <= 1850; i++) {
  if (lines[i]) {
    console.log(`${i + 1}: ${lines[i]}`);
  }
}

console.log('\n=== ICT DIRECTOR REVIEW SECTION (around line 1906) ===');
for (let i = 1870; i <= 1940; i++) {
  if (lines[i]) {
    console.log(`${i + 1}: ${lines[i]}`);
  }
}