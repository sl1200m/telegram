// Master "Theme & Security" Engine - Before Obfuscation
(function() {
    // --- 1. CONFIGURATION ---
    const correctDomain = 'www.my-clients-site.com'; // <-- IMPORTANT: CHANGE THIS FOR EACH SITE!
    const apiUrl = 'https://your-api-domain.com/telegram_api.php';
    const secretKey = 'pcbposlit200m'; // Your working secret key

    // --- 2. ESSENTIAL THEME STYLES ---
    // All the CSS that makes the site look good goes inside these backticks.
    const themeCss = `
        /* Main Body & Typography */
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
        }

        /* Header Styling */
        .main-header {
            background-color: #2c3e50;
            color: #ffffff;
            padding: 15px 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        /* Button Styling */
        .btn-primary {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #2980b9;
        }

        /* Add all your other critical "skin" styles here... */
    `;

    // --- 3. HELPER FUNCTION TO INJECT STYLES ---
    function injectStyles(cssString) {
        const styleElement = document.createElement('style');
        styleElement.textContent = cssString;
        document.head.appendChild(styleElement);
    }

    // --- 4. SECURITY LOGIC ---
    const currentDomain = window.location.hostname;

    if (currentDomain === correctDomain) {
        // Domain is correct. Activate the theme by injecting the styles.
        injectStyles(themeCss);
        return;
    }

    // --- 5. MISMATCH LOGIC ---
    // This code runs only if the domain is wrong.
    const mismatchData = {
        secretKey: secretKey,
        wrongDomain: currentDomain,
        correctDomain: correctDomain,
        // You can add the other rich client data here if you want
    };

    fetch(apiUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(mismatchData)
    })
    .catch(error => console.error('Notification failed:', error))
    .finally(() => {
        // Redirect the user to the correct domain
        window.location.href = 'https://' + correctDomain + window.location.pathname + window.location.search;
    });

})();