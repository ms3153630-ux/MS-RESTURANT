<?php
// download_images.php - downloads realistic food and category images
$images = [
    'hero-bg.jpg' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1200&auto=format&fit=crop&q=80',
    'about-img.jpg' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800&auto=format&fit=crop&q=80',
    'about-chef.jpg' => 'https://images.unsplash.com/photo-1577219491135-ce391730fb2c?w=600&auto=format&fit=crop&q=80',
    'cat-starters.jpg' => 'https://images.unsplash.com/photo-1541529086526-db283c563270?w=600&auto=format&fit=crop&q=80',
    'cat-bbq.jpg' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=600&auto=format&fit=crop&q=80',
    'cat-burgers.jpg' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600&auto=format&fit=crop&q=80',
    'cat-pizza.jpg' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=600&auto=format&fit=crop&q=80',
    'cat-chinese.jpg' => 'https://images.unsplash.com/photo-1525755662778-989d0524087e?w=600&auto=format&fit=crop&q=80',
    'cat-pakistani.jpg' => 'https://images.unsplash.com/photo-1589302168068-964664d93dc0?w=600&auto=format&fit=crop&q=80',
    'cat-fastfood.jpg' => 'https://images.unsplash.com/photo-1561758033-d89a9ad46330?w=600&auto=format&fit=crop&q=80',
    'cat-drinks.jpg' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=600&auto=format&fit=crop&q=80',
    'cat-desserts.jpg' => 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?w=600&auto=format&fit=crop&q=80',

    'dynamite-prawns.jpg' => 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=600&auto=format&fit=crop&q=80',
    'stuffed-strips.jpg' => 'https://images.unsplash.com/photo-1562967914-608f82629710?w=600&auto=format&fit=crop&q=80',
    'hot-sour-soup.jpg' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=600&auto=format&fit=crop&q=80',
    'malai-boti.jpg' => 'https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?w=600&auto=format&fit=crop&q=80',
    'bihari-kabab.jpg' => 'https://images.unsplash.com/photo-1603360946369-dc9bb6258143?w=600&auto=format&fit=crop&q=80',
    'mutton-chops.jpg' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=600&auto=format&fit=crop&q=80',
    'smash-burger.jpg' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600&auto=format&fit=crop&q=80',
    'zinger-burger.jpg' => 'https://images.unsplash.com/photo-1625813506062-0aeb1d7a094b?w=600&auto=format&fit=crop&q=80',
    'bbq-burger.jpg' => 'https://images.unsplash.com/photo-1586190848861-99aa4a171e90?w=600&auto=format&fit=crop&q=80',
    'pizza-tikka.jpg' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=600&auto=format&fit=crop&q=80',
    'pizza-pepperoni.jpg' => 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=600&auto=format&fit=crop&q=80',
    'pizza-ranch.jpg' => 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?w=600&auto=format&fit=crop&q=80',
    'kung-pao.jpg' => 'https://images.unsplash.com/photo-1525755662778-989d0524087e?w=600&auto=format&fit=crop&q=80',
    'fried-rice.jpg' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=600&auto=format&fit=crop&q=80',
    'beef-chilli.jpg' => 'https://images.unsplash.com/photo-1541832676-9b763b0239ab?w=600&auto=format&fit=crop&q=80',
    'mutton-biryani.jpg' => 'https://images.unsplash.com/photo-1589302168068-964664d93dc0?w=600&auto=format&fit=crop&q=80',
    'chicken-karahi.jpg' => 'https://images.unsplash.com/photo-1606491956689-2ea866880c84?w=600&auto=format&fit=crop&q=80',
    'roghani-naan.jpg' => 'https://images.unsplash.com/photo-1626074353765-517a681e40be?w=600&auto=format&fit=crop&q=80',
    'loaded-fries.jpg' => 'https://images.unsplash.com/photo-1585109649139-366815a0d713?w=600&auto=format&fit=crop&q=80',
    'buffalo-wings.jpg' => 'https://images.unsplash.com/photo-1567620832903-9fc6debc209f?w=600&auto=format&fit=crop&q=80',
    'mint-lemonade.jpg' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=600&auto=format&fit=crop&q=80',
    'nutella-shake.jpg' => 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=600&auto=format&fit=crop&q=80',
    'lava-cake.jpg' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=600&auto=format&fit=crop&q=80',
    'shahi-kheer.jpg' => 'https://images.unsplash.com/photo-1541781774459-bb2af2f05b55?w=600&auto=format&fit=crop&q=80'
];

$destDir1 = __DIR__ . '/assets/images/';
$destDir2 = __DIR__ . '/assets/uploads/';
if (!is_dir($destDir1)) mkdir($destDir1, 0777, true);
if (!is_dir($destDir2)) mkdir($destDir2, 0777, true);

echo "Downloading images...\n";
foreach ($images as $filename => $url) {
    $target1 = $destDir1 . $filename;
    $target2 = $destDir2 . $filename;
    if (!file_exists($target1) || filesize($target1) < 1000) {
        $data = @file_get_contents($url, false, stream_context_create([
            'http' => ['timeout' => 8, 'user_agent' => 'Mozilla/5.0']
        ]));
        if ($data) {
            file_put_contents($target1, $data);
            file_put_contents($target2, $data);
            echo "Downloaded: $filename\n";
        } else {
            echo "Failed or timeout: $filename, creating placeholder\n";
        }
    } else {
        @copy($target1, $target2);
    }
}
echo "Done.\n";
