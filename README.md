# Johor Waktu Solat (e-Solat XML)

Plugin WordPress untuk memaparkan waktu solat negeri Johor menggunakan data XML daripada API e-Solat JAKIM. Plugin ini menyediakan shortcode ringkas bagi memaparkan jadual waktu solat mengikut zon Johor JHR01 hingga JHR04.

## Ciri-ciri

- Paparan waktu solat Johor melalui shortcode `[johor_waktu_solat]`.
- Menyokong zon JHR01, JHR02, JHR03 dan JHR04.
- Paparan responsif dalam bentuk kad/grid.
- Menunjukkan hari, tarikh, waktu semasa, waktu seterusnya dan kiraan masa berbaki.
- Cache data menggunakan WordPress transient untuk mengurangkan panggilan API.
- Menggunakan data daripada API e-Solat JAKIM.
- Gaya CSS dimuatkan secara inline oleh plugin.

## Keperluan

- WordPress 5.2 atau lebih baharu.
- PHP 7.4 atau lebih baharu.
- Sambungan internet daripada pelayan WordPress untuk mengakses API e-Solat JAKIM.

## Pemasangan

1. Muat naik folder plugin ini ke direktori `wp-content/plugins/`.
2. Pastikan fail utama plugin ialah `johor-waktu-solat.php`.
3. Aktifkan plugin melalui menu **Plugins** di papan pemuka WordPress.
4. Masukkan shortcode `[johor_waktu_solat]` pada halaman, pos atau widget yang menyokong shortcode.

## Penggunaan Asas

Paparkan semua zon Johor:

```text
[johor_waktu_solat]
```

Paparkan zon tertentu sahaja:

```text
[johor_waktu_solat zones="JHR02"]
```

Paparkan dua zon dengan dua lajur:

```text
[johor_waktu_solat zones="JHR02,JHR04" columns="2"]
```

Paparkan tanpa footer dan tanpa kemaskini terakhir:

```text
[johor_waktu_solat show_footer="no" show_last_updated="no"]
```

## Zon Yang Disokong

| Kod Zon | Kawasan |
| --- | --- |
| JHR01 | Pulau Aur & Pulau Pemanggil |
| JHR02 | Johor Bahru, Kota Tinggi, Mersing, Kulai |
| JHR03 | Kluang, Pontian |
| JHR04 | Batu Pahat, Muar, Segamat, Gemas Johor, Tangkak |

## Atribut Shortcode

| Atribut | Nilai Lalai | Keterangan |
| --- | --- | --- |
| `zones` | `JHR01,JHR02,JHR03,JHR04` | Senarai zon dipisahkan dengan koma. Hanya JHR01 hingga JHR04 diterima. |
| `cache_minutes` | `10` | Tempoh cache data dalam minit. Nilai minimum 1 dan maksimum 1440. |
| `columns` | `4` | Bilangan lajur grid. Nilai minimum 1 dan maksimum 4. |
| `show_live` | `yes` | Papar waktu solat semasa. |
| `show_date` | `yes` | Papar tarikh pada kad. |
| `show_last_updated` | `yes` | Papar masa data terakhir dikemaskini. |
| `show_countdown` | `yes` | Papar waktu seterusnya dan kiraan masa berbaki. |
| `show_footer` | `yes` | Papar teks footer hak cipta. |

Nilai boolean yang diterima untuk pilihan paparan ialah `yes`, `true`, `1` atau `on`. Gunakan nilai selain itu seperti `no` untuk mematikan paparan.

## Sumber Data

Plugin ini mendapatkan data daripada endpoint e-Solat JAKIM:

```text
https://www.e-solat.gov.my/index.php?r=esolatApi/xmlfeed&zon=KOD_ZON
```

Contoh untuk zon JHR02:

```text
https://www.e-solat.gov.my/index.php?r=esolatApi/xmlfeed&zon=JHR02
```

Jika struktur XML berubah atau respons API tidak lengkap, plugin akan cuba membaca data menggunakan parser fallback berasaskan teks.

## Cache

Data setiap zon disimpan dalam transient WordPress berdasarkan zon dan tarikh semasa. Ini membantu mengurangkan permintaan berulang kepada API e-Solat. Tempoh cache boleh dikawal melalui atribut `cache_minutes`.

Contoh cache selama 30 minit:

```text
[johor_waktu_solat cache_minutes="30"]
```

## Penyesuaian Paparan

Plugin memuatkan CSS secara inline dengan class prefix `jws-`. Jika tema WordPress perlu mengubah gaya paparan, class seperti berikut boleh digunakan:

- `.jws-grid`
- `.jws-card`
- `.jws-header`
- `.jws-table`
- `.jws-live`
- `.jws-next`
- `.jws-jhrsolat`

## Penyelesaian Masalah

Jika waktu solat tidak dipaparkan:

1. Pastikan pelayan WordPress boleh mengakses `www.e-solat.gov.my`.
2. Semak sama ada zon yang digunakan ialah JHR01, JHR02, JHR03 atau JHR04.
3. Cuba kurangkan tempoh cache atau tunggu cache tamat.
4. Semak log ralat WordPress jika API mengembalikan status HTTP selain 200.

## Maklumat Plugin

- Nama plugin: Johor Waktu Solat (e-Solat XML)
- Versi: 1.1.5
- Pengarang: Ariff Samani
- Lesen: GPL2+
