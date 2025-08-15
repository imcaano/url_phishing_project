document.getElementById('scanForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const url = e.target.elements.url.value;
    const response = await fetch('/api.php?route=scan', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ url })
    });
    
    const result = await response.json();
    
    // Display result to user
    const resultDiv = document.createElement('div');
    resultDiv.classList.add('scan-result');
    resultDiv.innerHTML = `
        <h3>Scan Results</h3>
        <p>Phishing Detection: ${result.is_phishing ? 'Suspicious' : 'Safe'}</p>
        <p>Confidence Score: ${result.confidence_score}%</p>
    `;
    
    e.target.after(resultDiv);
}); 