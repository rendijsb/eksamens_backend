@extends('emails.layout')

@section('content')
    <h1 class="greeting">Laipni lūdzam NetNest jaunumu sarakstā! 🎉</h1>

    <p class="content-text">Sveiki, <strong>{{ $subscription->getEmail() }}</strong>!</p>

    <p class="content-text">Paldies, ka izvēlējāties pierakstīties mūsu jaunumu sarakstam. Tagad jūs būsiet pirmie, kas uzzinās par visiem mūsu jaunākajiem piedāvājumiem un produktiem!</p>

    <div class="highlight-box">
        <h3 style="color: #8B0000; margin: 0 0 15px 0;">Ko jūs saņemsiet:</h3>
        <ul style="list-style: none; padding: 0; margin: 20px 0;">
            <li style="padding: 8px 0; margin-left: 0; padding-left: 0;">
                <span style="color: #8B0000; font-weight: bold; margin-right: 10px;">✓</span>
                <strong>Jaunākos produktus</strong> un to īpašos piedāvājumus
            </li>
            <li style="padding: 8px 0; margin-left: 0; padding-left: 0;">
                <span style="color: #8B0000; font-weight: bold; margin-right: 10px;">✓</span>
                <strong>Ekskluzīvas atlaides</strong> tikai abonentiem
            </li>
            <li style="padding: 8px 0; margin-left: 0; padding-left: 0;">
                <span style="color: #8B0000; font-weight: bold; margin-right: 10px;">✓</span>
                <strong>Svarīgākos jaunumus</strong> no NetNest pasaules
            </li>
            <li style="padding: 8px 0; margin-left: 0; padding-left: 0;">
                <span style="color: #8B0000; font-weight: bold; margin-right: 10px;">✓</span>
                <strong>Īpašos pasākumus</strong> un akcijas
            </li>
        </ul>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-number">1000+</span>
            <span class="stat-label">Apmierināti klienti</span>
        </div>
        <div class="stat-card">
            <span class="stat-number">500+</span>
            <span class="stat-label">Produkti</span>
        </div>
        <div class="stat-card">
            <span class="stat-number">24/7</span>
            <span class="stat-label">Atbalsts</span>
        </div>
    </div>

    <p class="content-text">Gatavi sākt iepirkšanos? Apmeklējiet mūsu veikalu un atklājiet plašo produktu piedāvājumu.</p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ config('app.frontend_url', url('/')) }}"
           style="display: inline-block; background: linear-gradient(135deg, #8B0000 0%, #B22222 100%); color: #ffffff; padding: 16px 32px; text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 15px rgba(139, 0, 0, 0.3);">
            Iepirkties tagad
        </a>
    </div>

    <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #eee;">
        <h3 style="color: #8B0000; margin-bottom: 15px;">Nepieciešama palīdzība?</h3>
        <p class="content-text">Ja jums ir jautājumi, nevilcinieties ar mums sazināties:</p>
        <ul style="list-style: none; padding: 0;">
            <li style="margin: 8px 0;">📧 E-pasts: netnest777@gmail.com</li>
            <li style="margin: 8px 0;">📞 Tālrunis: +371 25759193</li>
        </ul>
    </div>

    <p class="content-text" style="margin-top: 40px; font-style: italic;">
        Ar nepacietību gaidām jūs mūsu veikalā!<br>
        <strong style="color: #8B0000;">NetNest komanda</strong> ❤️
    </p>
@endsection
