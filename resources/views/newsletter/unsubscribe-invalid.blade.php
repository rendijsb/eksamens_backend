<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nederīga saite - NetNest</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #FBEAEB;
            color: #2D2D2D;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .container {
            max-width: 500px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: center;
        }

        .header {
            background: linear-gradient(45deg, #8B0000, #B22222);
            color: white;
            padding: 2rem;
        }

        .header h1 {
            font-size: 1.8rem;
            font-weight: 700;
        }

        .content {
            padding: 2rem;
        }

        .error-icon {
            font-size: 4rem;
            color: #ff4444;
            margin-bottom: 1rem;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #8B0000;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn:hover {
            background-color: #A50000;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>NetNest</h1>
    </div>

    <div class="content">
        <div class="error-icon">⚠️</div>
        <h2>Nederīga atrakstīšanās saite</h2>
        <p>Šī saite ir nederīga vai ir jau izmantota. Jūs jau esat atrakstījies no jaunumiem vai saite ir novecojusi.</p>

        <a href="http://localhost:4200" class="btn">
            Atgriezties veikalā
        </a>
    </div>
</div>
</body>
</html>
