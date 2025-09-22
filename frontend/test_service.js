// Simple test to verify the service is working
const combinedAccessService = require('./src/services/combinedAccessService.js')

async function testAPI() {
  try {
    console.log('Testing combinedAccessService.getRequestById(4)...')
    const response = await combinedAccessService.getRequestById(4)
    console.log('Response:', response)

    if (response.success) {
      console.log('Signature path:', response.data?.signature_path)
    } else {
      console.log('Error:', response.error)
    }
  } catch (error) {
    console.error('Test error:', error)
  }
}

testAPI()
