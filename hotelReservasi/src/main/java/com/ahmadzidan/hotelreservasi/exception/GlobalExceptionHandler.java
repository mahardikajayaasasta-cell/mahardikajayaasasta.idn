package com.ahmadzidan.hotelreservasi.exception;

import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.ControllerAdvice;
import org.springframework.web.bind.annotation.ExceptionHandler;

/**
 * Global Exception Handler
 * Menangkap semua exception yang terjadi di aplikasi
 * dan mengarahkan ke halaman error yang user-friendly
 */
@ControllerAdvice
public class GlobalExceptionHandler {

    // Tangani error: Kamar Penuh
    @ExceptionHandler(KamarPenuhException.class)
    public String handleKamarPenuh(KamarPenuhException ex, Model model) {
        model.addAttribute("errorTitle", "Kamar Penuh!");
        model.addAttribute("errorMessage", ex.getMessage());
        model.addAttribute("errorIcon", "🏨");
        return "error";
    }

    // Tangani error: Reservasi Tidak Ditemukan
    @ExceptionHandler(ReservasiNotFoundException.class)
    public String handleReservasiNotFound(ReservasiNotFoundException ex, Model model) {
        model.addAttribute("errorTitle", "Data Tidak Ditemukan");
        model.addAttribute("errorMessage", ex.getMessage());
        model.addAttribute("errorIcon", "🔍");
        return "error";
    }

    // Tangani error umum / tak terduga (termasuk kegagalan koneksi database)
    @ExceptionHandler(Exception.class)
    public String handleGeneralError(Exception ex, Model model) {
        model.addAttribute("errorTitle", "Terjadi Kesalahan");
        model.addAttribute("errorMessage", "Maaf, terjadi kesalahan pada sistem: " + ex.getMessage());
        model.addAttribute("errorIcon", "⚠️");
        return "error";
    }
}
