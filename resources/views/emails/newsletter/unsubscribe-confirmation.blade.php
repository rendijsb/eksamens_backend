@extends('emails.layout')

@section('content')
    <h1 class="greeting">Jūs esat atrakstījies no jaunumiem 📤</h1>

    <p class="content-text">Sveiki, <strong>{{ $subscription->getEmail() }}</strong>!</p>

    <p class="content-text">Mēs apstiprinām, ka jūs esat veiksmīgi atrakstījies no NetNest jaunumu saraksta.</p>

    <div class="highlight-box">
        <h3 style="color: #8B0000; margin: 0 0 15px 0;">Ko tas nozīmē:</h3>
        <ul style="list-style: none; padding: 0; margin: 20px 0;">
            <li style="padding: 8px 0; margin-left: 0; padding-left: 0;">
                <span style="color: #8B0000; font-weight: bold; margin-right: 10px;">✓</span>
                Jūs vairs nesaņemsiet mūsu jaunumu e-pastus
            </li>
            <li style="padding: 8px 0; margin-left: 0; padding-left: 0;">
                <span style="color: #8B0000; font-weight: bold; margin-right: 10px;">✓</span>
                Jūsu e-pasta adrese ir saglabāta, bet neaktīva
            </li>
            <li style="padding: 8px 0; margin-left: 0; padding-left: 0;">
                <span style="color: #8B0000; font-weight: bold; margin-right: 10px;">✓</span>
                Jūs jebkurā laikā varat atkal pierakstīties
            </li>
        </ul>
    </div>

    <p class="content-text">Ja jūs esat atrakstījies kļūdas dēļ vai vēlaties atkal saņemt mūsu jaunumus, jūs vienmēr varat pierakstīties mūsu mājaslapā.</p>

    <div style="text-align: center;">
        <a href="{{ $resubscribeUrl }}" class="cta-button">
            Pierakstīties atkal
        </a>
    </div>

    <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #eee;">
        <h3 style="color: #8B0000; margin-bottom: 15px;">Kāpēc jūs vēl varētu mūs izvēlēties:</h3>
        <ul style="list-style: none; padding: 0;">
            <li style="margin: 8px 0;">🏷️ Ekskluzīvas atlaides tikai abonentiem</li>
            <li style="margin: 8px 0;">🚀 Pirmie uzzināt par jauniem produktiem</li>
            <li style="margin: 8px 0;">💰 Īpaši piedāvājumi un kuponi</li>
            <li style="margin: 8px 0;">📈 Personalizēti ieteikumi</li>
        </ul>
    </div>

    <p class="content-text" style="margin-top: 40px; font-style: italic;">
        Ja jums ir jautājumi vai ieteikumi, mēs vienmēr gaidām jūsu atsauksmes.<br>
        <strong style="color: #8B0000;">NetNest komanda</strong> 💙
    </p>
@endsection
