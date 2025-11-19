<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Close Confirmation Demo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .demo-section {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        button {
            background: #4CAF50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin: 5px;
            transition: background 0.3s;
        }

        button:hover {
            background: #45a049;
        }

        button.danger {
            background: #f44336;
        }

        button.danger:hover {
            background: #da190b;
        }

        .status {
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            text-align: center;
            font-weight: bold;
        }

        .enabled {
            background: rgba(76, 175, 80, 0.3);
            border: 1px solid #4CAF50;
        }

        .disabled {
            background: rgba(244, 67, 54, 0.3);
            border: 1px solid #f44336;
        }

        .code-example {
            background: rgba(0, 0, 0, 0.3);
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            margin: 15px 0;
            white-space: pre-wrap;
            overflow-x: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🛡️ Website Close Protection Demo</h1>

        <div class="demo-section">
            <h2>Live Demo Controls</h2>
            <div id="status" class="status enabled">✅ Close Protection: ENABLED</div>

            <button onclick="enableProtection()">Enable Protection</button>
            <button onclick="disableProtection()" class="danger">Disable Protection</button>

            <p><strong>Try it:</strong> With protection enabled, try to close this tab or browser window. You'll get a confirmation dialog!</p>
        </div>

        <div class="demo-section">
            <h2>How It Works</h2>
            <p>The <code>beforeunload</code> event fires when the user attempts to leave the page. Here's the code:</p>

            <div class="code-example">// Basic Implementation
                window.addEventListener('beforeunload', function(e) {
                // Show confirmation dialog
                e.preventDefault();
                e.returnValue = ''; // Required for Chrome
                return ''; // Required for other browsers
                });

                // Alternative using onbeforeunload
                window.onbeforeunload = function(e) {
                return "Are you sure you want to leave?";
                };</div>
        </div>

        <div class="demo-section">
            <h2>⚠️ Important Notes</h2>
            <ul>
                <li><strong>Browser Control:</strong> Modern browsers control the dialog message - you can't customize it</li>
                <li><strong>User Experience:</strong> Use sparingly - too many confirmations annoy users</li>
                <li><strong>Mobile Browsers:</strong> Some mobile browsers may not show the dialog</li>
                <li><strong>Only on User Action:</strong> Only works when user tries to close/navigate away</li>
            </ul>
        </div>

        <div class="demo-section">
            <h2>🎯 When to Use</h2>
            <ul>
                <li>Forms with unsaved data</li>
                <li>Online editors or applications</li>
                <li>Games or interactive content</li>
                <li>Long processes that shouldn't be interrupted</li>
            </ul>
        </div>

        <div class="demo-section">
            <h2>Advanced: Multiple Interaction Types</h2>
            <div class="code-example">// Detect various user interactions
                let userHasInteracted = false;

                // Multiple ways to detect user engagement
                const interactionEvents = [
                'click', // Mouse clicks
                'keydown', // Keyboard input
                'scroll', // Page scrolling
                'touchstart' // Mobile touch
                ];

                interactionEvents.forEach(eventType => {
                document.addEventListener(eventType, function() {
                if (!userHasInteracted) {
                userHasInteracted = true;
                console.log('User engaged - protection active');
                }
                }, { once: true }); // Only trigger once
                });

                window.addEventListener('beforeunload', function(e) {
                if (userHasInteracted) {
                e.preventDefault();
                e.returnValue = '';
                return '';
                }
                });</div>
        </div>
    </div>

    <script>
        let protectionEnabled = true;
        let hasUnsavedChanges = false;

        function beforeUnloadHandler(e) {
            if (protectionEnabled) {
                e.preventDefault();
                e.returnValue = '';
                return '';
            }
        }

        // Enable protection by default
        window.addEventListener('beforeunload', beforeUnloadHandler);

        function enableProtection() {
            protectionEnabled = true;
            document.getElementById('status').className = 'status enabled';
            document.getElementById('status').innerHTML = '✅ Close Protection: ENABLED';
        }

        function disableProtection() {
            protectionEnabled = false;
            document.getElementById('status').className = 'status disabled';
            document.getElementById('status').innerHTML = '❌ Close Protection: DISABLED';
        }

        function simulateUnsavedChanges() {
            hasUnsavedChanges = true;
            alert('Simulated unsaved changes! Now the close protection will be more relevant.');
        }

        function simulateSave() {
            hasUnsavedChanges = false;
            alert('Data saved! Close protection remains active for demo purposes.');
        }

        // Advanced example: only protect when there are unsaved changes
        function advancedBeforeUnloadHandler(e) {
            if (hasUnsavedChanges) {
                e.preventDefault();
                e.returnValue = '';
                return '';
            }
        }
    </script>
</body>

</html>