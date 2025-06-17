<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification d'erreur</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, {{ $priorityColor }}, {{ $priorityColor }}dd);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .priority-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
        }
        .content {
            padding: 30px;
        }
        .error-summary {
            background: #f8f9fa;
            border-left: 4px solid {{ $priorityColor }};
            padding: 15px;
            margin: 20px 0;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .info-card {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
        }
        .info-card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #6c757d;
            text-transform: uppercase;
        }
        .info-card p {
            margin: 5px 0;
            font-size: 14px;
        }
        .url-link {
            background: #e9ecef;
            padding: 10px;
            border-radius: 4px;
            word-break: break-all;
            font-family: monospace;
            font-size: 12px;
        }
        .url-link a {
            color: #0066cc;
            text-decoration: none;
        }
        .url-link a:hover {
            text-decoration: underline;
        }
        .stack-trace {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 11px;
            max-height: 200px;
            overflow-y: auto;
            white-space: pre-wrap;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .action-button {
            display: inline-block;
            background: {{ $priorityColor }};
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 10px 5px;
        }
        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $errorIcon }} Notification d'erreur</h1>
            <div class="priority-badge">{{ $priority }}</div>
        </div>

        <div class="content">
            <div class="error-summary">
                <h2 style="margin: 0 0 10px 0; color: {{ $priorityColor }};">
                    Erreur {{ $errorData['error_code'] }} - {{ $errorData['error_type'] }}
                </h2>
                <p><strong>Message :</strong> {{ $errorData['error_message'] }}</p>
                <p><strong>Fichier :</strong> {{ $errorData['error_file'] }}:{{ $errorData['error_line'] }}</p>
            </div>

            <div class="url-link">
                <strong>URL concernée :</strong><br>
                <a href="{{ $errorData['url'] }}" target="_blank">{{ $errorData['url'] }}</a>
            </div>

            <div class="info-grid">
                <div class="info-card">
                    <h3>🕐 Informations temporelles</h3>
                    <p><strong>Horodatage :</strong> {{ $errorData['timestamp'] }}</p>
                    <p><strong>Environnement :</strong> {{ strtoupper($errorData['environment']) }}</p>
                </div>

                <div class="info-card">
                    <h3>🌐 Informations requête</h3>
                    <p><strong>Méthode :</strong> {{ $errorData['method'] }}</p>
                    <p><strong>IP :</strong> {{ $errorData['ip'] }}</p>
                </div>

                <div class="info-card">
                    <h3>👤 Utilisateur</h3>
                    <p><strong>ID :</strong> {{ $errorData['user_id'] ?? 'Non connecté' }}</p>
                    <p><strong>Email :</strong> {{ $errorData['user_email'] }}</p>
                </div>

                <div class="info-card">
                    <h3>🖥️ Navigateur</h3>
                    <p><strong>User Agent :</strong></p>
                    <small>{{ Str::limit($errorData['user_agent'], 50) }}</small>
                </div>
            </div>

            @if(!empty($errorData['request_data']['get']) || !empty($errorData['request_data']['post']))
            <div class="info-card">
                <h3>📝 Données de la requête</h3>
                @if(!empty($errorData['request_data']['get']))
                    <p><strong>GET :</strong></p>
                    <pre style="font-size: 11px; background: #f8f9fa; padding: 10px; border-radius: 4px;">{{ json_encode($errorData['request_data']['get'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @endif
                @if(!empty($errorData['request_data']['post']))
                    <p><strong>POST :</strong></p>
                    <pre style="font-size: 11px; background: #f8f9fa; padding: 10px; border-radius: 4px;">{{ json_encode($errorData['request_data']['post'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @endif
            </div>
            @endif

            <details style="margin: 20px 0;">
                <summary style="cursor: pointer; font-weight: bold; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                    🔍 Stack Trace (Cliquer pour afficher)
                </summary>
                <div class="stack-trace">{{ $errorData['stack_trace'] }}</div>
            </details>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $errorData['url'] }}" class="action-button" target="_blank">
                    🔗 Visiter la page
                </a>
                @if($errorData['environment'] !== 'production')
                <a href="{{ config('app.url') }}" class="action-button" target="_blank">
                    🏠 Aller à l'accueil
                </a>
                @endif
            </div>
        </div>

        <div class="footer">
            <p>Cette notification a été générée automatiquement par {{ config('app.name') }}</p>
            <p>Environnement : {{ strtoupper($errorData['environment']) }} | {{ $errorData['timestamp'] }}</p>
        </div>
    </div>
</body>
</html>
