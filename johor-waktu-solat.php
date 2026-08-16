<?php
/**
 * Plugin Name: Johor Waktu Solat (e-Solat XML)
 * Plugin URI: https://ariff.my
 * Description: Papar waktu solat Johor (JHR01–JHR04) dari e-Solat JAKIM melalui shortcode.
 * Version: 1.1.6
 * Author: Ariff Samani
 * License: GPL2+
 * Requires at least: 5.2
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) exit;

class Johor_Waktu_Solat_Plugin
{
    private $plugin_version = '1.1.6';

    private $default_zones = ['JHR01','JHR02','JHR03','JHR04'];

    private $zone_labels = [
        'JHR01' => 'Pulau Aur & Pulau Pemanggil',
        'JHR02' => 'Johor Bahru, Kota Tinggi, Mersing, Kulai',
        'JHR03' => 'Kluang, Pontian',
        'JHR04' => 'Batu Pahat, Muar, Segamat, Gemas Johor, Tangkak',
    ];

    private $hari_bm = [
        'Monday' => 'Isnin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Khamis',
        'Friday' => 'Jumaat',
        'Saturday' => 'Sabtu',
        'Sunday' => 'Ahad',
    ];

    public function __construct()
    {
        add_shortcode('johor_waktu_solat', [$this, 'shortcode']);
    }

    public function enqueue_styles()
    {
        $css = "
        .jws-grid{display:grid;grid-template-columns:repeat(var(--jws-cols,4),minmax(0,1fr));gap:16px;color-scheme:only light}
        .jws-grid,.jws-grid *{box-sizing:border-box}
        @media (max-width:1024px){.jws-grid{grid-template-columns:repeat(min(var(--jws-cols,4),2),minmax(0,1fr))}}
        @media (max-width:560px){.jws-grid{grid-template-columns:1fr}}
        .jws-grid .jws-card{display:flex;flex-direction:column;border:1px solid rgba(15,81,50,.10);border-radius:16px;background:#fff;color:#111827;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,.06);height:100%;forced-color-adjust:none}
        .jws-card-inner{display:flex;flex-direction:column;flex:1}
        .jws-header{display:flex;justify-content:space-between;align-items:stretch;gap:12px;padding:16px 18px;background:linear-gradient(135deg,#0f5132,#198754);color:#fff;min-height:92px}
        .jws-header-left{display:flex;flex-direction:column;justify-content:center;min-width:0;flex:1}
        .jws-title{font-weight:700;margin:0;font-size:15px;line-height:1.35;color:#fff}
        .jws-hari{margin-top:6px;font-size:13px;line-height:1.2;opacity:.92}
        .jws-date-badge{display:flex;align-items:center;justify-content:center;align-self:center;text-align:center;min-width:112px;min-height:58px;padding:8px 12px;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.16);border-radius:14px;font-size:12px;line-height:1.35;font-weight:600;backdrop-filter:blur(4px)}
        .jws-grid .jws-body{padding:14px 14px 12px;flex:1;background:#fff;color:#111827}
        .jws-status-stack{display:flex;flex-direction:column;gap:8px;margin:0 0 12px 0}
        .jws-live,.jws-next,.jws-updated{display:flex;align-items:center;gap:10px;width:100%;box-sizing:border-box;border-radius:12px;padding:10px 12px;font-size:12px;line-height:1.45;margin:0}
        .jws-live{background:#e8f6ee;color:#0f5132;border:1px solid rgba(25,135,84,.18)}
        .jws-next{position:relative;background:linear-gradient(180deg,#f4f8ff 0%,#e8f1ff 100%);color:#123a7a;border:1px solid rgba(58,110,193,.18)}
        .jws-updated{background:#f7f7f8;color:#374151;border:1px solid rgba(55,65,81,.10)}
        .jws-live-main,.jws-next-main,.jws-updated-main{display:flex;align-items:center;gap:8px;min-width:0;flex-wrap:wrap}
        .jws-live-label,.jws-next-label{font-weight:800}
        .jws-next-label{display:block;font-size:11px;letter-spacing:.04em;text-transform:uppercase;color:#315b9a}
        .jws-next-value{display:block;font-size:18px;line-height:1.2;font-weight:800;color:#0f2f66}
        .jws-next-time{display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;background:rgba(255,255,255,.72);border:1px solid rgba(58,110,193,.14);font-weight:700;opacity:1}
        .jws-next-divider{opacity:.35;font-weight:700}
        .jws-next-countdown{margin-left:auto;padding:8px 12px;border-radius:999px;background:linear-gradient(180deg,#ffffff 0%,#f7fbff 100%);border:1px solid rgba(58,110,193,.16);box-shadow:0 6px 16px rgba(41,98,255,.08);font-weight:800;white-space:nowrap;color:#123a7a}
        @media (max-width:560px){.jws-next{align-items:flex-start}.jws-next-countdown{margin-left:0;width:100%;justify-content:center}}
        .jws-next-outside{padding:0 14px 14px;margin-top:auto}
        .jws-next-outside .jws-next{padding:14px 14px 14px 16px;box-shadow:0 10px 24px rgba(41,98,255,.10), inset 0 0 0 1px rgba(255,255,255,.45)}
        .jws-next-outside .jws-next:before{content:'';position:absolute;left:0;top:14px;bottom:14px;width:4px;border-radius:999px;background:linear-gradient(180deg,#5b9dff 0%,#2d6cdf 100%)}
        .jws-live-dot{flex:0 0 auto;width:8px;height:8px;border-radius:50%;background:#198754;box-shadow:0 0 0 0 rgba(25,135,84,.45);animation:jwsPulse 1.8s infinite}
        @keyframes jwsPulse{0%{box-shadow:0 0 0 0 rgba(25,135,84,.45)}70%{box-shadow:0 0 0 8px rgba(25,135,84,0)}100%{box-shadow:0 0 0 0 rgba(25,135,84,0)}}
        .jws-grid .jws-table{width:100%;border-collapse:separate;border-spacing:0;font-size:13px;background:#fff;color:#111827;opacity:1}
        .jws-grid .jws-table tbody,.jws-grid .jws-table tr{background:#fff;color:#111827;opacity:1}
        .jws-grid .jws-table tr td{padding:9px 10px;border-top:1px dashed rgba(0,0,0,.10);background:#fff;color:#111827;vertical-align:middle;opacity:1;text-shadow:none;-webkit-text-fill-color:currentColor}
        .jws-table tr:first-child td{border-top:none}
        .jws-grid .jws-table td:first-child{font-weight:600;width:44%;color:#1f2937}
        .jws-grid .jws-table td:last-child{text-align:right;color:#111827}
        .jws-grid .jws-table .jws-row-current td{background:#eef8f2 !important}
        .jws-grid .jws-table .jws-row-current td:first-child{color:#0f5132;font-weight:700}
        .jws-grid .jws-table .jws-row-current td:last-child{font-weight:700;color:#0f5132}
        .jws-current-pill{display:inline-block;margin-left:8px;padding:3px 8px;border-radius:999px;background:#198754;color:#fff;font-size:10px;font-weight:700;vertical-align:middle}
        .jws-error{margin:14px;padding:10px;border:1px solid rgba(200,0,0,.25);background:rgba(200,0,0,.06);border-radius:10px}
        .jws-jhrsolat{margin-top:12px;font-size:11px;opacity:.65;text-align:right}
        .jws-jhrsolat a{color:inherit;text-decoration:underline;text-underline-offset:2px}
        @media (max-width:560px){
            .jws-grid .jws-header{padding:14px;min-height:0}
            .jws-grid .jws-title{font-size:16px}
            .jws-grid .jws-date-badge{min-width:82px;min-height:44px;padding:7px 9px}
            .jws-grid .jws-body{padding:12px}
            .jws-grid .jws-table tr td{padding:10px 8px}
            .jws-grid .jws-current-pill{display:block;width:max-content;margin:4px 0 0}
            .jws-grid .jws-next-outside{padding:0 12px 12px}
        }
        ";
        wp_register_style('jws-inline', false, [], $this->plugin_version);
        wp_enqueue_style('jws-inline');
        wp_add_inline_style('jws-inline', $css);
    }

    public function shortcode($atts)
    {
        $this->enqueue_styles();

        $atts = shortcode_atts([
            'zones' => implode(',', $this->default_zones),
            'cache_minutes' => 10,
            'columns' => 4,
            'show_live' => 'yes',
            'show_date' => 'yes',
            'show_last_updated' => 'yes',
            'show_countdown' => 'yes',
            'show_footer' => 'yes',
        ], $atts, 'johor_waktu_solat');

        $zones = $this->sanitize_zones($atts['zones']);
        $columns = min(4, max(1, intval($atts['columns'])));
        $cache_minutes = min(1440, max(1, intval($atts['cache_minutes'])));
        $show_live = $this->is_truthy($atts['show_live']);
        $show_date = $this->is_truthy($atts['show_date']);
        $show_last_updated = $this->is_truthy($atts['show_last_updated']);
        $show_countdown = $this->is_truthy($atts['show_countdown']);
        $show_footer = $this->is_truthy($atts['show_footer']);

        $data = $this->get_multi_zone_data($zones, $cache_minutes);

        $out = '<div class="jws-grid" style="--jws-cols:' . esc_attr((string) $columns) . '">';
        foreach ($zones as $zone) {
            if (!empty($data[$zone]['error'])) {
                $out .= '<div class="jws-card"><div class="jws-header"><div class="jws-header-left"><div class="jws-title">' . esc_html($this->label($zone)) . '</div></div></div>';
                $out .= '<div class="jws-error">' . esc_html($data[$zone]['error']) . '</div></div>';
                continue;
            }

            $title = $this->label($zone);
            $date  = $data[$zone]['date'] ?? '';
            $times = $data[$zone]['times'] ?? [];
            $hari  = $this->resolve_hari($date);
            $current_prayer = $this->detect_current_prayer($times, $date);
            $next_prayer = $this->detect_next_prayer($times, $date);
            $fetched_at = !empty($data[$zone]['fetched_at']) ? intval($data[$zone]['fetched_at']) : 0;

            $out .= '<div class="jws-card">';
            $out .= '<div class="jws-card-inner">';
            $out .= '<div class="jws-header">';
            $out .= '<div class="jws-header-left">';
            $out .= '<div class="jws-title">' . esc_html($title) . '</div>';
            if ($hari) {
                $out .= '<div class="jws-hari">' . esc_html($hari) . '</div>';
            }
            $out .= '</div>';
            if ($show_date && $date) {
                $out .= '<div class="jws-date-badge">' . esc_html($date) . '</div>';
            }
            $out .= '</div>';

            $out .= '<div class="jws-body">';
            $show_next_outside = $show_countdown && !empty($next_prayer['label']) && !empty($next_prayer['countdown']);
            $has_status = ($show_live && $current_prayer) || ($show_last_updated && $fetched_at > 0);
            if ($has_status) {
                $out .= '<div class="jws-status-stack">';
                if ($show_live && $current_prayer) {
                    $out .= '<div class="jws-live"><div class="jws-live-main"><span class="jws-live-dot"></span><span class="jws-live-label">Waktu semasa:</span> <span class="jws-live-value">' . esc_html($current_prayer) . '</span></div></div>';
                }
                if ($show_last_updated && $fetched_at > 0) {
                    $out .= '<div class="jws-updated"><div class="jws-updated-main"><span class="jws-updated-label">Kemaskini terakhir:</span> <span class="jws-updated-value">' . esc_html(wp_date('g:i a', $fetched_at)) . '</span></div></div>';
                }
                $out .= '</div>';
            }

            $out .= '<table class="jws-table" role="presentation">';
            foreach ($times as $k => $v) {
                $row_class = ($current_prayer === $k) ? ' class="jws-row-current"' : '';
                $label = esc_html($k);
                if ($current_prayer === $k) {
                    $label .= '<span class="jws-current-pill">Semasa</span>';
                }
                $out .= '<tr' . $row_class . '><td>' . $label . '</td><td>' . esc_html($v) . '</td></tr>';
            }
            $out .= '</table>';
            $out .= '</div>';
            if ($show_next_outside) {
                $out .= '<div class="jws-next-outside"><div class="jws-next"><div class="jws-next-main"><span class="jws-next-label">Waktu seterusnya</span><span class="jws-next-value">' . esc_html($next_prayer['label']) . '</span><span class="jws-next-time">Jam ' . esc_html($next_prayer['time']) . '</span></div><span class="jws-next-countdown">Lagi ' . esc_html($next_prayer['countdown']) . '</span></div></div>';
            }
            $out .= '</div>';
            $out .= '</div>';
        }
        $out .= '</div>';

        if ($show_footer) {
            $out .= '<div class="jws-jhrsolat">© Hakcipta oleh <a href="' . esc_url('https://ariff.my') . '">ariff.my</a></div>';
        }

        return $out;
    }

    private function sanitize_zones($zones_raw)
    {
        $zones = array_filter(array_map('trim', explode(',', strtoupper((string) $zones_raw))));
        $zones = array_values(array_intersect($zones, $this->default_zones));

        if (count($zones) === 0) {
            $zones = $this->default_zones;
        }

        return array_slice(array_unique($zones), 0, 4);
    }

    private function is_truthy($value)
    {
        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
    }

    private function label($zone)
    {
        return $this->zone_labels[$zone] ?? $zone;
    }

    private function resolve_hari(string $date)
    {
        $tz = wp_timezone();

        if (!$date) {
            $eng = wp_date('l', time(), $tz);
            return $this->hari_bm[$eng] ?? $eng;
        }

        $dt = DateTime::createFromFormat('d-m-Y', $date, $tz);
        if (!$dt) {
            $eng = wp_date('l', time(), $tz);
            return $this->hari_bm[$eng] ?? $eng;
        }

        $eng = $dt->format('l');
        return $this->hari_bm[$eng] ?? $eng;
    }

    private function detect_current_prayer(array $times, string $date = '')
    {
        if (empty($times)) return '';

        $timeline = $this->build_timeline($times, $date);
        if (empty($timeline)) return '';

        $now_ts = time();
        $current = '';
        foreach ($timeline as $label => $ts) {
            if ($now_ts >= $ts) {
                $current = $label;
            }
        }

        return $current;
    }

    private function detect_next_prayer(array $times, string $date = '')
    {
        if (empty($times)) return [];

        $timeline = $this->build_timeline($times, $date);
        if (empty($timeline)) return [];

        $now_ts = time();
        foreach ($timeline as $label => $ts) {
            if ($ts > $now_ts) {
                return [
                    'label' => $label,
                    'time' => $times[$label] ?? '',
                    'countdown' => $this->human_diff_compact($now_ts, $ts),
                    'timestamp' => $ts,
                ];
            }
        }

        return [];
    }

    private function human_diff_compact(int $from, int $to)
    {
        $diff = max(0, $to - $from);
        $hours = (int) floor($diff / HOUR_IN_SECONDS);
        $minutes = (int) floor(($diff % HOUR_IN_SECONDS) / MINUTE_IN_SECONDS);

        if ($hours > 0 && $minutes > 0) {
            return $hours . ' jam ' . $minutes . ' minit';
        }
        if ($hours > 0) {
            return $hours . ' jam';
        }
        return max(1, $minutes) . ' minit';
    }

    private function build_timeline(array $times, string $date = '')
    {
        $sequence = ['Imsak','Subuh','Syuruk','Dhuha','Zohor','Asar','Maghrib','Isyak'];
        $today = $date ?: wp_date('d-m-Y', time(), wp_timezone());
        $timeline = [];

        foreach ($sequence as $label) {
            if (empty($times[$label])) continue;
            $ts = $this->to_timestamp($today, $times[$label]);
            if ($ts) {
                $timeline[$label] = $ts;
            }
        }

        return $timeline;
    }

    private function to_timestamp(string $date, string $pretty_time)
    {
        $pretty_time = trim(strtolower($pretty_time));
        $tz = wp_timezone();

        $dt = DateTime::createFromFormat('d-m-Y g:i a', $date . ' ' . $pretty_time, $tz);
        if ($dt instanceof DateTime) {
            return $dt->getTimestamp();
        }

        $dt = DateTime::createFromFormat('d-m-Y H:i', $date . ' ' . $pretty_time, $tz);
        if ($dt instanceof DateTime) {
            return $dt->getTimestamp();
        }

        return false;
    }

    private function get_multi_zone_data(array $zones, int $cache_minutes)
    {
        $results = [];
        foreach ($zones as $zone) {
            $results[$zone] = $this->get_zone_data($zone, $cache_minutes);
        }
        return $results;
    }

    private function get_zone_data(string $zone, int $cache_minutes)
    {
        if (!in_array($zone, $this->default_zones, true)) {
            return ['error' => 'Zone tidak sah.'];
        }

        $cache_date = wp_date('Ymd', time(), wp_timezone());
        $transient_key = 'jws_v114_' . strtolower($zone) . '_' . $cache_date;
        $cached = get_transient($transient_key);
        if (is_array($cached)) return $cached;

        $url = 'https://www.e-solat.gov.my/index.php?r=esolatApi/xmlfeed&zon=' . rawurlencode($zone);

        //$resp = wp_remote_get($url, [
        //    'timeout' => 12,
        //    'headers' => [
        //        'Accept' => 'application/xml,text/xml,*/*;q=0.9',
        //    ],
        //]);
		
		$resp = wp_remote_get($url, [
			'timeout' => 20,
			'redirection' => 5,
			'httpversion' => '1.1',
			'blocking' => true,
			'sslverify' => true,
			'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0 Safari/537.36',
			'headers' => [
				'Accept' => 'application/xml,text/xml,*/*;q=0.9',
				'Referer' => home_url(),
				'Cache-Control' => 'no-cache',
				'Pragma' => 'no-cache',
			],
		]);

        if (is_wp_error($resp)) {
            return ['error' => 'Gagal akses API: ' . $resp->get_error_message()];
        }

        $http_code = (int) wp_remote_retrieve_response_code($resp);
        if ($http_code !== 200) {
            return ['error' => 'API mengembalikan status HTTP: ' . $http_code];
        }

        $body = wp_remote_retrieve_body($resp);
        if (!$body) {
            return ['error' => 'Tiada data diterima dari API.'];
        }

        $parsed = $this->parse_xml($body);
        if (!empty($parsed['error'])) {
            $parsed = $this->parse_fallback_text($body);
        }

        if (empty($parsed['error'])) {
            $parsed['fetched_at'] = time();
            set_transient($transient_key, $parsed, MINUTE_IN_SECONDS * $cache_minutes);
        }

        return $parsed;
    }

    private function parse_xml(string $body)
    {
        $body = trim($body);

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body);

        if ($xml === false) {
            return ['error' => 'XML tidak dapat diparse (format berubah / bukan XML).'];
        }

        $times = [];
        $candidates = ['Imsak','Subuh','Syuruk','Dhuha','Zohor','Asar','Maghrib','Isyak'];
        foreach ($candidates as $name) {
            $lower = strtolower($name);
            if (isset($xml->$lower) && (string) $xml->$lower !== '') {
                $times[$name] = $this->pretty_time((string) $xml->$lower);
            } elseif (isset($xml->$name) && (string) $xml->$name !== '') {
                $times[$name] = $this->pretty_time((string) $xml->$name);
            }
        }

        if (count($times) < 6) {
            $allText = strip_tags($body);
            $fallback = $this->parse_fallback_text($allText);
            if (empty($fallback['error']) && !empty($fallback['times'])) {
                $times = $fallback['times'];
                $date  = $fallback['date'] ?? '';
                return ['date' => $date, 'times' => $times];
            }
        }

        if (count($times) < 6) {
            return ['error' => 'Struktur XML tidak dikenali (tiada waktu solat ditemui).'];
        }

        $date = '';
        $allText = strip_tags($body);
        if (preg_match('/(\d{2}-\d{2}-\d{4})/', $allText, $m)) {
            $date = $m[1];
        }

        return ['date' => $date, 'times' => $times];
    }

    private function parse_fallback_text(string $text)
    {
        $text = ' ' . preg_replace('/\s+/', ' ', strip_tags($text)) . ' ';

        $date = '';
        if (preg_match('/(\d{2}-\d{2}-\d{4})/', $text, $m)) {
            $date = $m[1];
        }

        $map = [
            'Imsak'   => '/Imsak\s+(\d{2}:\d{2}(?::\d{2})?)/i',
            'Subuh'   => '/Subuh\s+(\d{2}:\d{2}(?::\d{2})?)/i',
            'Syuruk'  => '/Syuruk\s+(\d{2}:\d{2}(?::\d{2})?)/i',
            'Dhuha'   => '/Dhuha\s+(\d{2}:\d{2}(?::\d{2})?)/i',
            'Zohor'   => '/Zohor\s+(\d{2}:\d{2}(?::\d{2})?)/i',
            'Asar'    => '/Asar\s+(\d{2}:\d{2}(?::\d{2})?)/i',
            'Maghrib' => '/Maghrib\s+(\d{2}:\d{2}(?::\d{2})?)/i',
            'Isyak'   => '/Isyak\s+(\d{2}:\d{2}(?::\d{2})?)/i',
        ];

        $times = [];
        foreach ($map as $label => $pattern) {
            if (preg_match($pattern, $text, $m)) {
                $times[$label] = $this->pretty_time($m[1]);
            }
        }

        if (count($times) < 6) {
            return ['error' => 'Tidak jumpa waktu solat dalam respons API.'];
        }

        return ['date' => $date, 'times' => $times];
    }

    private function pretty_time(string $t)
    {
        if (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $t)) {
            $dt = DateTime::createFromFormat('H:i:s', $t, wp_timezone()) ?: DateTime::createFromFormat('H:i', $t, wp_timezone());
            if ($dt) {
                return strtolower($dt->format('g:i A'));
            }
        }
        return $t;
    }
}

new Johor_Waktu_Solat_Plugin();
