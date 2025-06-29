<?php
// ================= CONFIGURATION ================= //
$VIDEO_URL = "https://cworld.fun/Stalker/video.mp4"; // Your 12-sec video
$TELEGRAM_URL = "https://t.me/IPTVM3Y";             // Telegram redirect
$M3U_URL = "https://haa.ovh/tv.m3u";                // Your M3U playlist

// ============= ULTIMATE IPTV DETECTION ============ //
function is_iptv_request() {
    $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
    
    // 1. Check ALL known IPTV player keywords
    $players = [
        // Android
        'ott navigator', 'tivimate', 'smarters', 'perfect player', 'implayer',
        'ssiptv', 'netiptv', 'xciptv', 'ottplayer', 'iptv extreme',
        
        // Set-top boxes
        'stbemu', 'mag', 'formuler', 'buzztv', 'dreamlink', 'dune',
        
        // Media players
        'vlc', 'kodi', 'mx player', 'mpv', 'nplayer',
        
        // iOS
        'gse smart iptv', 'iplaytv', 'flex iptv'
    ];

    foreach ($players as $player) {
        if (strpos($ua, $player) !== false) return true;
    }

    // 2. Check M3U-specific headers
    if (isset($_SERVER['HTTP_ACCEPT'])) {
        $accept = strtolower($_SERVER['HTTP_ACCEPT']);
        if (strpos($accept, 'mpegurl') !== false || strpos($accept, 'hls') !== false) {
            return true;
        }
    }

    // 3. Check for direct playlist requests
    if (isset($_SERVER['REQUEST_URI'])) {
        if (strpos($_SERVER['REQUEST_URI'], '.m3u') !== false) return true;
    }

    // 4. Special cases (Xtream Codes, etc)
    $headers = ['HTTP_X_IPTV', 'HTTP_OTT_DEVICE', 'HTTP_X_PLAYLIST'];
    foreach ($headers as $header) {
        if (isset($_SERVER[$header])) return true;
    }

    return false;
}

// ================== MAIN LOGIC ================== //
if (is_iptv_request()) {
    // FOR IPTV PLAYERS - Deliver M3U perfectly
    header('Content-Type: application/vnd.apple.mpegurl');
    header('Content-Disposition: inline; filename="iptv_channels.m3u"');
    
    // Use proper redirect for external M3U
    if (filter_var($M3U_URL, FILTER_VALIDATE_URL)) {
        header("Location: $M3U_URL", true, 302);
    } else {
        readfile($M3U_URL);
    }
    exit();
}

// FOR BROWSERS - Show video with redirect
?>
<!DOCTYPE html>
<html>
<head>
    <title>Loading...</title>
    <style>
        body { margin: 0; background: #000; }
        video { 
            width: 100vw; 
            height: 100vh; 
            object-fit: contain;
        }
    </style>
</head>
<body>
    <video id="vid" autoplay playsinline>
        <source src="<?= $VIDEO_URL ?>" type="video/mp4">
    </video>

    <script>
        const video = document.getElementById('vid');
        
        // Try unmuted autoplay
        video.play().catch(e => {
            // If blocked, show controls
            video.controls = true;
            video.muted = false;
        });

        // Redirect when ended
        video.addEventListener('ended', () => {
            window.location.href = "<?= $TELEGRAM_URL ?>";
        });

        // Fallback redirect after 15s
        setTimeout(() => {
            if (!video.ended) window.location.href = "<?= $TELEGRAM_URL ?>";
        }, 15000);
    </script>
</body>
</html>
