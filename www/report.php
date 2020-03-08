<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Report</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            background: whitesmoke;
        }

        h1 {
            overflow: auto;
        }

        main {
            margin: auto;
            max-width: 1280px;
            display: block;
            background: white;
            padding: 2rem;
        }

        iframe {
            border: 0;
            width: 100%;
        }

        iframe:first-of-type {
            height: 50vh;
        }
    </style>
</head>
<body>
    <main>
        <h1>Lighthouse</h1>
        <iframe src="/dist/report/lighthouse.html"></iframe>
        <h1>Eslint</h1>
        <iframe src="/dist/report/eslint.txt"></iframe>
        <h1>Stylelint</h1>
        <iframe src="/dist/report/stylelint.txt"></iframe>
    </main>
</body>
</html>