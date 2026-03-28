<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parallax Effect - BMIPusat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }
        .parallax-container {
            position: relative;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            perspective: 1px; /* Ini penting untuk efek parallax */
        }
        .parallax-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transform-origin: center;
        }
        #layer1 {
            z-index: 1;
        }
        #layer2 {
            z-index: 2;
            transform: translateZ(-1px) scale(2); /* Efek parallax */
        }
    </style>
</head>
<body>
    <div class="parallax-container">
        <!-- Layer 1: Halaman Awal -->
        <div id="layer1" class="parallax-layer">
            @include('display.halaman-awal')
        </div>

        <!-- Layer 2: Menu Awal -->
        <div id="layer2" class="parallax-layer">
            @include('display.menu-awal')
        </div>
    </div>

    <script>
        // JavaScript untuk efek parallax
        window.addEventListener('scroll', function() {
            const scrollY = window.scrollY;
            const layer2 = document.getElementById('layer2');
            layer2.style.transform = `translateZ(-1px) scale(2) translateY(${scrollY * 0.5}px)`;
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>