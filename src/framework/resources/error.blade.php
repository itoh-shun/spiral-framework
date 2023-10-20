<!DOCTYPE html>
<html lang="ja">

<head>
    <title><?php echo $title; ?></title>
    <script defer src="https://unpkg.com/alpinejs@3.10.3/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div class="max-w-8xl mx-auto">
        <div
            class="
                flex
                items-center
                justify-center
                w-full
                h-screen
                bg-gray-50
            "
            >
            <div class="px-40 py-20">
                <div class="flex flex-col items-center">
                <h1 class="font-bold text-sushi-600 text-9xl"><?php echo $code; ?></h1>

                <h6 class="mb-2 text-2xl font-bold text-center text-gray-800 md:text-3xl">
                <?php echo $message; ?>
                </h6>
            <?php
/*
                <p class="mb-8 text-center text-gray-500 md:text-lg">
                    お探しのページは存在しません。
                </p>
            */
    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>