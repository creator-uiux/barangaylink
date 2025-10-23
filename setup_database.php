<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BarangayLink - Database Setup</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #000;
            color: #fff;
            padding: 2rem;
            line-height: 1.6;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            border-bottom: 2px solid #fff;
            padding-bottom: 0.5rem;
        }
        
        h2 {
            font-size: 1.5rem;
            margin: 2rem 0 1rem 0;
            color: #fff;
        }
        
        h3 {
            font-size: 1.2rem;
            margin: 1.5rem 0 0.75rem 0;
            color: #ccc;
        }
        
        .status-box {
            background: #111;
            border: 1px solid #333;
            padding: 1.5rem;
            margin: 1rem 0;
            border-radius: 4px;
        }
        
        .status-box.success {
            border-color: #4caf50;
            background: #1a2f1a;
        }
        
        .status-box.error {
            border-color: #f44336;
            background: #2f1a1a;
        }
        
        .status-box.warning {
            border-color: #ff9800;
            background: #2f2a1a;
        }
        
        .status-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #333;
        }
        
        .status-item:last-child {
            border-bottom: none;
        }
        
        .status-label {
            font-weight: 600;
        }
        
        .status-value {
            color: #aaa;
        }
        
        .success-indicator {
            color: #4caf50;
        }
        
        .error-indicator {
            color: #f44336;
        }
        
        .warning-indicator {
            color: #ff9800;
        }
        
        pre {
            background: #111;
            border: 1px solid #333;
            padding: 1rem;
            overflow-x: auto;
            border-radius: 4px;
            margin: 1rem 0;
        }
        
        code {
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #fff;
            color: #000;
            text-decoration: none;
            border-radius: 4px;
            margin: 0.5rem 0.5rem 0.5rem 0;
            border: 1px solid #fff;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .btn:hover {
            background: #000;
            color: #fff;
        }
        
        .btn-secondary {
            background: transparent;
            color: #fff;
        }
        
        .btn-secondary:hover {
            background: #fff;
            color: #000;
        }
        
        .step {
            counter-increment: step-counter;
            position: relative;
            padding-left: 3rem;
            margin: 1.5rem 0;
        }
        
        .step:before {
            content: counter(step-counter);
            position: absolute;
            left: 0;
            top: 0;
            width: 2rem;
            height: 2rem;
            background: #fff;
            color: #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .steps {
            counter-reset: step-counter;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        
        table th,
        table td {
            border: 1px solid #333;
            padding: 0.75rem;
            text-align: left;
        }
        
        table th {
            background: #111;
            font-weight: 600;
        }
        
        table tr:nth-child(even) {
            background: #0a0a0a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 BarangayLink Database Setup</h1>
        <p style="margin: 1rem 0;">Database connection and table initialization for MySQL (XAMPP & Render.com)</p>

        <?php
        // Include configuration
        require_once __DIR__ . '/config.php';
        require_once __DIR__ . '/config/database.php';

        // Test database connection
        $connectionSuccess = false;
        $dbInfo = [];
        $error = null;

        try {
            $db = Database::getInstance();
            $connectionSuccess = true;
            $dbInfo = $db->getInfo();
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        ?>

        <!-- Connection Status -->
        <h2>📊 Connection Status</h2>
        <div class="status-box <?php echo $connectionSuccess ? 'success' : 'error'; ?>">
            <div class="status-item">
                <span class="status-label">Database Status:</span>
                <span class="<?php echo $connectionSuccess ? 'success-indicator' : 'error-indicator'; ?>">
                    <?php echo $connectionSuccess ? '✓ Connected' : '✗ Failed'; ?>
                </span>
            </div>
            <?php if ($connectionSuccess): ?>
                <div class="status-item">
                    <span class="status-label">Host:</span>
                    <span class="status-value"><?php echo htmlspecialchars($dbInfo['host']); ?>:<?php echo $dbInfo['port']; ?></span>
                </div>
                <div class="status-item">
                    <span class="status-label">Database:</span>
                    <span class="status-value"><?php echo htmlspecialchars($dbInfo['database']); ?></span>
                </div>
                <div class="status-item">
                    <span class="status-label">Username:</span>
                    <span class="status-value"><?php echo htmlspecialchars($dbInfo['username']); ?></span>
                </div>
                <div class="status-item">
                    <span class="status-label">Charset:</span>
                    <span class="status-value"><?php echo htmlspecialchars($dbInfo['charset']); ?></span>
                </div>
            <?php else: ?>
                <div class="status-item">
                    <span class="status-label">Error:</span>
                    <span class="error-indicator"><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($connectionSuccess): ?>
            <!-- Table Status -->
            <h2>📋 Table Status</h2>
            <?php
            $tables = [
                'users' => 'User accounts and profiles',
                'document_requests' => 'Document request submissions',
                'concerns' => 'Community concerns and issues',
                'notifications' => 'System notifications',
                'announcements' => 'Community announcements and events',
                'file_uploads' => 'Uploaded files metadata'
            ];

            $conn = $db->getConnection();
            ?>
            <table>
                <thead>
                    <tr>
                        <th>Table Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Row Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tables as $table => $description): ?>
                        <?php
                        try {
                            $stmt = $conn->query("SELECT COUNT(*) FROM `{$table}`");
                            $count = $stmt->fetchColumn();
                            $exists = true;
                        } catch (PDOException $e) {
                            $exists = false;
                            $count = 0;
                        }
                        ?>
                        <tr>
                            <td><code><?php echo htmlspecialchars($table); ?></code></td>
                            <td><?php echo htmlspecialchars($description); ?></td>
                            <td class="<?php echo $exists ? 'success-indicator' : 'error-indicator'; ?>">
                                <?php echo $exists ? '✓ Exists' : '✗ Missing'; ?>
                            </td>
                            <td><?php echo $count; ?> rows</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Sample Data -->
            <h2>👤 Default Accounts</h2>
            <div class="status-box">
                <h3>Admin Account</h3>
                <div class="status-item">
                    <span class="status-label">Email:</span>
                    <span class="status-value">admin@email.com</span>
                </div>
                <div class="status-item">
                    <span class="status-label">Password:</span>
                    <span class="status-value">admin@password.com</span>
                </div>

                <h3 style="margin-top: 1.5rem;">User Account</h3>
                <div class="status-item">
                    <span class="status-label">Email:</span>
                    <span class="status-value">user@email.com</span>
                </div>
                <div class="status-item">
                    <span class="status-label">Password:</span>
                    <span class="status-value">user@password.com</span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Setup Instructions -->
        <h2>📖 Setup Instructions</h2>

        <h3>For Local Development (XAMPP)</h3>
        <div class="steps">
            <div class="step">
                <strong>Start XAMPP Services</strong>
                <p>Open XAMPP Control Panel and start both Apache and MySQL services</p>
            </div>
            
            <div class="step">
                <strong>Database Auto-Created</strong>
                <p>The database <code>barangaylinkDB</code> is automatically created when you visit this page</p>
            </div>
            
            <div class="step">
                <strong>Default Connection Settings</strong>
                <pre><code>Host: localhost
Port: 3306
Database: barangaylinkDB
Username: root
Password: (empty for XAMPP default)</code></pre>
            </div>
            
            <div class="step">
                <strong>Access Application</strong>
                <p>Navigate to <a href="index.php" style="color: #4caf50;">index.php</a> to start using BarangayLink</p>
            </div>
        </div>

        <h3>For Render.com Deployment</h3>
        <div class="steps">
            <div class="step">
                <strong>Create MySQL Database</strong>
                <p>In Render.com dashboard, create a new MySQL database instance</p>
            </div>
            
            <div class="step">
                <strong>Set Environment Variable</strong>
                <p>Add the <code>DATABASE_URL</code> environment variable from Render MySQL dashboard</p>
                <pre><code>DATABASE_URL=mysql://user:password@host:port/database</code></pre>
            </div>
            
            <div class="step">
                <strong>Deploy Application</strong>
                <p>Push your code to GitHub and deploy through Render.com</p>
            </div>
            
            <div class="step">
                <strong>Auto-Initialization</strong>
                <p>Tables will be created automatically on first access</p>
            </div>
        </div>

        <h3>Configuration Toggle</h3>
        <div class="status-box warning">
            <p><strong>Current Mode:</strong> <?php echo USE_DATABASE ? 'MySQL Database' : 'JSON Files'; ?></p>
            <p style="margin-top: 0.5rem;">To change between MySQL and JSON storage, edit <code>/config.php</code>:</p>
            <pre><code>// Set to true for MySQL, false for JSON files
define('USE_DATABASE', <?php echo USE_DATABASE ? 'true' : 'false'; ?>);</code></pre>
        </div>

        <!-- Action Buttons -->
        <div style="margin: 2rem 0;">
            <a href="index.php" class="btn">← Back to Application</a>
            <a href="setup_database.php" class="btn btn-secondary">🔄 Refresh Status</a>
            <?php if ($connectionSuccess): ?>
                <a href="test.php" class="btn btn-secondary">🧪 Run System Tests</a>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #333; color: #666; font-size: 0.875rem;">
            <p>BarangayLink v1.0.0 | Database Setup & Configuration Tool</p>
            <p>For support, check the documentation or contact your system administrator</p>
        </div>
    </div>
</body>
</html>
