package com.muhammadrido.hotelreservasi.repository;

import com.muhammadrido.hotelreservasi.model.Reservasi;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.time.LocalDate;
import java.util.List;

/**
 * Repository Interface (JDBC/JPA)
 * Menyediakan akses data ke tabel 'reservasi' di MySQL
 * Menggunakan Spring Data JPA yang mengimplementasikan JDBC di balik layar
 */
@Repository
public interface ReservasiRepository extends JpaRepository<Reservasi, Long> {

    // Method untuk menghitung jumlah kamar aktif berdasarkan tipe kamar
    // yang bentrok di rentang tanggal tertentu
    @Query("SELECT COUNT(r) FROM Reservasi r WHERE r.tipeKamar = :tipeKamar " +
            "AND r.status = 'AKTIF' " +
            "AND r.tanggalCheckIn < :checkOut " +
            "AND r.tanggalCheckOut > :checkIn")
    long hitungKamarTerpakai(
            @Param("tipeKamar") String tipeKamar,
            @Param("checkIn") LocalDate checkIn,
            @Param("checkOut") LocalDate checkOut);

    // Cari semua reservasi berdasarkan status
    List<Reservasi> findByStatus(String status);

    // Cari reservasi berdasarkan nama tamu (search)
    List<Reservasi> findByNamaTamuContainingIgnoreCase(String namaTamu);
}
