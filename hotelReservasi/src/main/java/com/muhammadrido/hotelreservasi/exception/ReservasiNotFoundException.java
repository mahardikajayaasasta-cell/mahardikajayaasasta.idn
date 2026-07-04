package com.muhammadrido.hotelreservasi.exception;

/**
 * Custom Exception: ReservasiNotFoundException
 * Dilempar saat data reservasi berdasarkan ID tidak ditemukan di database
 */
public class ReservasiNotFoundException extends RuntimeException {

    public ReservasiNotFoundException(Long id) {
        super("Reservasi dengan ID " + id + " tidak ditemukan!");
    }
}
