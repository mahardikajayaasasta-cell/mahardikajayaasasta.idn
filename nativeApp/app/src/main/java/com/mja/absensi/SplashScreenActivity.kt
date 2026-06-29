package com.mja.absensi

import android.content.Intent
import android.os.Bundle
import android.os.Handler
import android.os.Looper
import androidx.appcompat.app.AppCompatActivity

class SplashScreenActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_splash_screen)

        // Menyembunyikan Action Bar bawaan agar layar penuh (Full Screen)
        supportActionBar?.hide()

        // Menahan layar Splash selama 3000 milidetik (3 detik)
        Handler(Looper.getMainLooper()).postDelayed({
            val intent = Intent(this@SplashScreenActivity, MainActivity::class.java)
            startActivity(intent)
            finish() // Menutup Splash Screen agar tidak kembali saat tombol back ditekan
        }, 3000)
    }
}
