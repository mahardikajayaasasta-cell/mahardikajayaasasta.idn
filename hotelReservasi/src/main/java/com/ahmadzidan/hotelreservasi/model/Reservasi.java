package com.ahmadzidan.hotelreservasi.model;

import jakarta.persistence.*;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;
import java.time.LocalDate;

/**
 * Class POJO/Entity: Reservasi
 * Merepresentasikan data reservasi kamar hotel
 * Menerapkan konsep OOP: Encapsulation, Constructor Overloading, toString Override
 * Atribut: id, namaTamu, tipeKamar, tanggalCheckIn, tanggalCheckOut
 */
@Entity
@Table(name = "reservasi")
public class Reservasi {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @NotBlank(message = "Nama tamu tidak boleh kosong!")
    @Column(name = "nama_tamu", nullable = false)
    private String namaTamu;

    @NotBlank(message = "Tipe kamar harus dipilih!")
    @Column(name = "tipe_kamar", nullable = false)
    private String tipeKamar;

    @NotNull(message = "Tanggal check-in harus diisi!")
    @Column(name = "tanggal_check_in", nullable = false)
    private LocalDate tanggalCheckIn;

    @NotNull(message = "Tanggal check-out harus diisi!")
    @Column(name = "tanggal_check_out", nullable = false)
    private LocalDate tanggalCheckOut;

    @Column(name = "nomor_kamar")
    private String nomorKamar;

    @Column(name = "status")
    private String status = "AKTIF";

    // ============================
    // Constructor Default (Wajib JPA)
    // ============================
    public Reservasi() {
    }

    // ============================
    // Constructor dengan Parameter (OOP)
    // ============================
    public Reservasi(String namaTamu, String tipeKamar, LocalDate tanggalCheckIn, LocalDate tanggalCheckOut) {
        this.namaTamu = namaTamu;
        this.tipeKamar = tipeKamar;
        this.tanggalCheckIn = tanggalCheckIn;
        this.tanggalCheckOut = tanggalCheckOut;
    }

    // ============================
    // Getter dan Setter (Encapsulation - OOP)
    // ============================
    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public String getNamaTamu() {
        return namaTamu;
    }

    public void setNamaTamu(String namaTamu) {
        this.namaTamu = namaTamu;
    }

    public String getTipeKamar() {
        return tipeKamar;
    }

    public void setTipeKamar(String tipeKamar) {
        this.tipeKamar = tipeKamar;
    }

    public LocalDate getTanggalCheckIn() {
        return tanggalCheckIn;
    }

    public void setTanggalCheckIn(LocalDate tanggalCheckIn) {
        this.tanggalCheckIn = tanggalCheckIn;
    }

    public LocalDate getTanggalCheckOut() {
        return tanggalCheckOut;
    }

    public void setTanggalCheckOut(LocalDate tanggalCheckOut) {
        this.tanggalCheckOut = tanggalCheckOut;
    }

    public String getNomorKamar() {
        return nomorKamar;
    }

    public void setNomorKamar(String nomorKamar) {
        this.nomorKamar = nomorKamar;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    // ============================
    // Method toString (OOP - Override)
    // ============================
    @Override
    public String toString() {
        return "Reservasi{" +
                "id=" + id +
                ", namaTamu='" + namaTamu + '\'' +
                ", tipeKamar='" + tipeKamar + '\'' +
                ", tanggalCheckIn=" + tanggalCheckIn +
                ", tanggalCheckOut=" + tanggalCheckOut +
                ", nomorKamar='" + nomorKamar + '\'' +
                ", status='" + status + '\'' +
                '}';
    }
}
