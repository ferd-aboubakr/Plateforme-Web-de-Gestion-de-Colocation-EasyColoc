<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation EasyColoc</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f3f4f6; padding: 40px 20px; }
        .container { max-width: 560px; margin: 0 auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1d4ed8, #3b82f6); padding: 40px 32px; text-align: center; }
        .header h1 { color: #fff; font-size: 28px; font-weight: 800; }
        .header p { color: #bfdbfe; margin-top: 6px; font-size: 14px; }
        .body { padding: 36px 32px; }
        .coloc-card { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 20px 24px; margin: 20px 0; }
        .coloc-card .label { font-size: 11px; font-weight: 700; color: #3b82f6; text-transform: uppercase; letter-spacing: 0.08em; }
        .coloc-card .name { font-size: 22px; font-weight: 800; color: #1d4ed8; margin-top: 4px; }
        .coloc-card .owner { font-size: 13px; color: #6b7280; margin-top: 4px; }
        .buttons { display: flex; gap: 12px; margin: 24px 0; }
        .btn { flex: 1; display: inline-block; padding: 14px 24px; border-radius: 10px; font-size: 15px; font-weight: 700; text-align: center; text-decoration: none; }
        .btn-accept { background: #16a34a; color: #fff; }
        .btn-refuse { background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; }
        .expiry { font-size: 13px; color: #9ca3af; margin-bottom: 16px; }
        .url-fallback { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 16px; font-size: 11px; color: #6b7280; word-break: break-all; margin-top: 16px; }
        .footer { text-align: center; font-size: 12px; color: #9ca3af; padding: 20px 32px 28px; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🏠 EasyColoc</h1>
        <p>Gestion de colocation simplifiée</p>
    </div>
    <div class="body">
        <p style="font-size:18px; font-weight:600; margin-bottom:12px;">Bonjour ! 👋</p>
        <p style="font-size:15px; color:#4b5563; line-height:1.7;">
            Vous avez été invité(e) à rejoindre une colocation sur EasyColoc.
        </p>

        <div class="coloc-card">
            <div class="label">Colocation</div>
            <div class="name">{{ $invitation->colocation->name }}</div>
            <div class="owner">
                Invité par <strong>{{ $invitation->colocation->owner->name }}</strong>
                @if($invitation->colocation->address)
                    · {{ $invitation->colocation->address }}
                @endif
            </div>
        </div>

        <p class="expiry">
            ⏱️ Expire le {{ $invitation->expires_at?->format('d/m/Y à H:i') }}
        </p>

        <div class="buttons">
            <a href="{{ route('invitations.show', $invitation->token) }}" class="btn btn-accept">
                ✅ Accepter
            </a>
            <a href="{{ route('invitations.show', $invitation->token) }}" class="btn btn-refuse">
                ❌ Refuser
            </a>
        </div>

        <p style="font-size:13px; color:#6b7280;">Lien de secours :</p>
        <div class="url-fallback">{{ route('invitations.show', $invitation->token) }}</div>
    </div>
    <div class="footer">
        Si vous n'attendiez pas cet email, ignorez-le simplement.
    </div>
</div>
</body>
</html>