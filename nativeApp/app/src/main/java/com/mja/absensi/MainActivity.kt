package com.mja.absensi

import android.Manifest
import android.annotation.SuppressLint
import android.app.Activity
import android.app.DownloadManager
import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.net.ConnectivityManager
import android.net.NetworkCapabilities
import android.net.Uri
import android.os.Build
import android.os.Bundle
import android.os.Environment
import android.provider.MediaStore
import android.provider.Settings
import android.view.View
import android.webkit.CookieManager
import android.webkit.DownloadListener
import android.webkit.GeolocationPermissions
import android.webkit.URLUtil
import android.webkit.ValueCallback
import android.webkit.WebChromeClient
import android.webkit.WebResourceError
import android.webkit.WebResourceRequest
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import android.widget.ProgressBar
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.core.app.ActivityCompat
import androidx.core.content.ContextCompat
import androidx.core.content.FileProvider
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import java.io.File
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

class MainActivity : AppCompatActivity() {

    private lateinit var webView: WebView
    private lateinit var swipeRefresh: SwipeRefreshLayout
    private lateinit var progressBar: ProgressBar

    private val PERMISSION_REQUEST_CODE = 123
    private val FILECHOOSER_RESULTCODE = 101

    private var mUploadMessage: ValueCallback<Array<Uri>>? = null
    private var photoURI: Uri? = null

    // URL Vercel Laravel Anda
    private val webAppUrl = "https://mahardikajayaasasta-idn.vercel.app/"

    @SuppressLint("SetJavaScriptEnabled")
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        webView = findViewById(R.id.webView)
        swipeRefresh = findViewById(R.id.swipeRefresh)
        progressBar = findViewById(R.id.progressBar)

        // 1. Cek Keamanan Dulu (Anti Root / Emulator)
        if (isDeviceRooted()) {
            showSecurityAlert("Perangkat Root Terdeteksi", "Aplikasi absensi tidak bisa dijalankan di perangkat root demi keamanan data.")
            return
        }

        // 2. Cek Anti Fake GPS (Opsi Deteksi Opsi Pengembang)
        if (isMockSettingsON()) {
            showSecurityAlert("Fake GPS Terdeteksi", "Harap matikan fitur 'Aplikasi Lokasi Palsu' (Mock Location) di Pengaturan Pengembang untuk melakukan absensi.")
            return
        }

        // FITUR 1: Swipe to Refresh (Tarik ke Bawah untuk Memuat Ulang)
        swipeRefresh.setOnRefreshListener {
            if (isNetworkAvailable()) {
                webView.reload()
            } else {
                swipeRefresh.isRefreshing = false
                Toast.makeText(this, "Tidak ada koneksi internet", Toast.LENGTH_SHORT).show()
            }
        }

        setupWebView()
        checkPermissions()
    }

    private fun setupWebView() {
        val webSettings: WebSettings = webView.settings
        webSettings.javaScriptEnabled = true
        webSettings.domStorageEnabled = true
        webSettings.setGeolocationEnabled(true)
        webSettings.allowFileAccess = true
        webSettings.useWideViewPort = true
        webSettings.loadWithOverviewMode = true

        // FITUR 2: Progress Bar & Loading Indicator
        webView.webChromeClient = object : WebChromeClient() {
            override fun onProgressChanged(view: WebView?, newProgress: Int) {
                super.onProgressChanged(view, newProgress)
                if (newProgress == 100) {
                    progressBar.visibility = View.GONE
                    swipeRefresh.isRefreshing = false
                } else {
                    progressBar.visibility = View.VISIBLE
                    progressBar.progress = newProgress
                }
            }

            // Mengizinkan lokasi secara diam-diam (karena permisi native sudah diminta)
            override fun onGeolocationPermissionsShowPrompt(
                origin: String?,
                callback: GeolocationPermissions.Callback?
            ) {
                callback?.invoke(origin, true, false)
            }

            // ANTI FAKE KAMERA & GALLERY CATCHER
            override fun onShowFileChooser(
                webView: WebView?,
                filePathCallback: ValueCallback<Array<Uri>>?,
                fileChooserParams: FileChooserParams?
            ): Boolean {
                if (mUploadMessage != null) {
                    mUploadMessage?.onReceiveValue(null)
                }
                mUploadMessage = filePathCallback
                val timeStamp: String = SimpleDateFormat("yyyyMMdd_HHmmss", Locale.getDefault()).format(Date())
                val imageFile = File.createTempFile("JPEG_${timeStamp}_", ".jpg", cacheDir)
                photoURI = FileProvider.getUriForFile(this@MainActivity, "${applicationContext.packageName}.provider", imageFile)
                val captureIntent = Intent(MediaStore.ACTION_IMAGE_CAPTURE)
                captureIntent.putExtra(MediaStore.EXTRA_OUTPUT, photoURI)
                try {
                    startActivityForResult(captureIntent, FILECHOOSER_RESULTCODE)
                } catch (e: Exception) {
                    mUploadMessage = null
                    return false
                }
                return true
            }
        }

        // FITUR 3: Penanganan Koneksi Terputus (Network Error Handler)
        webView.webViewClient = object : WebViewClient() {
            override fun onReceivedError(view: WebView?, request: WebResourceRequest?, error: WebResourceError?) {
                super.onReceivedError(view, request, error)
                if(!isNetworkAvailable()) {
                    Toast.makeText(this@MainActivity, "Koneksi terputus. Geser ke bawah untuk muat ulang.", Toast.LENGTH_LONG).show()
                }
            }
        }

        // FITUR 4: Download Manager Support (Bisa mengunduh PDF/Excel Laporan dari Web)
        webView.setDownloadListener { url, userAgent, contentDisposition, mimetype, contentLength ->
            try {
                val request = DownloadManager.Request(Uri.parse(url))
                request.setMimeType(mimetype)
                request.addRequestHeader("cookie", CookieManager.getInstance().getCookie(url))
                request.addRequestHeader("User-Agent", userAgent)
                request.setDescription("Mengunduh file...")
                request.setTitle(URLUtil.guessFileName(url, contentDisposition, mimetype))
                request.allowScanningByMediaScanner()
                request.setNotificationVisibility(DownloadManager.Request.VISIBILITY_VISIBLE_NOTIFY_COMPLETED)
                request.setDestinationInExternalPublicDir(Environment.DIRECTORY_DOWNLOADS, URLUtil.guessFileName(url, contentDisposition, mimetype))

                val dm = getSystemService(Context.DOWNLOAD_SERVICE) as DownloadManager
                dm.enqueue(request)
                Toast.makeText(applicationContext, "Mendownload file...", Toast.LENGTH_LONG).show()
            } catch (e: Exception) {
                Toast.makeText(applicationContext, "Gagal memulai unduhan", Toast.LENGTH_SHORT).show()
            }
        }
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        if (requestCode == FILECHOOSER_RESULTCODE) {
            if (mUploadMessage == null) return
            var results: Array<Uri>? = null

            // Hanya terima file jika jepretan berhasil
            if (resultCode == Activity.RESULT_OK) {
                if (photoURI != null) {
                    results = arrayOf(photoURI!!)
                }
            }
            // Kirim gambar ke Form Laravel di WebView
            mUploadMessage?.onReceiveValue(results)
            mUploadMessage = null
        } else {
            super.onActivityResult(requestCode, resultCode, data)
        }
    }

    private fun checkPermissions() {
        val permissions = arrayOf(
            Manifest.permission.ACCESS_FINE_LOCATION,
            Manifest.permission.ACCESS_COARSE_LOCATION,
            Manifest.permission.CAMERA,
            Manifest.permission.WRITE_EXTERNAL_STORAGE
        )

        val permissionsNeedReq = permissions.filter {
            ContextCompat.checkSelfPermission(this, it) != PackageManager.PERMISSION_GRANTED
        }

        if (permissionsNeedReq.isNotEmpty()) {
            ActivityCompat.requestPermissions(
                this,
                permissionsNeedReq.toTypedArray(),
                PERMISSION_REQUEST_CODE
            )
        } else {
            loadWebApp()
        }
    }

    override fun onRequestPermissionsResult(
        requestCode: Int,
        permissions: Array<out String>,
        grantResults: IntArray
    ) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults)
        loadWebApp() // Tetap load webApp meski ditolak sebagian agar user tau batasannya
    }

    private fun loadWebApp() {
        if (isNetworkAvailable()) {
            webView.loadUrl(webAppUrl)
        } else {
            Toast.makeText(this, "Tidak ada koneksi internet!", Toast.LENGTH_LONG).show()
        }
    }

    // Utilitas Cek Internet
    private fun isNetworkAvailable(): Boolean {
        val connectivityManager = getSystemService(Context.CONNECTIVITY_SERVICE) as ConnectivityManager
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            val network = connectivityManager.activeNetwork ?: return false
            val activeNetwork = connectivityManager.getNetworkCapabilities(network) ?: return false
            return when {
                activeNetwork.hasTransport(NetworkCapabilities.TRANSPORT_WIFI) -> true
                activeNetwork.hasTransport(NetworkCapabilities.TRANSPORT_CELLULAR) -> true
                activeNetwork.hasTransport(NetworkCapabilities.TRANSPORT_ETHERNET) -> true
                else -> false
            }
        } else {
            @Suppress("DEPRECATION")
            val networkInfo = connectivityManager.activeNetworkInfo ?: return false
            @Suppress("DEPRECATION")
            return networkInfo.isConnected
        }
    }

    // FITUR 5: Konfirmasi Dialog Keluar Aplikasi (Mencegah salah pencet tombol Back)
    @Deprecated("Deprecated in Java", ReplaceWith("if (webView.canGoBack()) webView.goBack() else showExitConfirmation()"))
    override fun onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack()
        } else {
            AlertDialog.Builder(this)
                .setTitle("Keluar Aplikasi")
                .setMessage("Apakah Anda yakin ingin keluar dari aplikasi absensi?")
                .setPositiveButton("Ya") { _, _ -> super.onBackPressed() }
                .setNegativeButton("Tidak", null)
                .show()
        }
    }

    // -------------------------------------------------------------
    // FITUR KEAMANAN (ANTI CHEAT / ANTI FAKE)
    // -------------------------------------------------------------
    private fun isMockSettingsON(): Boolean {
        try {
            if (Build.VERSION.SDK_INT < Build.VERSION_CODES.M) {
                val mockLocation = Settings.Secure.getString(
                    contentResolver,
                    Settings.Secure.ALLOW_MOCK_LOCATION
                )
                if (mockLocation != null && mockLocation != "0") {
                    return true
                }
            }
            val fakeGpsPackages = arrayOf(
                "com.lexa.fakegps", "com.incorporateapps.fakegps", "com.fakegps.mock",
                "com.blogspot.newapphorizons.fakegps", "ru.gavrikov.mocklocations",
                "com.evezzon.fakegps", "com.theappninjas.gpsjoystick",
                "com.divi.fakeGPS", "fr.dvilleneuve.lockito"
            )
            val pm = packageManager
            for (packageName in fakeGpsPackages) {
                try {
                    pm.getPackageInfo(packageName, PackageManager.GET_META_DATA)
                    return true
                } catch (e: PackageManager.NameNotFoundException) {}
            }
        } catch (e: Exception) { e.printStackTrace() }
        return false
    }

    private fun isDeviceRooted(): Boolean {
        return checkRootMethod1() || checkRootMethod2() || checkRootMethod3()
    }

    private fun checkRootMethod1(): Boolean {
        val buildTags = Build.TAGS
        return buildTags != null && buildTags.contains("test-keys")
    }

    private fun checkRootMethod2(): Boolean {
        val paths = arrayOf(
            "/system/app/Superuser.apk", "/sbin/su", "/system/bin/su",
            "/system/xbin/su", "/data/local/xbin/su", "/data/local/bin/su",
            "/system/sd/xbin/su", "/system/bin/failsafe/su", "/data/local/su", "/su/bin/su"
        )
        for (path in paths) {
            if (File(path).exists()) return true
        }
        return false
    }

    private fun checkRootMethod3(): Boolean {
        var process: Process? = null
        return try {
            process = Runtime.getRuntime().exec(arrayOf("/system/xbin/which", "su"))
            val `in` = process.inputStream.bufferedReader()
            `in`.readLine() != null
        } catch (t: Throwable) {
            false
        } finally {
            process?.destroy()
        }
    }

    private fun showSecurityAlert(title: String, message: String) {
        AlertDialog.Builder(this)
            .setTitle(title)
            .setMessage(message)
            .setCancelable(false)
            .setPositiveButton("Tutup Aplikasi") { _, _ -> finish() }
            .show()
    }
}
