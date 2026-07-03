package com.ahmadzidan.hotelreservasi.exception;

/**
 * Custom Exception: KamarPenuhException
 * Dilempar saat semua kamar dari tipe tertentu sudah penuh/terpakai
 * pada rentang tanggal yang diminta
 */
public class KamarPenuhException extends RuntimeException {

    private String tipeKamar;

    public KamarPenuhException(String tipeKamar) {
        super("Semua kamar tipe '" + tipeKamar + "' sudah penuh pada tanggal yang dipilih!");
        this.tipeKamar = tipeKamar;
    }

    public String getTipeKamar() {
        return tipeKamar;
    }
}
