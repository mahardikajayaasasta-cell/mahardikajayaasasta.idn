package com.ahmadzidan.hotelreservasi.controller;

import com.ahmadzidan.hotelreservasi.model.Reservasi;
import com.ahmadzidan.hotelreservasi.service.ReservasiService;
import jakarta.validation.Valid;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.validation.BindingResult;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import java.util.List;

/**
 * Controller (MVC Pattern)
 * Menangani semua request HTTP dari browser pengguna
 * dan menghubungkan ke Service Layer serta Thymeleaf View
 */
@Controller
public class ReservasiController {

    @Autowired
    private ReservasiService reservasiService;

    // ============================
    // HALAMAN UTAMA: Daftar Reservasi
    // ============================
    @GetMapping("/")
    public String index(Model model, @RequestParam(required = false) String search) {
        List<Reservasi> daftarReservasi;

        if (search != null && !search.isEmpty()) {
            daftarReservasi = reservasiService.cariByNama(search);
            model.addAttribute("search", search);
        } else {
            daftarReservasi = reservasiService.semuaReservasi();
        }

        model.addAttribute("daftarReservasi", daftarReservasi);
        model.addAttribute("pageTitle", "Daftar Reservasi Hotel");
        return "index";
    }

    // ============================
    // FORM: Tambah Reservasi Baru
    // ============================
    @GetMapping("/reservasi/tambah")
    public String formTambah(Model model) {
        model.addAttribute("reservasi", new Reservasi());
        model.addAttribute("pageTitle", "Form Reservasi Baru");
        model.addAttribute("isEdit", false);
        return "form";
    }

    // ============================
    // PROSES: Simpan Reservasi Baru
    // ============================
    @PostMapping("/reservasi/simpan")
    public String simpanReservasi(@Valid @ModelAttribute("reservasi") Reservasi reservasi,
            BindingResult result,
            Model model,
            RedirectAttributes redirectAttributes) {
        // Jika ada error validasi input, kembali ke form
        if (result.hasErrors()) {
            model.addAttribute("pageTitle", "Form Reservasi Baru");
            model.addAttribute("isEdit", false);
            return "form";
        }

        try {
            reservasiService.buatReservasi(reservasi);
            redirectAttributes.addFlashAttribute("successMessage",
                    "Reservasi untuk tamu '" + reservasi.getNamaTamu() + "' berhasil dibuat!");
        } catch (Exception e) {
            model.addAttribute("errorMessage", e.getMessage());
            model.addAttribute("pageTitle", "Form Reservasi Baru");
            model.addAttribute("isEdit", false);
            return "form";
        }

        return "redirect:/";
    }

    // ============================
    // DETAIL: Lihat Detail Reservasi
    // ============================
    @GetMapping("/reservasi/{id}")
    public String detailReservasi(@PathVariable Long id, Model model) {
        Reservasi reservasi = reservasiService.cariReservasiById(id);
        model.addAttribute("reservasi", reservasi);
        model.addAttribute("pageTitle", "Detail Reservasi #" + id);
        return "detail";
    }

    // ============================
    // FORM: Edit Reservasi
    // ============================
    @GetMapping("/reservasi/edit/{id}")
    public String formEdit(@PathVariable Long id, Model model) {
        Reservasi reservasi = reservasiService.cariReservasiById(id);
        model.addAttribute("reservasi", reservasi);
        model.addAttribute("pageTitle", "Edit Reservasi #" + id);
        model.addAttribute("isEdit", true);
        return "form";
    }

    // ============================
    // PROSES: Update Reservasi
    // ============================
    @PostMapping("/reservasi/update/{id}")
    public String updateReservasi(@PathVariable Long id,
            @Valid @ModelAttribute("reservasi") Reservasi reservasi,
            BindingResult result,
            Model model,
            RedirectAttributes redirectAttributes) {
        if (result.hasErrors()) {
            model.addAttribute("pageTitle", "Edit Reservasi #" + id);
            model.addAttribute("isEdit", true);
            return "form";
        }

        try {
            reservasiService.updateReservasi(id, reservasi);
            redirectAttributes.addFlashAttribute("successMessage", "Reservasi berhasil diperbarui!");
        } catch (Exception e) {
            model.addAttribute("errorMessage", e.getMessage());
            model.addAttribute("pageTitle", "Edit Reservasi #" + id);
            model.addAttribute("isEdit", true);
            return "form";
        }

        return "redirect:/";
    }

    // ============================
    // PROSES: Check-Out Tamu
    // ============================
    @GetMapping("/reservasi/checkout/{id}")
    public String checkOut(@PathVariable Long id, RedirectAttributes redirectAttributes) {
        Reservasi reservasi = reservasiService.checkOut(id);
        redirectAttributes.addFlashAttribute("successMessage",
                "Check-out untuk tamu '" + reservasi.getNamaTamu() + "' berhasil!");
        return "redirect:/";
    }

    // ============================
    // PROSES: Hapus Reservasi
    // ============================
    @GetMapping("/reservasi/hapus/{id}")
    public String hapusReservasi(@PathVariable Long id, RedirectAttributes redirectAttributes) {
        try {
            reservasiService.hapusReservasi(id);
            redirectAttributes.addFlashAttribute("successMessage", "Reservasi berhasil dihapus!");
        } catch (Exception e) {
            redirectAttributes.addFlashAttribute("errorMessage", e.getMessage());
        }
        return "redirect:/";
    }
}
