package com.ahmadzidan.hotelreservasi.service;

import com.ahmadzidan.hotelreservasi.exception.KamarPenuhException;
import com.ahmadzidan.hotelreservasi.exception.ReservasiNotFoundException;
import com.ahmadzidan.hotelreservasi.model.Reservasi;
import com.ahmadzidan.hotelreservasi.repository.ReservasiRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

/**
 * Service Layer (Business Logic)
 * Berisi logika bisnis aplikasi reservasi hotel
 * Termasuk pengecekan ketersediaan kamar dan validasi data
 */
@Service
public class ReservasiService {

    @Autowired
    private ReservasiRepository reservasiRepository;

    // Kapasitas Maks Kamar per Tipe
    private static final Map<String, Integer> KAPASITAS_KAMAR = new HashMap<>();
    static {
        KAPASITAS_KAMAR.put("Standard", 10);
        KAPASITAS_KAMAR.put("Deluxe", 5);
        KAPASITAS_KAMAR.put("Suite", 3);
        KAPASITAS_KAMAR.put("President Suite", 1);
    }

    // Mendapatkan seluruh daftar reservasi
    public List<Reservasi> semuaReservasi() {
        return reservasiRepository.findAll();
    }

    // Mendapatkan detail reservasi berdasarkan ID
    public Reservasi cariReservasiById(Long id) {
        return reservasiRepository.findById(id)
                .orElseThrow(() -> new ReservasiNotFoundException(id));
    }

    // Membuat reservasi baru dengan pengecekan kapasitas kamar
    public Reservasi buatReservasi(Reservasi reservasi) {
        // Validasi tanggal: check-out harus setelah check-in
        if (reservasi.getTanggalCheckOut().isBefore(reservasi.getTanggalCheckIn()) ||
                reservasi.getTanggalCheckOut().isEqual(reservasi.getTanggalCheckIn())) {
            throw new RuntimeException("Tanggal check-out harus setelah tanggal check-in!");
        }

        // Cek ketersediaan kamar
        String tipeKamar = reservasi.getTipeKamar();
        int kapasitasMaks = KAPASITAS_KAMAR.getOrDefault(tipeKamar, 5);

        long kamarTerpakai = reservasiRepository.hitungKamarTerpakai(
                tipeKamar,
                reservasi.getTanggalCheckIn(),
                reservasi.getTanggalCheckOut());

        // Jika kamar sudah penuh, lempar KamarPenuhException
        if (kamarTerpakai >= kapasitasMaks) {
            throw new KamarPenuhException(tipeKamar);
        }

        // Auto-assign nomor kamar
        reservasi.setNomorKamar(tipeKamar.substring(0, 1).toUpperCase() + "-" + (kamarTerpakai + 1));
        reservasi.setStatus("AKTIF");

        return reservasiRepository.save(reservasi);
    }

    // Mengupdate data reservasi
    public Reservasi updateReservasi(Long id, Reservasi reservasiUpdate) {
        Reservasi existing = cariReservasiById(id);

        existing.setNamaTamu(reservasiUpdate.getNamaTamu());
        existing.setTipeKamar(reservasiUpdate.getTipeKamar());
        existing.setTanggalCheckIn(reservasiUpdate.getTanggalCheckIn());
        existing.setTanggalCheckOut(reservasiUpdate.getTanggalCheckOut());

        return reservasiRepository.save(existing);
    }

    // Check-out (ubah status menjadi selesai)
    public Reservasi checkOut(Long id) {
        Reservasi reservasi = cariReservasiById(id);
        reservasi.setStatus("SELESAI");
        return reservasiRepository.save(reservasi);
    }

    // Menghapus reservasi
    public void hapusReservasi(Long id) {
        Reservasi reservasi = cariReservasiById(id);
        reservasiRepository.delete(reservasi);
    }

    // Cari reservasi berdasarkan nama tamu
    public List<Reservasi> cariByNama(String nama) {
        return reservasiRepository.findByNamaTamuContainingIgnoreCase(nama);
    }

    // Mendapatkan kapasitas kamar
    public Map<String, Integer> getKapasitasKamar() {
        return KAPASITAS_KAMAR;
    }
}
