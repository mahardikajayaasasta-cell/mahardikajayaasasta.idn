package com.mja.absensi

import android.Manifest
import android.annotation.SuppressLint
import android.app.Activity
import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.location.Location
import android.location.LocationManager
import android.net.Uri
import android.os.Build
import android.os.Bundle
import android.provider.MediaStore
import android.provider.Settings
import android.webkit.GeolocationPermissions
import android.webkit.ValueCallback
import android.webkit.WebChromeClient
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.core.app.ActivityCompat
import androidx.core.content.ContextCompat
import androidx.core.content.FileProvider
import java.io.File
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

class MainActivity : AppCompatActivity() {

    private lateinit var webView: WebView
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

        webView.webViewClient = WebViewClient()
        
        webView.webChromeClient = object : WebChromeClient() {
            
            // Mengizinkan lokasi secara diam-diam (karena permisi native sudah diminta)
            override fun onGeolocationPermissionsShowPrompt(
                origin: String?,
                callback: GeolocationPermissions.Callback?
            ) {
                callback?.invoke(origin, true, false)
            }

            // ANTI FAKE KAMERA & GALLERY CATCHER
            // Memaksa input type="file" di web untuk membuka Kamera Native dan BUKAN Galeri
            override fun onShowFileChooser(
                webView: WebView?,
                filePathCallback: ValueCallback<Array<Uri>>?,
                fileChooserParams: FileChooserParams?
            ): Boolean {
                if (mUploadMessage != null) {
                    mUploadMessage?.onReceiveValue(null)
                }
                mUploadMessage = filePathCallback

                // Buat file sementara untuk tangkapan kamera
                val timeStamp: String = SimpleDateFormat("yyyyMMdd_HHmmss", Locale.getDefault()).format(Date())
                val imageFile = File.createTempFile("JPEG_${timeStamp}_", ".jpg", cacheDir)
                
                // Gunakan FileProvider agar aman di Android 7.0+
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
    }

    // Penanganan Hasil Foto Kamera Kembali ke WebView (Lapisan Web)
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
            Manifest.permission.CAMERA
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
        if (requestCode == PERMISSION_REQUEST_CODE) {
            loadWebApp()
        }
    }

    private fun loadWebApp() {
        webView.loadUrl(webAppUrl)
    }

    // -------------------------------------------------------------
    // FITUR KEAMANAN SIDANG PROGRAM (ANTI CHEAT / ANTI FAKE)
    // -------------------------------------------------------------

    // 1. ANTI FAKE GPS (Deteksi Fake GPS Apps & Developer Option)
    private fun isMockSettingsON(): Boolean {
        try {
            // Metode A: Cek setting developer "Allow Mock Location" (Android < 6.0)
            if (Build.VERSION.SDK_INT < Build.VERSION_CODES.M) {
                val mockLocation = Settings.Secure.getString(
                    contentResolver,
                    Settings.Secure.ALLOW_MOCK_LOCATION
                )
                if (mockLocation != null && mockLocation != "0") {
                    return true
                }
            }

            // Metode B: Scan aplikasi Fake GPS yang terinstall di HP (Android 6.0+)
            // Daftar package name aplikasi Fake GPS populer
            val fakeGpsPackages = arrayOf(
                "com.lexa.fakegps",               // Fake GPS Location
                "com.incorporateapps.fakegps",     // Fake GPS GO
                "com.fakegps.mock",                // Mock GPS
                "com.blogspot.newapphorizons.fakegps", // Fake GPS Free
                "ru.gavrikov.mocklocations",       // Mock Locations
                "com.evezzon.fakegps",             // Fake GPS Joystick
                "com.theappninjas.gpsjoystick",    // GPS JoyStick
                "com.divi.fakeGPS",                // Fake GPS
                "fr.dvilleneuve.lockito"           // Lockito (developer tool)
            )

            val pm = packageManager
            for (packageName in fakeGpsPackages) {
                try {
                    pm.getPackageInfo(packageName, PackageManager.GET_META_DATA)
                    // Jika tidak error, berarti aplikasi Fake GPS terinstall!
                    return true
                } catch (e: PackageManager.NameNotFoundException) {
                    // Aplikasi tidak ditemukan, lanjut cek yang lain
                }
            }

        } catch (e: Exception) {
            e.printStackTrace()
        }
        return false
    }

    // 2. ANTI ROOT / EMULATOR (Check for common su binaries & test tags)
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

    @Deprecated("Deprecated in Java", ReplaceWith("if (webView.canGoBack()) webView.goBack() else super.onBackPressed()"))
    override fun onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack()
        } else {
            super.onBackPressed()
        }
    }
}
