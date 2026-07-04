package com.dimasmulyo.hotelreservasi;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;

/**
 * Aplikasi Reservasi Hotel
 * Tugas Pemrograman 2 - Dimas Mulyo
 * Framework: Spring Boot + Thymeleaf + MySQL (JPA/JDBC)
 */
@SpringBootApplication
public class HotelReservasiApplication {

    public static void main(String[] args) {
        SpringApplication.run(HotelReservasiApplication.class, args);
        System.out.println("===========================================");
        System.out.println("  Aplikasi Reservasi Hotel Berhasil Jalan!");
        System.out.println("  Akses: http://localhost:8082");
        System.out.println("===========================================");
    }
}
