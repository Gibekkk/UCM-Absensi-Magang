<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance Scanner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
    <style>
        body {
            /* Menggunakan gambar dari folder public/img/ */
            background-image: url('<?= base_url("img/loginBackground.jpg"); ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', system-ui, sans-serif;
            margin: 0;
            /* Overlay gelap agar gambar tidak terlalu terang/mengganggu teks */
            background-color: rgba(0, 0, 0, 0.45);
            background-blend-mode: overlay;
        }

        .glass-card {
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            background: rgba(255, 255, 255, 0.15) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
        }

        .content-container{
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            width: 80%;
            height: 80%;
            position: absolute;
        }

        .left-container{
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .top-container{
            flex: 2;
        }

        .top-container p{
            font-size: 5rem;
            color: white;
            width: fit-content;
            height: fit-content;
        }

        .bottom-container{
            display: flex;
            gap: 2rem;
            flex: 1;
            padding: 0;
        }

        .right-container{
        }

        .date-container{
            flex-direction: column;
        }

        .time-container{
            flex-direction: column;
        }

        .container{
            border-radius: 12px;
        }

        .flex-container{
            justify-content: center;
            display: flex;
            align-items: center;
        }

        .date-container .day{
            font-size: 1.5rem;
            font-weight: 600;
        }

        .time-container .time{
            font-size: 2.5rem;
            font-weight: 700;
        }

        .date-container .date,
        .time-container .greeting{
            font-size: 2rem;
            color: #555;
        }

    </style>
</head>

<body>
    <div class="content-container container">
        <div class="left-container container">
            <div class="top-container container flex-container glass-card">
                <p class="title">Students</p>
                <p class="count">67</p>
            </div>
            <div class="bottom-container container">
                <div class="date-container container flex-container glass-card">
                    <p class="day">Monday</p>
                    <p class="date">October 9, 2023</p>
                </div>
                <div class="time-container container flex-container glass-card">
                    <p class="time">10:30:00</p>
                    <p class="greeting">Good morning!</p>
                </div>
            </div>
        </div>
        <div class="right-container container flex-container glass-card"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('js/app.js') ?>"></script>
    <script>

    </script>
</body>

</html>