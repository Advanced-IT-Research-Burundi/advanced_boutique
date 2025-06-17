NOTIFICATION D'ERREUR - {{ $priority }}
{{ str_repeat('=', 50) }}

{{ $errorIcon }} ERREUR {{ $errorData['error_code'] }} SUR {{ strtoupper(config('app.name')) }}

Type d'erreur: {{ $errorData['error_type'] }}
Message: {{ $errorData['error_message'] }}
Fichier: {{ $errorData['error_file'] }}:{{ $errorData['error_line'] }}

URL CONCERNÉE:
{{ $errorData['url'] }}

INFORMATIONS GÉNÉRALES:
- Horodatage: {{ $errorData['timestamp'] }}
- Environnement: {{ strtoupper($errorData['environment']) }}
- Méthode HTTP: {{ $errorData['method'] }}
- Adresse IP: {{ $errorData['ip'] }}

UTILISATEUR:
- ID: {{ $errorData['user_id'] ?? 'Non connecté' }}
- Email: {{ $errorData['user_email'] }}

NAVIGATEUR:
{{ $errorData['user_agent'] }}

@if(!empty($errorData['request_data']['get']))
DONNÉES GET:
{{ json_encode($errorData['request_data']['get'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
@endif

@if(!empty($errorData['request_data']['post']))
DONNÉES POST:
{{ json_encode($errorData['request_data']['post'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
@endif

STACK TRACE:
{{ str_repeat('-', 50) }}
{{ $errorData['stack_trace'] }}
{{ str_repeat('-', 50) }}

Cette notification a été générée automatiquement.
Pour accéder à la page: {{ $errorData['url'] }}
Accueil du site: {{ config('app.url') }}
