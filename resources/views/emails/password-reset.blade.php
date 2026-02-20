<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #083e5f 0%, #0EA5E9 50%, #84CC16 100%); padding: 30px; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 24px;">WiFiProfit</h1>
    </div>
    
    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; border: 1px solid #e0e0e0;">
        <h2 style="color: #083e5f; margin-top: 0;">Réinitialisation de votre mot de passe</h2>
        
        <p>Bonjour <strong>{{ $proprio->prenom }} {{ $proprio->nom }}</strong>,</p>
        
        <p>Vous avez demandé la réinitialisation de votre mot de passe sur WiFiProfit.</p>
        
        <p style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetLink }}" style="background-color: #083e5f; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                Réinitialiser mon mot de passe
            </a>
        </p>
        
        <p style="font-size: 14px; color: #666;">
            Ce lien expire dans <strong>24 heures</strong>.
        </p>
        
        <p style="font-size: 14px; color: #666;">
            Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email en toute sécurité. Votre mot de passe reste inchangé.
        </p>
        
        <hr style="border: none; border-top: 1px solid #e0e0e0; margin: 30px 0;">
        
        <p style="font-size: 12px; color: #999; text-align: center;">
            © 2026 WiFiProfit Platform. Tous droits réservés.
        </p>
    </div>
</body>
</html>
