<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Operaciones' ?></title>
    <!-- <link rel="stylesheet" href="/assets/styles.css"> -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- Development version -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <!-- Production version -->
    <script src="https://unpkg.com/lucide@latest"></script>

</head>

<body>

    <?php // include __DIR__ . '../../components/navbar.php'; 
    ?>
    <main>
        <?= $content ?? '' ?>
    </main>
    <?php // include __DIR__ . '../../components/footer.php'; 
    ?>
    <script>
        lucide.createIcons();
        // <i data-lucide="volume-2" class="my-class"></i>
        // <i data-lucide="x"></i>
        // <i data-lucide="menu"></i>
    </script>
</body>

</html>