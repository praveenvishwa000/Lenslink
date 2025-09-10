<!-- Featured Image 1 -->
<div class="max-w-sm mx-auto">
    <div class="image-card relative group overflow-hidden rounded-lg shadow-lg">
        <img src="<?php echo htmlspecialchars($imageSrc); ?>" alt="<?php echo $imageTitle; ?>" class="w-full h-64 object-cover">
        <div class="image-overlay absolute inset-0 bg-black bg-opacity-50 opacity-0 transition-opacity duration-300 flex flex-col justify-end p-4">
            <h3 class="text-white text-lg font-semibold mb-1"><?php echo $imageTitle; ?></h3>
            <div class="flex justify-between items-center">
                <p class="text-white text-sm"><?php echo substr($imageDescription, 0, 50) . (strlen($imageDescription) > 50 ? '...' : ''); ?></p>
                <p class="text-white font-bold">Lkr <?php echo number_format($image['price'], 2); ?></p>
            </div>
            <a href="image_details.php?id=<?php echo $imageId; ?>" class="mt-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm text-center">View Details</a>
        </div>
    </div>
</div>