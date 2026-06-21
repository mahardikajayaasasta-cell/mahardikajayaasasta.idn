package com.example.webkulinerterintegrasiwebviewtoast;

import android.annotation.SuppressLint;
import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.view.View;
import android.webkit.JavascriptInterface;
import android.webkit.WebChromeClient;
import android.webkit.WebResourceRequest;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.ProgressBar;
import android.widget.Toast;
import androidx.activity.OnBackPressedCallback;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.app.NotificationCompat;
import androidx.core.app.NotificationManagerCompat;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

public class MainActivity extends AppCompatActivity {
    private WebView webView;
    private ProgressBar progressBar;
    private SwipeRefreshLayout swipeRefreshLayout;

    @SuppressLint("SetJavaScriptEnabled")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        // 1. Inisialisasi View dari XML Layout
        webView = findViewById(R.id.webView);
        progressBar = findViewById(R.id.progressBar);
        swipeRefreshLayout = findViewById(R.id.swipeRefreshLayout);

        // 2. Konfigurasi Pengaturan WebView
        WebSettings webSettings = webView.getSettings();
        webSettings.setJavaScriptEnabled(true); // Aktifkan JavaScript untuk interaksi dua arah
        webSettings.setDomStorageEnabled(true); // Aktifkan DOM Storage untuk website modern
        webSettings.setDatabaseEnabled(true); // Aktifkan Database lokal agar cart tersimpan aman
        webSettings.setAllowFileAccess(true); // Izinkan pembacaan aset lokal

        // PENTING: Aktifkan Viewport agar Tailwind / Mobile Layout berfungsi!
        webSettings.setUseWideViewPort(true);
        webSettings.setLoadWithOverviewMode(true);

        // 3. Konfigurasi SwipeRefreshLayout (Pull-to-Refresh)
        swipeRefreshLayout.setColorSchemeColors(getResources().getColor(android.R.color.holo_orange_dark));
        swipeRefreshLayout.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {
                webView.reload(); // Memuat ulang halaman saat pengguna menarik layar ke bawah
            }
        });

        // 4. Custom WebViewClient untuk Navigasi Halaman & Intent Eksternal
        webView.setWebViewClient(new WebViewClient() {
            @Override
            public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
                Uri uri = request.getUrl();
                String url = uri.toString();

                // Cek skema khusus seperti WhatsApp, Telepon (tel:), Email (mailto:), dll.
                if (url.startsWith("tel:") || url.startsWith("mailto:") ||
                        url.startsWith("whatsapp:") || url.contains("api.whatsapp.com") || url.contains("wa.me")) {
                    try {
                        // Buka aplikasi eksternal (misal: WhatsApp, Dialler, Gmail)
                        Intent intent = new Intent(Intent.ACTION_VIEW, uri);
                        startActivity(intent);
                        return true; // Berhasil di-handle secara native
                    } catch (Exception e) {
                        Toast.makeText(MainActivity.this,
                                "Aplikasi untuk membuka tautan ini tidak ditemukan!",
                                Toast.LENGTH_SHORT).show();
                        return true;
                    }
                }
                return false; // WebView biasa tetap memuat link standar di dalam aplikasi
            }

            @Override
            public void onPageFinished(WebView view, String url) {
                super.onPageFinished(view, url);
                progressBar.setVisibility(View.GONE); // Sembunyikan Progress Bar
                swipeRefreshLayout.setRefreshing(false); // Matikan loading animasi SwipeRefresh
            }
        });

        webSettings.setGeolocationEnabled(true);

        // 5. WebChromeClient untuk Memantau Progress Loading WebView & Geolocation
        webView.setWebChromeClient(new WebChromeClient() {
            @Override
            public void onProgressChanged(WebView view, int newProgress) {
                if (newProgress < 100) {
                    progressBar.setVisibility(View.VISIBLE);
                } else {
                    progressBar.setVisibility(View.GONE);
                }
                super.onProgressChanged(view, newProgress);
            }

            @Override
            public void onGeolocationPermissionsShowPrompt(String origin,
                    android.webkit.GeolocationPermissions.Callback callback) {
                // Memberikan izin GPS secara otomatis di browser WebView
                callback.invoke(origin, true, false);
            }
        });

        // 7. Muat web live Absensi MJA
        webView.loadUrl("https://mahardikajayaasasta-idn.vercel.app");

        // 8. Penanganan tombol Back modern menggunakan OnBackPressedDispatcher
        // (Pengganti onBackPressed() deprecated)
        getOnBackPressedDispatcher().addCallback(this, new OnBackPressedCallback(true) {
            @Override
            public void handleOnBackPressed() {
                if (webView.canGoBack()) {
                    webView.goBack(); // Kembali ke halaman web sebelumnya jika ada riwayat
                } else {
                    // Keluar dari aplikasi dengan aman jika berada di halaman pertama
                    setEnabled(false);
                    MainActivity.this.getOnBackPressedDispatcher().onBackPressed();
                }
            }
        });
    }

    // Kelas Interface JavaScript (Jembatan Komunikasi Web-to-Native)
    public class WebInterface {
        private final Context mContext;

        public WebInterface(Context c) {
            mContext = c;
        }

        // Fitur Utama 5: Metode menampilkan Toast Native di Android
        @JavascriptInterface
        public void showToast(String menuName) {
            String message = "Menu [" + menuName + "] sukses ditambahkan ke keranjang!";
            Toast.makeText(mContext, message, Toast.LENGTH_SHORT).show();
        }

        // Fitur Premium: Kirim Notifikasi Native Android ke System Tray HP
        @JavascriptInterface
        public void showNotification(String title, String message) {
            createNotificationChannel();

            NotificationCompat.Builder builder = new NotificationCompat.Builder(mContext, "kuliner_channel")
                    .setSmallIcon(android.R.drawable.ic_dialog_info)
                    .setContentTitle(title)
                    .setContentText(message)
                    .setPriority(NotificationCompat.PRIORITY_DEFAULT)
                    .setAutoCancel(true);

            NotificationManagerCompat notificationManager = NotificationManagerCompat.from(mContext);
            try {
                // Catatan: Android 13+ memerlukan izin notifikasi dinamis POST_NOTIFICATIONS.
                // Jika izin tidak aktif, kami tangani dengan exception dan dialihkan ke Toast.
                notificationManager.notify(99, builder.build());
            } catch (SecurityException e) {
                e.printStackTrace();
                // Fallback ke Toast
                Toast.makeText(mContext, title + ": " + message, Toast.LENGTH_LONG).show();
            }
        }

        // Membuat Channel Notifikasi untuk Android 8.0 (Oreo) ke atas
        private void createNotificationChannel() {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
                CharSequence name = "Web Kuliner Channel";
                String description = "Notifikasi konfirmasi pesanan kuliner";
                int importance = NotificationManager.IMPORTANCE_DEFAULT;
                NotificationChannel channel = new NotificationChannel("kuliner_channel", name, importance);
                channel.setDescription(description);

                NotificationManager manager = mContext.getSystemService(NotificationManager.class);
                if (manager != null) {
                    manager.createNotificationChannel(channel);
                }
            }
        }
    }
}