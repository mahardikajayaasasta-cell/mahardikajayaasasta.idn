# Aplikasi Reservasi Hotel (Spring Boot + Thymeleaf + MySQL)

Tugas Pemrograman 2 - **Ahmad Zidan**

Aplikasi web Reservasi Hotel yang dibangun dengan menggunakan bahasa Java, framework Spring Boot, template engine Thymeleaf, dan database MySQL melalui JPA/JDBC.

---

## 🛠️ Spesifikasi & Fitur Program
1. **Framework Java Web:** Menggunakan Spring Boot 3.2.5 dengan Spring Web MVC.
2. **Template Engine:** Thymeleaf untuk merender halaman HTML dinamis yang sudah terintegrasi dengan Bootstrap 5.
3. **Database & ORM:** MySQL menggunakan Spring Data JPA (yang mengabstraksikan koneksi JDBC).
4. **Desain Berorientasi Objek (OOP):** Implementasi class `Reservasi` dengan atribut encapsulation (Getter & Setter), Constructor Overloading, dan method overriding (`toString`).
5. **Kapasitas Kamar & Ketersediaan:** Proteksi otomatis jika pemesanan kamar melebihi kuota per-tipe kamar pada rentang tanggal check-in/check-out yang sama.
6. **Exception Handling:** Penanganan error kustom berupa `KamarPenuhException` dan `ReservasiNotFoundException` dengan tampilan halaman error yang user-friendly (Global Exception Handler (`@ControllerAdvice`)).
7. **Fitur Tambahan:**
   - Fitur pencarian nama tamu secara real-time.
   - Status Check-out otomatis (mengubah status AKTIF menjadi SELESAI).
   - Fitur hapus data reservasi.

---

## 📋 Struktur Folder Project (Standar Spring Boot)
```text
hotelReservasi/
├── pom.xml                                   # File Konfigurasi Maven & Dependency
└── src/
    └── main/
        ├── java/
        │   └── com/ahmadzidan/hotelreservasi/
        │       ├── HotelReservasiApplication.java # Entry Point Aplikasi
        │       ├── controller/
        │       │   └── ReservasiController.java   # Controller HTTP Route
        │       ├── exception/
        │       │   ├── KamarPenuhException.java   # Custom Exception
        │       │   ├── ReservasiNotFoundException.java
        │       │   └── GlobalExceptionHandler.java # Handler Error Global
        │       ├── model/
        │       │   └── Reservasi.java             # Entity / Class OOP Reservasi
        │       ├── repository/
        │       │   └── ReservasiRepository.java   # Database Access (JPA/JDBC)
        │       └── service/
        │           └── ReservasiService.java      # Logika Bisnis & Check Kapasitas
        └── resources/
            ├── application.properties             # Konfigurasi Koneksi Database & Port
            ├── schema.sql                         # Schema Database MySQL
            ├── static/
            │   └── css/
            │       └── style.css                  # Custom Styling CSS
            └── templates/
                ├── index.html                     # Halaman Utama (Daftar Reservasi)
                ├── form.html                      # Halaman Tambah/Edit Reservasi
                ├── detail.html                    # Halaman Detail Reservasi
                └── error.html                     # Halaman Tampilan Kesalahan/Error
```

---

## 🚀 Cara Import & Menjalankan di Apache NetBeans
1. Buka **NetBeans IDE** Anda.
2. Pilih menu **File** -> **Open Project**.
3. Arahkan ke folder tempat Anda menaruh folder repositori ini dan pilih folder **`hotelReservasi`** (ikon project bertanda Maven/Spring Boot).
4. Jalankan aplikasi database local server Anda (seperti **XAMPP** / **MySQL**). Pastikan port database Anda adalah default `3306` dan password mysql kosong `""` (sesuai settingan di `application.properties`).
5. Klik kanan pada project **hotel-reservasi** di NetBeans, lalu pilih **Run** (atau klik tombol **Play/Run** di toolbar NetBeans).
6. NetBeans akan otomatis mengunduh dependency Maven yang diperlukan dan membuat database bernama `hotel_reservasi` secara otomatis.
7. Setelah log konsol menampilkan kata `Aplikasi Reservasi Hotel Berhasil Jalan!`, buka browser dan akses:
   👉 **http://localhost:8080**
